# umu-database
Contains spreadsheet of game titles for different stores, their store specific code names, and their umu ids.

Information from the spreadsheet is piped into a umu sql database which is then read by various tools to fetch data.

Typical useage would be for a tool to query the database using the codename and store type, and fetch the correlating game title and umu id, then feed them into the umu launcher.

# Online database search:

https://umu.openwinecomponents.org

# Current available database endpoints (results are in JSON format):

List ALL entries:

https://umu.openwinecomponents.org/umu_api.php

List ALL entries based on STORE:

https://umu.openwinecomponents.org/umu_api.php?store=SOME-STORE

Get TITLE and UMU_ID based on STORE and CODENAME:

https://umu.openwinecomponents.org/umu_api.php?store=SOME-STORE&codename=SOME-CODENAME-OR-APP-ID

Get ALL GAME VALUES based on CODENAME:

https://umu.openwinecomponents.org/umu_api.php?codename=SOME-CODENAME-OR-APP-ID

Get TITLE based on UMU_ID and STORE:

https://umu.openwinecomponents.org/umu_api.php?umu_id=SOME-UMU-ID&store=SOME-STORE-OR-NONE

Get ALL GAME VALUES AND ENTRIES based on UMU_ID:

https://umu.openwinecomponents.org/umu_api.php?umu_id=SOME-UMU-ID

# Rules for adding umu id entries:

1. Determine the codename for the game depending on it's store.

EGS:

For EGS this is the codename used in the process when trying to launch the game (viewed via `ps aux`).

Ex. Borderlands 3:
```
-com.epicgames.launcher://apps/Catnip?action=launch
```
Codename would be `Catnip`

Ex. Fall Guys
```
-com.epicgames.launcher://apps/0a2d9f6403244d12969e11da6713137b?action=launch
```
Codename would be `0a2d9f6403244d12969e11da6713137b`

GOG:

For GOG go to https://www.gogdb.org/, search the game title, find ID correlating to the title and Type 'Game'.

Ex. Y's Origin
```
https://www.gogdb.org/products?search=ys+origin

ID         Name       Type
1422357892 Ys Origin  Game
```
Codename would be `1422357892`


(More store-specific methods will be documented later).

2. If a game is on steam, the UMU ID will be umu-steamid. If it is not, you can make up any value as long as it does not already exist.

Examples for games on steam and other platforms:

Borderlands 3:
```
umu-397540
```

Ys Origin:
```
umu-207350
```

Examples for games not on steam:

Dauntless:
```
umu-dauntless
```

3. You can have duplicate lines for the same game, just as long as the umu ID is the same.

Example:  
```
TITLE                   STORE    CODENAME                              UMU_ID     NOTE (Optional)
Grand Theft Auto V      egs      9d2d0eb64d5c44529cece33fe2a46482      umu-271590
Grand Theft Auto V      none     none                                  umu-271590 rockstar standalone
```

4. Game titles must be correctly capitalized as they may be used in protonfixes to display text output. all other entries should be lower case. Database search queries should be cast to lower case and/or searched case insensitive.  

5. If a game is standalone or does not belong to a major storefront, use 'none' as the store and codename. Protonfixes has several gamefixes directories for different stores. If no store and/or codename is specified it will search instead search the 'umu' gamefixes directory instead of the store directory for the UMU ID.

6. For games not on steam the second part of the ID should have at least one letter but preferably be a phrase thats easily understandable simply so that it's not parsed as a steam id. We perform a check on the second part of the UMU ID to determine if it's numeric or not. If it is, that part is sent as the steam ID to proton. Protonfixes prioritizes UMU_ID envvar, but proton itself uses SteamAppId for some game specific fixes directly.

Ex from proton:

```
           if appid in [
                "1341820", #As Dusk falls
                "280790", #Creativerse
                "306130", #The Elder Scrolls Online
                "24010", #Train Simulator
                "374320", #DARK SOULS III
                "65500", #Aura: Fate of the Ages
                "4000", #Garry's Mod
                "383120", #Empyrion - Galactic Survival
                "2371630", #Sword Art Online: Integral Factor
                ]:
            ret.add("gamedrive")
```

So, if say the game 'As Dusk falls' has both protonfixes and a proton official specific fix like above. It's UMU ID would be umu-1341820 which gets passed to protonfixes, while the second part of that -- 1341820 gets parsed and passed as the SteamAppId/appid, this way it allows both Valve's fixes in their proton script (and their wine code) to work as well as our protonfixes.


7. The ONLY time the same game should have two different UMU IDs is if they are not from a major API-managed store front.

For example, Genshin Impact has a standalone PC version and a "PlayPC" version. Neither of these are a major API managed storefront such as like EGS or Amazon etc. In this case they are treated as individual separate games within the umu protonfixes folder, and thus they need separate IDs in the event that one may ever need different fixes from the other (umu-genshin and umu-genshin-playpc).

