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

// Utility function to get the API method
function get_method() {
    return $_SERVER['REQUEST_METHOD'];
}

// Utility function to get the request data
function get_request_data() {
    return array_merge(empty($_GET) ? array() : $_GET, (array) json_decode(file_get_contents('php://input'), true));
}

// Utility function to send an API response
function send_response($response, $code = 200) {
    http_response_code($code);
    die(json_encode($response));
}

// Connect to the database
// ... (same as before)

// Check if the request method is GET and if the codename and store parameters are set
if (get_method() === 'GET' && isset($_GET['codename'], $_GET['store'])) {
    $codename = $_GET['codename'];
    $store = $_GET['store'];

    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("
        SELECT g.title, gr.ulwgl_id
        FROM gamerelease gr
        INNER JOIN game g ON g.id = gr.ulwgl_id
        WHERE gr.codename = :codename AND gr.store = :store
    ");
    $stmt->execute([':codename' => $codename, ':store' => $store]);
    $results = $stmt->fetchAll();

    // Prepare the response
    $response = [];
    foreach ($results as $result) {
        $response[] = ['title' => $result['title'], 'ulwgl_id' => $result['ulwgl_id']];
    }

    // Send the response
    send_response($response);
} else {
    send_response(['error' => 'Invalid request'], 400);
}
?>

