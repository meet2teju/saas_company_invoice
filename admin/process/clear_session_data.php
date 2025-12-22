<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clear_bank_data']) && $_POST['clear_bank_data'] == 1) {
        if (isset($_SESSION['old_bank_data'])) {
            unset($_SESSION['old_bank_data']);
        }
    }
    
    if (isset($_POST['clear_edit_bank_data']) && $_POST['clear_edit_bank_data'] == 1) {
        if (isset($_SESSION['edit_bank_data'])) {
            unset($_SESSION['edit_bank_data']);
        }
    }
    
    echo json_encode(['status' => 'success']);
}
?>