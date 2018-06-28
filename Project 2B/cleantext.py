"""Clean comment text for easier parsing."""

from __future__ import print_function

import bz2
import json
import re
import string
import argparse
import sys


__author__ = ""
__email__ = ""

# Some useful data.
_CONTRACTIONS = {
    "tis": "'tis",
    "aint": "ain't",
    "amnt": "amn't",
    "arent": "aren't",
    "cant": "can't",
    "couldve": "could've",
    "couldnt": "couldn't",
    "didnt": "didn't",
    "doesnt": "doesn't",
    "dont": "don't",
    "hadnt": "hadn't",
    "hasnt": "hasn't",
    "havent": "haven't",
    "hed": "he'd",
    "hell": "he'll",
    "hes": "he's",
    "howd": "how'd",
    "howll": "how'll",
    "hows": "how's",
    "id": "i'd",
    "ill": "i'll",
    "im": "i'm",
    "ive": "i've",
    "isnt": "isn't",
    "itd": "it'd",
    "itll": "it'll",
    "its": "it's",
    "mightnt": "mightn't",
    "mightve": "might've",
    "mustnt": "mustn't",
    "mustve": "must've",
    "neednt": "needn't",
    "oclock": "o'clock",
    "ol": "'ol",
    "oughtnt": "oughtn't",
    "shant": "shan't",
    "shed": "she'd",
    "shell": "she'll",
    "shes": "she's",
    "shouldve": "should've",
    "shouldnt": "shouldn't",
    "somebodys": "somebody's",
    "someones": "someone's",
    "somethings": "something's",
    "thatll": "that'll",
    "thats": "that's",
    "thatd": "that'd",
    "thered": "there'd",
    "therere": "there're",
    "theres": "there's",
    "theyd": "they'd",
    "theyll": "they'll",
    "theyre": "they're",
    "theyve": "they've",
    "wasnt": "wasn't",
    "wedve": "wed've",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "whos": "who's",
    "whove": "who've",
    "whyd": "why'd",
    "whyre": "why're",
    "whys": "why's",
    "wont": "won't",
    "wouldve": "would've",
    "wouldnt": "wouldn't",
    "yall": "y'all",
    "youd": "you'd",
    "youll": "you'll",
    "youre": "you're",
    "youve": "you've"
}

# You may need to write regular expressions.

def sanitize(text):
    """Do parse the text in variable "text" according to the spec, and return
    a LIST containing FOUR strings 
    1. The parsed text.
    2. The unigrams
    3. The bigrams
    4. The trigrams
    """

    # YOUR CODE GOES BELOW:

    # Replace new lines and tab characters with a single space.
    # deal with the concat spaces afterward
    replace_text = text.replace("\n", " ")
    replace_text = text.replace(r"\\n", " ")
    replace_text = replace_text.replace("\t", " ")

    #https://stackoverflow.com/questions/16720541/python-string-replace-regular-expression
    # Remove URLs. Replace them with the empty string ''. 
    # the following didn't quite work
    replace_text = re.sub(r'http:\/\/[\w]+[\.\S]+[\/\S]+', "", replace_text)
    replace_text = re.sub(r'https:\/\/[\S]+[\.\S]+[\/\S]+', "", replace_text)
    replace_text = re.sub(r'www.[\S]+[\.\S]+[\/\S]+', "", replace_text)  
    # so I referenced from stackoverflow
    # https://stackoverflow.com/questions/3809401/what-is-a-good-regular-expression-to-match-a-url
    # replace_text = re.sub(r'(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})',"", replace_text)
    
    # Split text on a single space. If there are multiple contiguous spaces
    # you will need to remove empty tokens after doing the split.
    # either to use re or to use string.split() 
    # the \nformer one is slower, we'll see if it needs change
    text_list = replace_text.split()
    text_list = [i for i in text_list if i != " "]

    # print(*text_list, sep='\n')
    #  Separate all external punctuation such as periods, commas, etc. into their own tokens 
    # (a token is a single piece of text with no spaces), but maintain punctuation within words 
    # from here we need to consider the case of unigrams, bigrams and trigrams
    # https://www.dotnetperls.com/punctuation-python
    length = len(text_list)
    i = 0
    while i < length:
        len_sub = len(text_list[i])
        if (len_sub < 1):
            continue
        end = text_list[i][len_sub - 1]
        
        j = 0
        while ((end in string.punctuation) or (end == "”")) and (end != "%") and (len_sub > 1):
            text_list[i] = text_list[i][:-1]
            text_list.insert(i + 1, end)
            len_sub -= 1
            end = text_list[i][len_sub - 1]
            j += 1
        
        length += j
        i += j + 1
        
    # for test purpose
    #print("test,here\n" + str(len(text_list)))
    #print(*text_list,sep='[\n]')

    # Remove all punctuation except punctuation that ends a phrase or sentence. 
    # ending sentences: '.', '!', '?'. 
    # ending phrases: ',', ';', ':'. 
    parsed_text = []
    unigrams = []
    bigrams = []
    trigrams = []

    uni_count = 0
    bi_flag = 0
    tri_flag = 0
    for i in range(len(text_list)):
        if (text_list[i] == '.' or text_list[i] == ',' or text_list[i] == '!' 
            or text_list[i] == '?' or text_list[i] == ':' or text_list[i] == ';'):
                parsed_text.append(text_list[i])
                bi_flag = 0
                tri_flag = 0

        else:
            result = re.sub('^[!\"\“#$%&\'()*+,-.\/:;<=>?@[\\]^_`{|}~]+(?<!-)', "", text_list[i])  
            result = re.sub(r'^[-]+', "", result)
            result = re.sub(r'[\”]+', "", result)
            #result = re.sub('[!"#$&\'()*+,-.\/:;<=>?@[\\]^_`{|}~]+(?<!-)$', "", temp)
            
            if (result == ""):
                continue
            elif (result.lower() in _CONTRACTIONS):
                result = _CONTRACTIONS.get(result.lower())

            parsed_text.append(result)
            unigrams.append(result)

            if (bi_flag == 0):
                bi_flag = 1
            else:
                bigrams.append(unigrams[uni_count-1] + "_" + result)

            if (tri_flag == 0):
                tri_flag = 1
            elif (tri_flag == 1):
                tri_flag = 2
            else:
                trigrams.append(unigrams[uni_count-2] + "_" + unigrams[uni_count-1] + '_' + result)

            uni_count += 1

    # Convert all text to lowercase.  
    # https://stackoverflow.com/questions/1801668/convert-a-python-list-with-strings-all-to-lowercase-or-uppercase
    parsed_text = [element.lower() for element in parsed_text]
    unigrams = [element.lower() for element in unigrams]
    bigrams = [element.lower() for element in bigrams]
    trigrams = [element.lower() for element in trigrams]

    return [parsed_text, unigrams, bigrams, trigrams]


# str=open("input.txt","r").read()
# result = sanitize(str)
# for j in range(4):
#     print(*result[j])
#     print('\n')
#print(*result, sep='\n')

if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.
    if (len(sys.argv) == 2):
        filename = sys.argv[1]
    else:
        raise TypeError("Invalid number of arguments; must be called as: [python3 cleantest.py <filename>]")
    #i = 0
    # bz2 file handler
    if filename.endswith('.bz2'):
        with bz2.open(filename, "rt") as bz_file:
            for line in bz_file:
                #if 0 <= i <= 300:
                    #print('====== example' + str(i) + ' ======' + '\n')
                    data = json.loads(line)
                    #print('======' + '\n' + data["body"] + '\n' + '======' + '\n')
                    result = sanitize(data["body"])
                    for j in range(4):
                        print(*result[j])
                        print('\n')
                    #print(*result, sep='\n')
                    #i += 1
                # elif i < 250:
                #     i += 1
                #     continue
                #else:
                    #break

    # json file handler
    elif filename.endswith('.json'):
        with open(filename, "rt") as json_file:        
            data = json.load(json_file)
            sanitize(data["body"])

