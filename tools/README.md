# ULWGL tools

Here is the set of tools for easier database maintenance and quickly searching of releases across different stores

## gamesdb.py
    
> [!WARNING]  
> Use this only as a hint, always make sure you can confirm that the data is valid

display external releases of games by title from GOG Galaxy's API  
Usage:
```
./gamesdb.py Horizon Zero Dawn
```

## amazon-import.py

print games with Steam releases based on [Nile](https://github.com/imLinguin/nile)'s library.json that are not in the database

Common location of library.json on Linux is `$XDG_CONFIG_HOME/nile/library.json` and in Heroic `$XDG_CONFIG_HOME/heroic/nile_config/nile/library.json`

Make sure to run `nile library sync` before import to ensure latest metadata

## preflight-check.py

makes sure the csv is valid, used in GitHub action on each push
