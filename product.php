<?php
// Retrieve input from POST request
$input = isset($_POST['input']) ? $_POST['input'] : '';

// Perform necessary validation on the input if needed

// Connect to your database (replace these details with your actual database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ems";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Use the input in your query (replace this query with your actual query)
$query = "SELECT p_name FROM tbl_product WHERE p_id = '$input'";
$result = $conn->query($query);

// Process the query result
if ($result) {
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
} else {
    echo "Error executing query: " . $conn->error;
}

// Close the database connection
$conn->close();
?>
