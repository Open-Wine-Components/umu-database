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
