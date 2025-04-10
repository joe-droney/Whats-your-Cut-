<?php
// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 403 Forbidden');
    exit('Access forbidden');
}

// Get the raw POST data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Validate the data
if (!$data || !isset($data['height']) || !isset($data['weight']) || !isset($data['age']) || !isset($data['gender']) || !isset($data['marblingScore'])) {
    header('HTTP/1.0 400 Bad Request');
    exit('Invalid data format');
}

// Format the data for storage
$dataToStore = sprintf(
    "%s,%s,%s,%s,%s,%s\n",
    date('Y-m-d H:i:s'),
    $data['height'],
    $data['weight'],
    $data['age'],
    $data['gender'],
    $data['marblingScore']
);

// Path to the data file
$dataFile = 'user_data_collection.csv';

// Create headers if file doesn't exist
if (!file_exists($dataFile)) {
    $headers = "timestamp,height,weight,age,gender,marbling_score\n";
    file_put_contents($dataFile, $headers, FILE_APPEND | LOCK_EX);
}

// Append the data to the file
if (file_put_contents($dataFile, $dataToStore, FILE_APPEND | LOCK_EX)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
} else {
    header('HTTP/1.0 500 Internal Server Error');
    echo json_encode(['status' => 'error', 'message' => 'Failed to save data']);
}
?>
