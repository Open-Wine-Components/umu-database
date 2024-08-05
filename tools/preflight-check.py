#!/usr/bin/env python3

import sys
import os
import csv

SUPPORTED_STORES = ["amazon", "battlenet", "none", "egs", "ubisoft", "ea", "humble", "itchio", "steam", "gog", "zoomplatform"]

def main():
    file = sys.argv[1]
    filename = os.path.basename(file)
    has_error = False
    with open(file, 'r') as csvfile:
        rows = csv.reader(csvfile)
        header = True
        release_ids = list()
        for i, row in enumerate(rows, 1):
            if header:
                header = False
                continue

            if not row:
                print(f"::error file={filename},line={i}::empty row found")
                has_error = True
                continue

            if len(row) != 6:
                print(f"::error file={filename},line={i}::incorrect number of columns")
                has_error = True
                continue

            title = row[0]
            store = row[1]
            codename = row[2]
            umu_id = row[3]

            if not (title and store and codename and umu_id):
                print(f"::error file={filename},line={i}::At least one of the required fields is missing")
                has_error = True
                continue

            if store not in SUPPORTED_STORES:
                print(f"::error file={filename},line={i}::Invalid store provided '{store}'")
                has_error = True
                continue

            if store == "none" and codename == "none":
                continue
            release_id = f"{store}_{codename}"
            if release_id in release_ids:
                print(f"::error file={filename},line={i}::Duplicate entry found '{title}, {store}, {codename}'")
                has_error = True
                continue
            release_ids.append(release_id)
    
    if has_error:
        exit(1)

if __name__ == "__main__":
    main()
