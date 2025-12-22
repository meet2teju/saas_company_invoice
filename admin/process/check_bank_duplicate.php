<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config
$configFile = '../../config/config.php';
if (!file_exists($configFile)) {
    die(json_encode(['status' => 'error', 'message' => 'Config file not found']));
}

include $configFile;

// Set content type to JSON
header('Content-Type: application/json');

// Check if database connected
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Check if we have POST data
if (!isset($_POST['field']) || !isset($_POST['value'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$field = $_POST['field'];
$value = mysqli_real_escape_string($conn, trim($_POST['value']));
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Get organization ID from POST or session
$currentOrgId = isset($_POST['org_id']) ? intval($_POST['org_id']) : 0;
if ($currentOrgId == 0 && isset($_SESSION['org_id'])) {
    $currentOrgId = $_SESSION['org_id'];
}

// Only allow checking specific fields
if (!in_array($field, ['account_number', 'ifsc_code', 'swift_code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid field']);
    exit;
}

// Check WITH org_id (organization-level uniqueness)
$sql = "SELECT * FROM bank WHERE $field = '$value' AND org_id = '$currentOrgId'";
if ($id > 0) {
    $sql .= " AND id != $id";
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) > 0) {
    // Customize message based on field
    $fieldNames = [
        'account_number' => 'Account number',
        'ifsc_code' => 'IFSC code',
        'swift_code' => 'SWIFT code'
    ];
    $fieldName = isset($fieldNames[$field]) ? $fieldNames[$field] : ucfirst(str_replace('_', ' ', $field));
    
    echo json_encode([
        'status' => 'exists', 
        'message' => $fieldName . ' already exists!'
    ]);
} else {
    echo json_encode(['status' => 'ok']);
}
?>