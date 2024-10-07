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
    $umu_id = isset($_GET['umu_id']) ? $_GET['umu_id'] : null;
    $title = isset($_GET['title']) ? $_GET['title'] : null;

    // Prepare and execute the SQL statement
    $sql = "SELECT g.title, gr.umu_id, g.acronym, gr.codename, gr.store, gr.notes FROM gamerelease gr INNER JOIN game g ON g.id = gr.umu_id";
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

    if ($umu_id !== null) {
        if ($store !== null) {
            $sql .= " AND gr.umu_id = :umu_id";
        } else {
            $sql .= " WHERE gr.umu_id = :umu_id";
        }
        $params[':umu_id'] = $umu_id;
    }

    if ($title !== null) {
        if ($store !== null) {
            $sql .= " AND g.title = :title";
        } else {
            $sql .= " WHERE g.title = :title AND gr.store = 'none'";
        }
        $params[':title'] = $title;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Prepare the response
    $response = [];
    foreach ($results as $result) {
        if ($codename !== null && $store !== null) {
            $response[] = ['title' => $result['title'], 'umu_id' => $result['umu_id']];
        } else if ($umu_id !== null && $store !== null) {
            $response[] = ['title' => $result['title']];
        } else if ($title !== null) {
            $response[] = ['umu_id' => $result['umu_id']];
        } else {
            $response[] = [
                'title' => $result['title'],
                'umu_id' => $result['umu_id'],
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

