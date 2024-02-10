#!/usr/bin/env python3

import sys
import csv

def main():
    file = sys.argv[1]
    with open(file, 'r') as csvfile:
        rows = csv.reader(csvfile)
        header = True
        id_title_map = dict()
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

            # Check if we have the same title for all entries with same ulwgl-id
            # Import ignores the case when adding new entries
            if ulwgl_id in id_title_map and id_title_map[ulwgl_id].lower() != title.lower():
                print("Different title for same ulwgl_id found", id_title_map[ulwgl_id], title, ulwgl_id)
                exit(1)
            else:
                id_title_map.update({ulwgl_id: title})


if __name__ == "__main__":
    main()
