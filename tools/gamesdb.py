#!/usr/bin/env python3

# Query games by title using GOG GALAXY's gamesdb.gog.com API

import sys
import urllib.request
import urllib.parse
import datetime
import json

# https://galaxy-integrations-python-api.readthedocs.io/en/latest/platforms.html
PLATFORM_WHITELIST = set(["steam", "amazon", "battlenet", "origin", "epic", "humble", "itch", "gog"])

def main():
    try:
        title = " ".join(sys.argv[1:])
        if not title:
            raise IndexError()
    except IndexError:
        print("Title required")
        return

    save_title = urllib.parse.urlencode({"title": title})

    request_url = f"https://gamesdb.gog.com/games?{save_title}"

    req = urllib.request.Request(request_url)
    response = urllib.request.urlopen(req)
    data = response.read()
    json_data = json.loads(data)

    for item in json_data["items"]:
        if not PLATFORM_WHITELIST.intersection(set([release["platform_id"] for release in item["releases"]])):
           continue 
        
        if item["first_release_date"]:
            date = datetime.datetime.fromisoformat(item["first_release_date"])
            print(item["title"]["*"], f"({date.strftime('%Y')})")
        else:
            print(item["title"]["*"])

        for release in item["releases"]:
            if release["platform_id"] in PLATFORM_WHITELIST:
                print("\t", release["platform_id"], release["external_id"])

        print()

if __name__ == "__main__":
    main()

