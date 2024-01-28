<?php

include 'dbinfo.php';

// Connect to the database
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME,
        DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Check if form has been submitted
if (isset($_POST["search"])) {
    $stmt = $pdo->prepare("
        SELECT g.title, g.acronym, gr.ulwgl_id, gr.store, gr.codename, gr.notes
        FROM game g
        INNER JOIN gamerelease gr ON g.id = gr.ulwgl_id
        WHERE g.title LIKE :search OR g.acronym LIKE :search OR gr.codename LIKE :search
    ");
    $stmt->execute([":search" => "%".$_POST["search"]."%"]);
    $results = $stmt->fetchAll();
}
?>

<!-- HTML form -->
<!DOCTYPE html>
<html>
<head>
    <title>Game Search</title>
    <style>
      .results {
        border: 1px solid black;
      }
      .results td {
        text-align: left;
        padding: 5px;
      }
      .results th {
        text-align: left;
        padding: 5px;
        border-bottom: 1px solid black;
      }
    </style>
</head>
<body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" name="search" placeholder="Search Game Title..." required>
        <input type="submit" value="Search">
    </form>
<p>Can't find a game? Help build our database here: </p>
<p>Database: <a href="https://github.com/Open-Wine-Components/ULWGL-database/blob/main/ULWGL-database.csv">ULWGL-database.csv</a></p>
<p>Database contribution guidelines: <a href="https://github.com/Open-Wine-Components/ULWGL-database/blob/main/README.md#rules-for-adding-ulwgl-id-entries">README.md#rules-for-adding-ulwgl-id-entries</a></p>
<p>Data from the ULWGL-database.csv is pulled from our github and added hourly.</p>
<?php
if (isset($results)) {
     echo "<h1> Number of results: " . $stmt->rowCount() . "</h1>";
     $counter = 1;
}
?>
<table class="results">
    <tr>
        <th></th>
        <th>Title</th>
        <th>ULWGL ID</th>
        <th>Store</th>
        <th>Codename</th>
        <th>Acronym</th>
        <th>Notes</th>
        <th>Protonfixes Script</th>
    </tr>
    <?php
    // Display the search results
    if (isset($results)) {
        foreach ($results as $result) {
          echo "<tr>";
          echo "<td>" . $counter . "</td>";
          echo "<td>" . htmlspecialchars($result['title']) . "</td>";
          echo "<td>" . htmlspecialchars($result['ulwgl_id']) . "</td>";
          echo "<td>" . htmlspecialchars($result['store']) . "</td>";
          echo "<td>" . htmlspecialchars($result['codename']) . "</td>";
          echo "<td>" . htmlspecialchars($result['acronym']) . "</td>";
          echo "<td>" . htmlspecialchars($result['notes']) . "</td>";

          if (htmlspecialchars($result['store']) == 'none') {
                $result['store'] = 'ulwgl';
          }
          $fileContents = file_get_contents("https://raw.githubusercontent.com/Open-Wine-Components/ULWGL-protonfixes/master/gamefixes-" . htmlspecialchars($result['store']) . "/" . htmlspecialchars($result['ulwgl_id']) . ".py");
          if (!$fileContents) {
            echo "<td>None</td>";
          } else if (strpos($fileContents, 'gamefixes-steam') != false) {
            echo "<td><a href=\"https://github.com/Open-Wine-Components/ULWGL-protonfixes/blob/master/" . str_replace('../', '', $fileContents) . "\">" . htmlspecialchars($result['ulwgl_id']) . "</a></td>";
          } else {
            echo "<td><a href=\"https://github.com/Open-Wine-Components/ULWGL-protonfixes/blob/master/gamefixes-" . htmlspecialchars($result['store']) . "/" . htmlspecialchars($result['ulwgl_id']) . ".py\">" . htmlspecialchars($result['ulwgl_id']) . "</a></td>";
          }
          echo "</tr>";
          $counter++;
      }
    } else {
      echo "No results found";
    }
    ?>
</table>
</body>
</html>
