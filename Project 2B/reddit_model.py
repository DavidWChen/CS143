from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext

# IMPORT OTHER MODULES HERE
from pyspark.sql.types import *
from itertools import chain
from pyspark.sql.functions import col, udf
from pyspark.ml.feature import CountVectorizer
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator
import cleantext
import pyspark.sql.functions as F


# https://stackoverflow.com/questions/37284077/combine-pyspark-dataframe-arraytype-fields-into-single-arraytype-field
def sanitize_and_concat(line):
    result = cleantext.sanitize(line) 
    re = result[1:]
    re = list(chain(*re))

    return re
#For Task 6B
def check_pos(num):
    if int(num) == 1:
        return 1
    else:
        return 0
def check_neg(num):
    if int(num) == -1:
        return 1
    else:
        return 0
#For Task 9
def threshold_check_pos(num):
    if float(num[1]) > 0.2:
        #print(float(num[1]))
        return 1
    else:
        return 0
def threshold_check_neg(num):
    if float(num[1]) > 0.25:
        return 1
    else:
        return 0

def main(context):
    """Main function takes a Spark SQL context."""
    # YOUR CODE HERE

    # Task 1: 
    # Create Parquet
    comments = context.read.json("comments-minimal.json.bz2")
    submissions = context.read.json("submissions.json.bz2")
    # https://spark.apache.org/docs/latest/sql-programming-guide.html 
    labels = context.read.load("./labeled_data.csv",
                    format="csv", sep=",", inferSchema="true", header="true")
    
    # Task 4 && 5
    # http://changhsinlee.com/pyspark-udf/
    cleanpy_udf_int = udf(lambda z: sanitize_and_concat(z), ArrayType(StringType())) 

    # # Task 2:
    # # Only have data associated with the labeled data.
    # http://spark.apache.org/docs/2.1.0/api/python/pyspark.sql.html
    df = labels.join(comments, labels.Input_id == comments.id).select(labels.Input_id, labels.labeldem, 
        labels.labelgop, labels.labeldjt, cleanpy_udf_int(comments.body).alias('parsed_result'))

    # Task 3: not needed

    # # Task 6A:
    # Only use tokens that appear more than 5 times across the entire dataset (the minDf parameter).
    cv = CountVectorizer(inputCol="parsed_result", outputCol="features", minDF=5.0)
    model = cv.fit(df)
    df = model.transform(df)
    df.show(truncate=False, n=2)

    # Task 6B:
    check_pos_udf = udf(lambda z: check_pos(z), IntegerType()) 
    check_neg_udf = udf(lambda z: check_neg(z), IntegerType())
    # https://piazza.com/class/jfblbd7hhcs5ac?cid=606
    posdf = df.withColumn('label', check_pos_udf(df.labeldjt))
    negdf = df.withColumn('label', check_neg_udf(df.labeldjt))


    # # Task 7
    pos = posdf.select(posdf['label'], posdf['features'])
    neg = negdf.select(negdf['label'], negdf['features'])
    poslr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
    neglr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
    posEvaluator = BinaryClassificationEvaluator()
    negEvaluator = BinaryClassificationEvaluator()
    posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
    negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
    posCrossval = CrossValidator(
        estimator=poslr,
        evaluator=posEvaluator,
        estimatorParamMaps=posParamGrid,
        numFolds=5)
    negCrossval = CrossValidator(
        estimator=neglr,
        evaluator=negEvaluator,
        estimatorParamMaps=negParamGrid,
        numFolds=5)
    posTrain, posTest = pos.randomSplit([0.5, 0.5])
    negTrain, negTest = neg.randomSplit([0.5, 0.5])
    print("Training positive classifier...")
    posModel = posCrossval.fit(posTrain)
    print("Training negative classifier...")
    negModel = negCrossval.fit(negTrain)
    posModel.save("www/pos.model")
    negModel.save("www/neg.model")

    # Task 8, 9.4, 9.5
    df = comments.join(submissions, submissions.id == comments.link_id.substr(4,9)).select(
        comments.id, comments.created_utc,submissions.title, comments.author_flair_text, comments.body)
    df = df.filter("body not like '%/s'")
    df = df.filter("body not like '&gt%'")

    # Task 9.6
    df = df.select(df.id, df.created_utc.alias('timestamp'),df.title, df.author_flair_text.alias('state'), cleanpy_udf_int(df.body).alias('parsed_result'))
    df = model.transform(df)

    # #Task 9
    posModel = CrossValidatorModel.load("www/pos.model")
    negModel = CrossValidatorModel.load("www/neg.model")
    posResult = posModel.transform(df)
    negResult = negModel.transform(df)
    threshold_check_pos_udf = udf(lambda z: threshold_check_pos(z), IntegerType()) 
    threshold_check_neg_udf = udf(lambda z: threshold_check_neg(z), IntegerType())

    # # https://piazza.com/class/jfblbd7hhcs5ac?cid=652
    posResult = posResult.withColumn('pos', threshold_check_pos_udf(posResult.probability))
    negResult = negResult.withColumn('neg', threshold_check_neg_udf(negResult.probability))


    df1 = posResult.select(posResult.id, posResult.title, posResult.timestamp, posResult.state, posResult.pos)
    df2 = negResult.select(negResult.id, negResult.title, negResult.timestamp, negResult.state, negResult.neg)
    df1.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("posResult.csv")
    df2.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("negResult.csv")
    df = posResult.select("*")

    # Task 10
    # Compute the percentage of comments that were positive and the percentage
    # of comments that were negative across all submissions/posts.

    neg_df = negResult.groupBy("title").agg(F.mean("neg").alias("percentage"))
    pos_df = posResult.groupBy("title").agg(F.mean("pos").alias("percentage"))


    df = neg_df.join(pos_df, neg_df.title == pos_df.title).select(neg_df.title, neg_df.percentage.alias("neg_percentage"), pos_df.percentage.alias("pos_percentage"))
    df.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("percent_sub.csv")
    df.write.parquet("percent_sub.parquet")
 

    # # Compute the percentage of comments that were positive and the percentage
    # # of comments that were negative across all days.

    neg_df = negResult.groupBy(F.from_unixtime("timestamp", "y-MM-dd").alias("days")).agg(F.mean("neg").alias("percentage"))

    pos_df = posResult.groupBy(F.from_unixtime("timestamp", "y-MM-dd").alias("days")).agg(F.mean("pos").alias("percentage"))

    df = neg_df.join(pos_df, neg_df.days == pos_df.days).select(neg_df.days.alias("date"), neg_df.percentage.alias("neg_percentage"), pos_df.percentage.alias("pos_percentage"))
    df.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("percent_days.csv")
    df.write.parquet("percent_days.parquet")

    # Compute the percentage of comments that were positive and the percentage
    # of comments that were negative across all states. 

    neg_df = negResult.filter("""state IN ('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
    'Colorado', 'Connecticut', 'Delaware', 'District of Columbia',
    'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana',
    'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland',
    'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
    'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey',
    'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio',
    'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina',
    'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia',
    'Washington', 'West Virginia', 'Wisconsin', 'Wyoming')""")
    neg_df = neg_df.groupBy("state").agg(F.mean("neg").alias("percentage"))

    pos_df = posResult.filter("""state IN ('Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California',
    'Colorado', 'Connecticut', 'Delaware', 'District of Columbia',
    'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana',
    'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland',
    'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri',
    'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey',
    'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio',
    'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina',
    'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia',
    'Washington', 'West Virginia', 'Wisconsin', 'Wyoming')""")
    pos_df = pos_df.groupBy("state").agg(F.mean("pos").alias("percentage"))

    df = neg_df.join(pos_df, neg_df.state == pos_df.state).select(neg_df.state, neg_df.percentage.alias("neg_percentage"), pos_df.percentage.alias("pos_percentage"))
    df.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("percent_state.csv")
    df.write.parquet("percent_state.parquet")

    # # Compute the percentage of comments that were positive and the percentage of
    # # comments that were negative by comment and story score, independently. You will
    # # want to be careful about quotes. Check out the quoteAll option.

    neg_df1 = negResult.join(comments, negResult.id == comments.id).select(negResult.neg, negResult.title, comments.score.alias("comment_score"))
    neg_df1 = neg_df1.join(submissions, neg_df1.title == submissions.title).select(neg_df1.neg, neg_df1.comment_score, submissions.score.alias("submission_score"))    
    pos_df1 = posResult.join(comments, posResult.id == comments.id).select(posResult.pos, posResult.title, comments.score.alias("comment_score"))
    pos_df1 = pos_df1.join(submissions, pos_df1.title == submissions.title).select(pos_df1.pos, pos_df1.comment_score, submissions.score.alias("submission_score"))

    neg_df = neg_df1.groupBy("comment_score").agg(F.mean("neg").alias("percentage"))

    pos_df = pos_df1.groupBy("comment_score").agg(F.mean("pos").alias("percentage"))

    df = neg_df.join(pos_df, neg_df.comment_score == pos_df.comment_score).select(neg_df.comment_score, neg_df.percentage.alias("neg_percentage"), pos_df.percentage.alias("pos_percentage"))
    df.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("percent_comment_score.csv")
    df.write.parquet("percent_comment_score.parquet")


    neg_df = neg_df1.groupBy("submission_score").agg(F.mean("neg").alias("percentage"))

    pos_df = pos_df1.groupBy("submission_score").agg(F.mean("pos").alias("percentage"))

    df = neg_df.join(pos_df, neg_df.submission_score == pos_df.submission_score).select(neg_df.submission_score, neg_df.percentage.alias("neg_percentage"), pos_df.percentage.alias("pos_percentage"))
    df.repartition(1).write.format("com.databricks.spark.csv").option("header", "true").option("quoteAll", "true").save("percent_submission_score.csv")
    df.write.parquet("percent_submission_score.parquet")



if __name__ == "__main__":
    conf = SparkConf().setAppName("CS143 Project 2B")
    conf = conf.setMaster("local[*]")
    sc   = SparkContext(conf=conf)
    sqlContext = SQLContext(sc)
    sc.addPyFile("cleantext.py")
    main(sqlContext)
