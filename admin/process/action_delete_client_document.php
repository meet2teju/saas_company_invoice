<?php
include '../../config/config.php'; // Adjust path if needed

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Fetch the document file name
    $query = "SELECT document FROM client_document WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $file = '../uploads/' . $row['document'];

        // Delete file from the server
        if (file_exists($file)) {
            unlink($file);
        }

        // Delete record from database
        mysqli_query($conn, "DELETE FROM client_document WHERE id = $id");

        echo 'success';
    } else {
        echo 'not_found';
    }
} else {
    echo 'invalid';
}
?>
