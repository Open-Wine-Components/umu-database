import pandas as pd
from mysql.connector import connect, Error
from ulwgl_import_credentials import DB_HOST, DB_NAME, DB_USER, DB_PASSWORD

try:
    connection = connect(host=DB_HOST,
                         database=DB_NAME,
                         user=DB_USER,
                         password=DB_PASSWORD)
    if connection.is_connected():
        db_Info = connection.get_server_info()
        print("Connected to MySQL Server version ", db_Info)
except Error as e:
    print("Error while connecting to MySQL", e)

url = "https://raw.githubusercontent.com/Open-Wine-components/ULWGL-database/main/ULWGL-database.csv"
df = pd.read_csv(url)
df2 = df.astype(object).where(pd.notnull(df), None)


cursor = connection.cursor()
for index, row in df2.iterrows():

    # Check if 'title' exists in 'game'
    cursor.execute("SELECT * FROM game WHERE id=%s", (row['ULWGL_ID'],))
    result = cursor.fetchone()
    if result:
        # Check if 'ulwgl_id', 'codename', and 'store' exist in 'gamerelease'
        cursor.execute("SELECT * FROM gamerelease WHERE ulwgl_id=%s AND codename=%s AND store=%s", (row['ULWGL_ID'], row['CODENAME'], row['STORE']))
        result = cursor.fetchone()
        if not result:
            # Add 'ulwgl_id', 'codename', 'store', and 'notes' to 'gamerelease'
            sql_insert_query = """ INSERT INTO gamerelease (ulwgl_id,codename,store,notes) VALUES (%s,%s,%s,%s)"""
            record_to_insert = (row['ULWGL_ID'], row['CODENAME'], row['STORE'], row['NOTE (Optional)'])
            #print(sql_insert_query)
            print(record_to_insert)
            cursor.execute(sql_insert_query, record_to_insert)
    else:
        # Add 'title' and 'acronym' to 'game', only add 'acronym' if not empty.
        sql_insert_query = """INSERT INTO game (id, title, acronym) VALUES (%s, %s, %s)"""
        record_to_insert = (row['ULWGL_ID'], row['TITLE'], row['COMMON ACRONYM (Optional)'])
        #print(sql_insert_query)
        print(record_to_insert)
        cursor.execute(sql_insert_query, record_to_insert)

        # Add 'ulwgl_id', 'codename', 'store', and 'notes' to 'gamerelease'. Only add 'notes' if not empty.
        sql_insert_query = """INSERT INTO gamerelease (ulwgl_id, codename, store, notes) VALUES (%s, %s, %s, %s)"""
        record_to_insert = (row['ULWGL_ID'], row['CODENAME'], row['STORE'], row['NOTE (Optional)'])
        #print(sql_insert_query)
        print(record_to_insert)
        cursor.execute(sql_insert_query, record_to_insert)

connection.commit()

cursor.close()
connection.close()
print("MySQL connection is closed")
