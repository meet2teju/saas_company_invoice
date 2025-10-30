<?php
include '../../config/config.php';

if (!empty($_POST['doc_id']) && isset($_FILES['new_file'])) {
    $docId = intval($_POST['doc_id']);
    $oldName = $_POST['old_name'];

    $uploadDir = '../../uploads/';
    $newName = time() . '_' . basename($_FILES['new_file']['name']);
    $targetPath = $uploadDir . $newName;

    // Delete old file
    if (file_exists($uploadDir . $oldName)) {
        unlink($uploadDir . $oldName);
    }

    if (move_uploaded_file($_FILES['new_file']['tmp_name'], $targetPath)) {
        $update = mysqli_query($conn, "UPDATE expense_document SET document = '$newName' WHERE id = $docId");
        if ($update) {
            echo json_encode(['status' => 'success', 'new_name' => $newName]);
            exit;
        }
    }
}

echo json_encode(['status' => 'error']);
