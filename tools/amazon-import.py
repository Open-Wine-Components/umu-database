#!/usr/bin/env python3

import sys
import json
import csv

USAGE = "./amazon-library.py library.json database.csv"

def main():
    try:
        library = sys.argv[1]
    except IndexError:
        print("Please provide library file")
        print(USAGE)
        return

    try:
        database = sys.argv[2]
    except IndexError:
        print("Please provide database.csv location")
        print(USAGE)
        return

    game_ids = list()
    with open(database, 'r', newline='') as csvfile:
        reader = csv.reader(csvfile, delimiter=',')
        header = True
        for row in reader:
            if header:
                header = False
                continue
            store = row[1]
            codename = row[2]
            if store == "amazon":
                game_ids.append(codename)

    file = open(library, 'r')
    library_data = json.load(file)
    file.close()
    
    
    for game in library_data:
        try:
            steam = game['product']['productDetail']['details']['websites']['steam']
        except KeyError:
            continue
        if game['product']['id'] in game_ids:
            continue
        parts = steam.split('/')
        steamid = parts[4]
        title = game['product']['title']
        if ',' in title:
            title = '"' + title + '"'
        print(f"{title},amazon,{game['product']['id']},ulwgl-{steamid},,")

if __name__=="__main__":
    main()
