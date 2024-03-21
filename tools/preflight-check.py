#!/usr/bin/env python3

import sys
import csv

SUPPORTED_STORES = ["amazon", "battlenet", "none", "egs", "ubisoft", "ea", "humble", "itchio", "steam", "gog", "zoomplatform"]

def main():
    file = sys.argv[1]
    with open(file, 'r') as csvfile:
        rows = csv.reader(csvfile)
        header = True
        release_ids = list()
        for i, row in enumerate(rows, 1):
            if header:
                header = False
                continue

            if not row:
                print("Empty row found:", i)
                exit(1)

            title = row[0]
            store = row[1]
            codename = row[2]
            umu_id = row[3]

            if not (title and store and codename and umu_id):
                print("At least one of the required fields is missing, in row:", i)
                exit(1)

            if store not in SUPPORTED_STORES:
                print("Invalid store provided", store, "in row", i)
                exit(1)

            if store == "none" and codename == "none":
                continue
            release_id = f"{store}_{codename}"
            if release_id in release_ids:
                print("Duplicate entry found", title, store, codename, "in row", i)
                exit(1)
            release_ids.append(release_id)


if __name__ == "__main__":
    main()
