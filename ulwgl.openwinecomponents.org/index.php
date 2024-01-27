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
</head>
<body>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" name="search" placeholder="Search Game Title..." required>
        <input type="submit" value="Search">
    </form>

    <?php
    // Display the search results
if (isset($results)) {
     echo "<h1> Number of results: " . $stmt->rowCount() . "</h1>";
     $counter = 1;
     foreach ($results as $result) {
        echo "-----------------------------";
        echo "<div>";
        echo "<h2>Result: " . $counter .  "</h2>";
        echo "<p> Title: " . htmlspecialchars($result['title']) . "</p>";
        echo "<p>ULWGL ID: " . htmlspecialchars($result['ulwgl_id']) . "</p>";
        echo "<p>Store: " . htmlspecialchars($result['store']) . "</p>";
        echo "<p>Codename: " . htmlspecialchars($result['codename']) . "</p>";
        echo "<p>Common Acronym: " . htmlspecialchars($result['acronym']) . "</p>";
        echo "<p>Notes: " . htmlspecialchars($result['notes']) . "</p>";
        echo "</div>";
        $counter++;
    }
} else {
    echo "No results found";
}
    ?>
</body>
</html>
