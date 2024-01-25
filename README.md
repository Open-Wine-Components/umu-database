# ULWGL-database
Contains spreadsheet of game titles for different stores, their store specific code names, and their ULWGL ids.

Information from the spreadsheet is piped into a ULWGL sql database which is then read by various tools to fetch data.

Typical useage would be for a tool to query the database using the codename and store type, and fetch the correlating game title and ulwgl id, then feed them into the ULWGL launcher.

# Rules for adding ulwgl id entries:

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

2. If a game is on steam, the ULWGL ID will be ulwgl-steamid. If it is not, you can make up any value as long as it does not already exist.

Examples for games on steam and other platforms:

Borderlands 3:
```
ulwgl-397540
```

Ys Origin:
```
ulwgl-207350
```

Examples for games not on steam:

Dauntless:
```
ulwgl-dauntless
```

3. You can have duplicate lines for the same game, just as long as the ulwgl ID is the same.

Example:  
```
TITLE                   STORE    CODENAME                              ULWGL_ID     NOTE (Optional)
Grand Theft Auto V      egs      9d2d0eb64d5c44529cece33fe2a46482      ulwgl-271590
Grand Theft Auto V      none     none                                  ulwgl-271590 rockstar standalone
```

4. Game titles must be correctly capitalized as they may be used in protonfixes to display text output. all other entries should be lower case. Database search queries should be cast to lower case and/or searched case insensitive.  

5. If a game is standalone or does not belong to a major storefront, use 'none' as the store and codename. Protonfixes has several gamefixes directories for different stores. If no store and/or codename is specified it will search instead search the 'ULWGL' gamefixes directory instead of the store directory for the ULWGL ID.

6. For games not on steam the second part of the ID should have at least one letter but preferably be a phrase thats easily understandable simply so that it's not parsed as a steam id. We perform a check on the second part of the ULWGL ID to determine if it's numeric or not. If it is, that part is sent as the steam ID to proton. Protonfixes prioritizes ULWGL_ID envvar, but proton itself uses SteamAppId for some game specific fixes directly.

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

So, if say the game 'As Dusk falls' has both protonfixes and a proton official specific fix like above. It's ULWGL ID would be ulwgl-1341820 which gets passed to protonfixes, while the second part of that -- 1341820 gets parsed and passed as the SteamAppId/appid, this way it allows both Valve's fixes in their proton script (and their wine code) to work as well as our protonfixes.


7. The ONLY time the same game should have two different ULWGL IDs is if they are not from a major API-managed store front.

For example, Genshin Impact has a standalone PC version and a "PlayPC" version. Neither of these are a major API managed storefront such as like EGS or Amazon etc. In this case they are treated as individual separate games within the ULWGL protonfixes folder, and thus they need separate IDs in the event that one may ever need different fixes from the other (ulwgl-genshin and ulwgl-genshin-playpc).

