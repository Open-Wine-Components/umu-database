# umu database

This repository contains the data and tools needed to provide seamless protonfixes integration for games from different stores.
Historically, [protonfixes](https://github.com/Open-Wine-Components/umu-protonfixes) have targeted Steam games only but [umu](https://github.com/Open-Wine-Components/umu-launcher) makes it possible to use Proton for all existing Windows games.

We collect data for games that do require fixes for every store they are available on. This data is stored in a spreadsheet which is
regularly imported in a database accessible online at https://umu.openwinecomponents.org

Game launchers such as Lutris and Heroic can then query the database to match a given protonfix with games of different stores. Those launchers can provide a store name and the internal codename on that store to get the matching umu ID and game title.

This database is by no means a complete database of every game released on Windows. We focus on games that requires fixes in Proton.
Games that run out of the box have no need be added to the database. If you want a more extensive database of games you can use the Lutris API.

## Current available database endpoints

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

## Rules for adding umu ID entries:

1\. Determine the TITLE of the game.

What is the game title?

-   Game titles must be correctly capitalized as they may be used in protonfixes to display text output. All other entries should be lowercase. Database search queries should be lowercased and/or searched case-insensitive.

2\. Determine the STORE for the game.

What store does the game come from? GOG? Epic (egs)? Battlenet? Amazon?

-   Available storefronts that umu can parse are:

*   amazon
*   battlenet
*   ea
*   egs
*   gog
*   humble
*   itchio
*   steam
*   ubisoft
*   umu
*   zoomplatform

-   If a game is standalone or does not belong to a major storefront, use 'none' as the store.

3\. Determine the CODENAME for the game depending on it's store.

-   For EGS use the 'App Name' value for the game under the Builds section:

    https://egdata.app

    Ex. Borderlands 3:

    `App Name`    `Catnip`

-   In the event that the 'App Name' cannot be found under the Builds section, use the `appId` value. 

    https://egdata.app

    Ex. The Last of Us™ Part I

    https://egdata.app/offers/0f0fe55f8a464f3f992fa31c0e2810d7

    From the game's offer page, navigate to the correct game under the Items section:

    https://egdata.app/offers/0f0fe55f8a464f3f992fa31c0e2810d7/items

    Finally, from the game's item page and using your web browser, view the page source for the `appId`.

    https://egdata.app/items/e9c47d47c2ac44f3a032e9d645096535

    `7e988ba04889404197fdf06c994326ed`

-   For GOG go to https://www.gogdb.org/, search the game title, find ID correlating to the title and Type 'Game'.

    Ex. Y's Origin

    ID         Name       Type
    1422357892 Ys Origin  Game

    Codename would be `1422357892`

-   If a game is standalone or does not belong to a major storefront, use 'none' as the codename.

4\. Determine the UMU_ID for the game, depending on its store:

-   If a game is on Steam, the umu ID will be umu-(Steam ID), even if it is from another store and also on Steam.

    Examples for games on Steam and other platforms:

    Borderlands 3:
    `umu-397540`

    Ys Origin:
    `umu-207350`

-   This is important in order for Steam-specific fixes in Proton to take effect on non-Steam versions of the same game that is also shipped by Steam.

-   If a game is **not** on Steam, but is on another platform, please try to use the ID for the platform.

    GOG 'Product ID' lookup:\
     https://www.gogdb.org

    Epic 'Item ID' lookup (in search results under 'Items' category listings):\
     https://egdata.app

-   If the game does not belong to any storefront, is a standalone version, or you absolutely cannot find a specific product ID, you can make one up:

    Examples for standalone games:

    Genshin Impact (standalone version):
    `umu-genshin`

-   For games not on Steam the second part of the ID should have at least one letter, but preferably be a phrase that's easily understandable simply so that it's not parsed as a Steam ID. We perform a check on the second part of the umu ID to determine if it's numeric or not. If it is, that part is sent as the Steam ID to Proton. Protonfixes prioritizes the UMU_ID environment variable, but Proton itself uses SteamAppId for some game-specific fixes directly. So, if say the game As Dusk Falls has both protonfixes and an official Proton-specific fix. It's umu ID would be umu-1341820, which gets passed to protonfixes, while the second part of that (1341820) gets parsed and passed as the app ID (SteamAppId). This way, it allows both Valve's fixes in their proton script (and their wine code) to work as well as our protonfixes.

    Example from upstream Proton:

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

-   You can have duplicate records for the same game where the UMU_ID for the game may be different across storefronts. The only time the umu ID will be the same is if a Steam version exists.

    Example (no Steam version):

```\
TITLE                   STORE    CODENAME                              UMU_ID                                 NOTE (Optional)
Genshin Impact          egs      41869934302e4b8cafac2d3c0e7c293d      umu-7d690c122fde4c60bed85405f343ad10
Genshin Impact          none     none                                  umu-genshin                            standalone
```

-   The protonfix for umu-7d690c122fde4c60bed85405f343ad10 should be a symbolic link to the protonfix for umu-genshin if they require the same fixes. They -can- be independent -IF- they require different fixes or if it's a new single title and no fix exists

    Example (game that is also sold on Steam):

```\
TITLE                   STORE    CODENAME                              UMU_ID                                 NOTE (Optional)
Red Dead Redemption 2   egs      Heather                               umu-1174180
Red Dead Redemption 2   none     none                                  umu-1174180                            standalone
```

-   The same applies here. Each different non-Steam version protonfix should be a symbolic link to the Steam version by its UMU_ID protonfix unless it requires different fixes.

If no store and/or codename is specified it will search instead search the 'umu' gamefixes directory instead of the store directory for the umu ID.

5\. Optionally include a commonly used acronym for the game:

-   For example "WoW" is an acronym for [_World of Warcraft_](https://worldofwarcraft.blizzard.com/en-us/) (or also [_World of Warships_](https://worldofwarships.com/))

6\. Optionally include a note:

-   For example, [_Genshin Impact_](https://genshin.hoyoverse.com/en/) has two standalone versions, namely one from Hoyo and one from PlayPC. Leave a note stating which one it is.
