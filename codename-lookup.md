GOG:
https://www.gogdb.org 
Notes:
- ID value is the codename
- Only use those marked Type: Game

Epic Games:
-  AppNames can only be obtained when owning the game

Steam:
https://steamdb.info
Notes:
- We don't make umu entries for steam games, but we do make protonfixes. As stated
in the rules, umu IDs are referenced to steam IDs if both a steam and non-steam
version of the game exists, and the non-steam game versions are symlinked to 
the steam version protonfixes,so It's useful to check if a steam version exists.

Amazon:
- Possible for entitlement owners only
- Steam release auto import possible with [amazon-import.py](./tools/amazon-import.py)


For each store you can obtain somewhat accurate results using [gamesdb.py](./tools/gamesdb.py).  

TODO:
Add more sources for Humble, EA, Battlenet, Ubisoft, itch.io
