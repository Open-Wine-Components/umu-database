#!/usr/bin/env python3

import sys
import os
import csv

SUPPORTED_STORES = [
    "amazon", "battlenet", "none", "egs", "ubisoft", "ea",
    "humble", "itchio", "gog", "zoomplatform"
]

EXPECTED_COLS = 7  # TITLE, STORE, CODENAME, UMU_ID, ACRONYM(opt), EXE_STRING(opt), NOTE(opt)

def main():
    if len(sys.argv) < 2:
        print("Usage: validate_csv.py <path-to-csv>")
        exit(2)

    file = sys.argv[1]
    filename = os.path.basename(file)
    has_error = False

    with open(file, "r", newline="") as csvfile:
        rows = csv.reader(csvfile)
        header = True
        release_keys = set()

        for i, row in enumerate(rows, 1):
            if header:
                header = False
                continue

            if not row:
                print(f"::error file={filename},line={i}::empty row found")
                has_error = True
                continue

            if len(row) != EXPECTED_COLS:
                print(
                    f"::error file={filename},line={i}::incorrect number of columns "
                    f"(expected {EXPECTED_COLS}, got {len(row)})"
                )
                has_error = True
                continue

            title = row[0]
            store = row[1]
            codename = row[2]
            umu_id = row[3]

            # Optional columns (present but not required)
            acronym = row[4]
            exe_string = row[5]
            notes = row[6]

            if not (title and store and codename and umu_id):
                print(f"::error file={filename},line={i}::At least one of the required fields is missing")
                has_error = True
                continue

            if store not in SUPPORTED_STORES:
                print(f"::error file={filename},line={i}::Invalid store provided '{store}'")
                has_error = True
                continue

            # Preserve existing behavior: skip "none/none" releases
            if store == "none" and codename == "none":
                continue

            # Duplicates should be per game+store+codename
            release_key = (umu_id, store, codename)
            if release_key in release_keys:
                print(
                    f"::error file={filename},line={i}::Duplicate entry found "
                    f"'{title}, {store}, {codename}, {umu_id}'"
                )
                has_error = True
                continue
            release_keys.add(release_key)

    if has_error:
        exit(1)

if __name__ == "__main__":
    main()
