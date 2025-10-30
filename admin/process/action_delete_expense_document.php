<?php
require '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $docId = intval($_POST['id']);

    // Get file name
    $result = mysqli_query($conn, "SELECT document FROM expense_document WHERE id = $docId");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filePath = '../../uploads/' . $row['document'];

        // Delete from DB
        if (mysqli_query($conn, "DELETE FROM expense_document WHERE id = $docId")) {
            // Delete file from server
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            echo "success";
            exit;
        }
    }
}

echo "error";
