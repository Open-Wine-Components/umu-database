import pandas as pd
from mysql.connector import connect, Error
from umu_import_credentials import DB_HOST, DB_NAME, DB_USER, DB_PASSWORD

CSV_URL = "https://raw.githubusercontent.com/Open-Wine-components/umu-database/main/umu-database.csv"

def main():
    try:
        connection = connect(
            host=DB_HOST,
            database=DB_NAME,
            user=DB_USER,
            password=DB_PASSWORD,
        )
        print("Connected to MySQL Server version", connection.get_server_info())

        df = pd.read_csv(CSV_URL)
        df = df.astype(object).where(pd.notnull(df), None)

        # Build unique game rows by UMU_ID (first occurrence wins)
        games_df = (
            df[["UMU_ID", "TITLE", "COMMON ACRONYM (Optional)"]]
            .drop_duplicates(subset=["UMU_ID"], keep="first")
        )
        game_rows = [
            (r["UMU_ID"], r["TITLE"], r["COMMON ACRONYM (Optional)"])
            for _, r in games_df.iterrows()
        ]

        # Build release rows (all rows)
        # exe_string may be missing from older CSVs; .get() keeps it safe.
        release_rows = [
            (
                r["UMU_ID"],
                r["CODENAME"],
                r["STORE"],
                r.get("EXE_STRING (Optional)"),
                r["NOTE (Optional)"],
            )
            for _, r in df.iterrows()
        ]

        cursor = connection.cursor()

        try:
            connection.start_transaction()

            # Drop & recreate tables
            cursor.execute("SET FOREIGN_KEY_CHECKS=0;")
            cursor.execute("DROP TABLE IF EXISTS gamerelease;")
            cursor.execute("DROP TABLE IF EXISTS game;")
            cursor.execute("SET FOREIGN_KEY_CHECKS=1;")

            cursor.execute("""
                CREATE TABLE game (
                    id VARCHAR(255) PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    acronym VARCHAR(255)
                );
            """)

            cursor.execute("""
                CREATE TABLE gamerelease (
                    umu_id VARCHAR(255),
                    codename VARCHAR(255),
                    store VARCHAR(255),
                    exe_string VARCHAR(255),
                    notes TEXT,
                    CONSTRAINT fk_gamerelease_game
                        FOREIGN KEY (umu_id) REFERENCES game(id)
                );
            """)

            # Insert data
            cursor.executemany(
                "INSERT INTO game (id, title, acronym) VALUES (%s, %s, %s)",
                game_rows
            )

            cursor.executemany(
                """
                INSERT INTO gamerelease (umu_id, codename, store, exe_string, notes)
                VALUES (%s, %s, %s, %s, %s)
                """,
                release_rows
            )

            connection.commit()
            print(f"Rebuilt DB. Imported {len(game_rows)} games and {len(release_rows)} releases.")

        except Exception as e:
            connection.rollback()
            raise

        cursor.close()
        connection.close()
        print("MySQL connection is closed")

    except Error as e:
        print("Error while connecting to MySQL:", e)
    except Exception as e:
        print("Import failed:", e)

if __name__ == "__main__":
    main()
