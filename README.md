# ULWGL-database
Contains spreadsheet of game titles for different stores, their store specific code names, and their ULWGL ids.

Information from the spreadsheet is piped into a ULWGL sql database which is then read by various tools to fetch data.

Typical useage would be for a tool to query the database using the codename and store type, and fetch the correlating game title and ulwgl id, then feed them into the ULWGL launcher.
