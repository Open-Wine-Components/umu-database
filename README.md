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

Get UMU_ID based on TITLE and STORE:

https://umu.openwinecomponents.org/umu_api.php?title=SOME-GAME-TITLE&STORE=SOME-STORE

Get UMU_ID based on TITLE and no store:

https://umu.openwinecomponents.org/umu_api.php?title=SOME-GAME-TITLE

# Rules for adding umu id entries:

1\. Determine the TITLE of the game.

What is the game title?

* Game titles must be correctly capitalized as they may be used in protonfixes to display text output. all other entries should be lower case. Database search queries should be cast to lower case and/or searched case insensitive.

2\. Determine the STORE for the game.

What store does the game come from? GOG? Epic (egs)? Battlenet? Amazon?

* Available store fronts that UMU can parse are:

- amazon
- battlenet
- ea
- egs
- gog
- humble
- itchio
- steam
- ubisoft
- umu
- zoomplatform

* If a game is standalone or does not belong to a major storefront, use 'none' as the store.

3\. Determine the CODENAME for the game depending on it's store.

* For EGS use the 'Namespace' value for the game found here:

    https://egdata.app

    Ex. Borderlands 3:

    Namespace    catnip

    Codename would be `catnip`

    Ex. Fall Guys

    `-com.epicgames.launcher://apps/0a2d9f6403244d12969e11da6713137b?action=launch`

    Codename would be `0a2d9f6403244d12969e11da6713137b`

* For GOG go to https://www.gogdb.org/, search the game title, find ID correlating to the title and Type 'Game'.

    Ex. Y's Origin

    ID         Name       Type  
    1422357892 Ys Origin  Game

    Codename would be `1422357892`

* If a game is standalone or does not belong to a major storefront, use 'none' as the codename.


4\. Determine the UMU_ID for the game, depending on its store:

* If a game is on steam, the UMU ID will be umu-(Steam ID), even if it is from another store and also on steam.

    Examples for games on steam and other platforms:

    Borderlands 3:  
    `umu-397540`

    Ys Origin:  
    `umu-207350`

* This is important in order for steam-specific game-coded fixes in proton to take effect on non-steam versions of the same game that is also shipped by steam.

* If a game is NOT on steam, but is on another platform, please try to use the ID for the platform.

    GOG 'Product ID' lookup:\
    https://www.gogdb.org

    Epic 'Item ID' lookup (in search results under 'Items' category listings):\
    https://egdata.app

* If the game does not belong to any storefront, is a standalone version, or you absolutely cannot find a specific product ID, you can make one up:

    Examples for standalone games:

    Genshin Impact (standalone version):  
    `umu-genshinimpact`

* For games not on steam the second part of the ID should have at least one letter but preferably be a phrase thats easily understandable simply so that it's not parsed as a steam id. We perform a check on the second part of the UMU ID to determine if it's numeric or not. If it is, that part is sent as the steam ID to proton. Protonfixes prioritizes UMU_ID envvar, but proton itself uses SteamAppId for some game specific fixes directly. So, if say the game 'As Dusk falls' has both protonfixes and a proton official specific fix. It's UMU ID would be umu-1341820 which gets passed to protonfixes, while the second part of that -- 1341820 gets parsed and passed as the SteamAppId/appid, this way it allows both Valve's fixes in their proton script (and their wine code) to work as well as our protonfixes.

    Ex from proton:

```\
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

* You can have duplicate lines for the same game, the UMU_ID for the game may be different for different store fronts. The only time the umu-id will be the same is if a Steam version exists.

    Example (no steam version):

```\
TITLE                   STORE    CODENAME                              UMU_ID                                 NOTE (Optional)
Genshin Impact          egs      41869934302e4b8cafac2d3c0e7c293d      umu-7d690c122fde4c60bed85405f343ad10
Genshin Impact          none     none                                  umu-genshin                            standalone
```

* The protonfix for umu-7d690c122fde4c60bed85405f343ad10 should be a symlink to the protonfix for umu-genshin if they require the same fixes. They -can- be independent -IF- they require different fixes or if it's a new single title and no fix exists

    Example (steam version):

```\
TITLE                   STORE    CODENAME                              UMU_ID                                 NOTE (Optional)
Red Dead Redemption 2   steam    none                                  umu-1174180
Red Dead Redemption 2   egs      Heather                               umu-1174180
Red Dead Redemption 2   none     none                                  umu-1174180                            standalone
```

* The same applies here. Each different non-steam version protonfix -should- be a symlink to the steam version protonfix UNLESS it requires different fixes.

If no store and/or codename is specified it will search instead search the 'umu' gamefixes directory instead of the store directory for the UMU ID.
