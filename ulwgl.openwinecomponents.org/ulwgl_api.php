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
    // Set the content type to JSON
    header('Content-Type: application/json');

    // Set the HTTP status code
    http_response_code($code);

    // Encode the response array as a JSON string and print it
    echo json_encode($response);

    // Terminate the script execution
    exit();
}

// Check if the request method is GET and if the codename and store parameters are set
if (get_method() === 'GET') {
    $codename = isset($_GET['codename']) ? $_GET['codename'] : null;
    $store = isset($_GET['store']) ? $_GET['store'] : null;

    // Prepare and execute the SQL statement
    $sql = "SELECT g.title, gr.ulwgl_id, g.acronym, gr.codename, gr.store, gr.notes FROM gamerelease gr INNER JOIN game g ON g.id = gr.ulwgl_id";
    $params = [];

    if ($codename !== null) {
        $sql .= " WHERE gr.codename = :codename";
        $params[':codename'] = $codename;
    }

    if ($store !== null) {
        if ($codename !== null) {
            $sql .= " AND gr.store = :store";
        } else {
            $sql .= " WHERE gr.store = :store";
        }
        $params[':store'] = $store;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Prepare the response
    $response = [];
    foreach ($results as $result) {
        if ($codename !== null && $store !== null) {
            $response[] = ['title' => $result['title'], 'ulwgl_id' => $result['ulwgl_id']];
        } else {
            $response[] = [
                'title' => $result['title'],
                'ulwgl_id' => $result['ulwgl_id'],
                'acronym' => $result['acronym'],
                'codename' => $result['codename'],
                'store' => $result['store'],
                'notes' => $result['notes']
            ];
        }
    }
    // Send the response
    send_response($response);
} else {
    send_response(['error' => 'Invalid request'], 400);
}
?>
