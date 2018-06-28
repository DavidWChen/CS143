#!/usr/bin/env python

"""Clean comment text for easier parsing."""

from __future__ import print_function

import re
import string
import argparse
import sys
import json
import bz2


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
    "wed": "we'd",
    "wedve": "wed've",
    "well": "we'll",
    "were": "we're",
    "weve": "we've",
    "werent": "weren't",
    "whatd": "what'd",
    "whatll": "what'll",
    "whatre": "what're",
    "whats": "what's",
    "whatve": "what've",
    "whens": "when's",
    "whered": "where'd",
    "wheres": "where's",
    "whereve": "where've",
    "whod": "who'd",
    "whodve": "whod've",
    "wholl": "who'll",
    "whore": "who're",
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
    ending_punctuation = ['.','?','!',',',';',':']
    ending_punctuation = ".,?!;:"
    parsed_text = text
# Replace new lines and tab characters with a single space.
    text = text.replace('\n', ' ')
    text = text.replace('\t', ' ')
# Remove URLs. Replace them with the empty string ''. URLs typically look like [some text](http://www.ucla.edu) in the JSON.
    text = re.sub(r'^https?:\/\/.*[\r\n]*', '', text) # re.MULTILINE
# Split text on a single space. If there are multiple contiguous spaces, you will need to remove empty tokens after doing the split.
    text = ' '.join(text.split())
# Separate all external punctuation such as periods, commas, etc. into their own tokens (a token is a single piece of text with no spaces)
    text_list = re.findall(r"[\w']+|[.,!?;]",text)
# Remove all punctuation (including special characters that are not technically punctuation) except punctuation that ends a phrase or sentence.    for item in text_list:
    for item in text_list: 
        if item in string.punctuation and item not in ending_punctuation:
            del(item)
    text = ' '.join(text_list)
# Convert all text to lowercase.
    text= text.lower()


    text_list = text.split(' ')

    for item in text_list:
        if item in string.punctuation:
            text_list.remove(item)
    unigrams = ' '.join(text_list)

    bigrams_list =[]
    for x in range(0,len(text_list)-1):
        bigrams_list.append(text_list[x] + '_' + text_list[x+1])
    bigrams = ' '.join(bigrams_list)


    trigrams_list =[]
    for x in range(0,len(text_list)-2):
        trigrams_list.append(text_list[x] + '_' + text_list[x+1] + '_' + text_list[x+2])
    trigrams = ' '.join(trigrams_list)

    return [parsed_text, unigrams, bigrams, trigrams]

if __name__ == "__main__":
    # This is the Python main function.
    # You should be able to run
    # python cleantext.py <filename>
    # and this "main" function will open the file,
    # read it line by line, extract the proper value from the JSON,
    # pass to "sanitize" and print the result as a list.

    # YOUR CODE GOES BELOW.
    file = sys.argv[1]
    plist=[]
    ulist=[]
    blist=[]
    tlist=[]

    if file.endswith('.bz2'):
        with bz2.BZ2File(file, 'r') as f:
            for line in f:
                data = json.loads(line)
                sanitized_data = sanitize(data['body'])
                plist.append(sanitized_data[0])
                ulist.append(sanitized_data[1])
                blist.append(sanitized_data[2])
                tlist.append(sanitized_data[3])
            print ([' '.join(ulist), ' '.join(blist), ' '.join(tlist)])

    elif file.endswith('.json'):
        with open(file, 'r') as f:
            for line in f:
                data = json.loads(line)
                sanitized_data = sanitize(data['body'])
                plist.append(sanitized_data[0])
                ulist.append(sanitized_data[1])
                blist.append(sanitized_data[2])
                tlist.append(sanitized_data[3])
            print ([' '.join(plist),' '.join(ulist), ' '.join(blist), ' '.join(tlist)])
    else:
        raise argparse.ArgumentTypeError('argument file of invald type')


    

            






