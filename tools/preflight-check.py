#!/usr/bin/env python3

import sys
import csv

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
            ulwgl_id = row[3]

            if not (title or store or codename or ulwgl_id):
                print("At least one of the required fields is missing, in row:", i)
                exit(1)

            if store == "none" and codename == "none":
                continue
            release_id = f"{store}_{codename}"
            if release_id in release_ids:
                print("Duplicate entry found", title, store, codename)
                exit(1)
            release_ids.append(release_id)


if __name__ == "__main__":
    main()
