<?php
include 'layouts/session.php';
include '../config/config.php';

$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task details with project and client information
$task_query = "
    SELECT pt.*, 
           p.project_name, 
           p.project_code,
           p.client_id,
           c.first_name as client_name,
           c.customer_image as client_image
    FROM project_task pt
    LEFT JOIN project p ON pt.project_id = p.id
    LEFT JOIN client c ON p.client_id = c.id
    WHERE pt.id = $task_id AND pt.is_deleted = 0
";
$task_result = mysqli_query($conn, $task_query);
$task = mysqli_fetch_assoc($task_result);

if (!$task) {
    $_SESSION['message'] = 'Task not found';
    $_SESSION['message_type'] = 'error';
    header('Location: project-tasks.php');
    exit();
}

// Fetch task attachments
$attachments_query = "SELECT * FROM project_task_doc WHERE task_id = $task_id ORDER BY created_at DESC";
$attachments_result = mysqli_query($conn, $attachments_query);
$attachments = [];
while ($row = mysqli_fetch_assoc($attachments_result)) {
    $attachments[] = $row;
}

// Fetch assigned users
$assigned_users_query = "
    SELECT u.id, u.name, u.email, u.profile_img 
    FROM project_users pu 
    JOIN login u ON pu.user_id = u.id 
    WHERE pu.project_id = $task_id AND pu.is_deleted = 0
    ORDER BY u.name ASC
";
$assigned_users_result = mysqli_query($conn, $assigned_users_query);
$assigned_users = [];
while ($row = mysqli_fetch_assoc($assigned_users_result)) {
    $assigned_users[] = $row;
}

// Define status options (same as in your add/edit forms)
$statusOptions = [
    1 => ['name' => 'Pending', 'color' => '#ffc107'],
    2 => ['name' => 'In Progress', 'color' => '#17a2b8'],
    3 => ['name' => 'Completed', 'color' => '#28a745'],
    4 => ['name' => 'On Hold', 'color' => '#6c757d'],
    5 => ['name' => 'Cancelled', 'color' => '#dc3545']
];

// Get status info
$status_id = $task['status_id'] ?? 1;
$status_name = $statusOptions[$status_id]['name'] ?? 'Pending';
$status_color = $statusOptions[$status_id]['color'] ?? '#6c757d';

// Format dates
$start_date = !empty($task['start_date']) ? date('d-m-Y', strtotime($task['start_date'])) : 'Not set';
$end_date = !empty($task['end_date']) ? date('d-m-Y', strtotime($task['end_date'])) : 'Not set';
$created_date = !empty($task['created_at']) ? date('d-m-Y', strtotime($task['created_at'])) : 'Not set';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    
    <!-- Quill.js Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    .timeline-content {
        padding-bottom: 10px;
    }
    .timeline-item:not(:last-child) .timeline-content {
        border-left: 2px solid #e9ecef;
        padding-left: 20px;
        margin-left: -20px;
    }

    /* Attachment Styles */
    .attachment-item {
        transition: all 0.3s ease;
        height: 100%;
    }
    .attachment-item:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .attachment-icon {
        flex-shrink: 0;
    }
    .attachment-details {
        min-width: 0;
    }
    .attachment-name {
        font-size: 14px;
        margin-bottom: 4px;
    }
    .attachment-meta {
        font-size: 12px;
    }
    .attachment-actions .btn {
        font-size: 12px;
        padding: 4px 8px;
    }

    /* Rich Text Content Styles - Preserving existing design */
    .rich-text-content {
        font-family: inherit;
        line-height: 1.6;
        color: #495057;
    }

    .rich-text-content p {
        margin-bottom: 1rem;
    }

    .rich-text-content h1,
    .rich-text-content h2,
    .rich-text-content h3,
    .rich-text-content h4,
    .rich-text-content h5,
    .rich-text-content h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
        color: #343a40;
    }

    .rich-text-content h1 { font-size: 2rem; }
    .rich-text-content h2 { font-size: 1.75rem; }
    .rich-text-content h3 { font-size: 1.5rem; }
    .rich-text-content h4 { font-size: 1.25rem; }
    .rich-text-content h5 { font-size: 1.1rem; }
    .rich-text-content h6 { font-size: 1rem; }

    .rich-text-content ul,
    .rich-text-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }

    .rich-text-content li {
        margin-bottom: 0.5rem;
    }

    .rich-text-content blockquote {
        border-left: 4px solid #0d6efd;
        padding-left: 1rem;
        margin-left: 0;
        margin-right: 0;
        margin-bottom: 1rem;
        font-style: italic;
        color: #6c757d;
    }

    .rich-text-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
        color: #e83e8c;
    }

    .rich-text-content pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        overflow-x: auto;
        margin-bottom: 1rem;
    }

    .rich-text-content pre code {
        background: none;
        padding: 0;
        color: inherit;
    }

    .rich-text-content table {
        width: 100%;
        margin-bottom: 1rem;
        border-collapse: collapse;
    }

    .rich-text-content table th,
    .rich-text-content table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        text-align: left;
    }

    .rich-text-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .rich-text-content img {
        max-width: 90px !important;
        height: auto !important;
        display: block;
        margin: 10px 0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }

    .rich-text-content img:hover {
        transform: scale(1.02);
    }

    .rich-text-content a {
        color: #0d6efd;
        text-decoration: none;
    }

    .rich-text-content a:hover {
        text-decoration: underline;
    }

    .rich-text-content strong {
        font-weight: 600;
    }

    .rich-text-content em {
        font-style: italic;
    }

    .rich-text-content u {
        text-decoration: underline;
    }

    .rich-text-content s {
        text-decoration: line-through;
    }

    /* Ensure the content fits within your existing design */
    .rich-text-content {
        max-height: 500px;
        overflow-y: auto;
    }

    /* Scrollbar styling for the description box */
    .rich-text-content::-webkit-scrollbar {
        width: 6px;
    }

    .rich-text-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .rich-text-content::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .rich-text-content::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Image Zoom Modal Styles */
    .image-zoom-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .image-zoom-content {
        max-width: 90%;
        max-height: 90%;
        position: relative;
    }

    .image-zoom-content img {
        max-width: 100%;
        max-height: 100%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .image-zoom-close {
        position: absolute;
        top: -40px;
        right: 0;
        background: none;
        border: none;
        color: white;
        font-size: 30px;
        cursor: pointer;
        padding: 5px;
    }

    .image-zoom-close:hover {
        color: #ff6b6b;
    }

    .image-zoom-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
    }

    .image-zoom-prev,
    .image-zoom-next {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        font-size: 24px;
        padding: 10px 15px;
        cursor: pointer;
        border-radius: 50%;
        margin: 0 20px;
        transition: background 0.3s ease;
    }

    .image-zoom-prev:hover,
    .image-zoom-next:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .image-counter {
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: 16px;
        background: rgba(0, 0, 0, 0.7);
        padding: 5px 15px;
        border-radius: 20px;
    }

    /* Comment Section Styles */
    .comment-section {
        margin-top: 20px;
        border-top: 1px solid #dee2e6;
        padding-top: 20px;
    }

    .comment-tabs {
        display: flex;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .comment-tab {
        padding: 12px 16px;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 500;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .comment-tab.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
    }

    .comment-tab:hover {
        color: #0d6efd;
    }

    .comment-box {
        background-color: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 15px;
    }

    .comment-user {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #0d6efd;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        margin-right: 10px;
    }

    .user-name {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }

    .comment-time {
        font-size: 12px;
        color: #6c757d;
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .comment-text {
        color: #495057;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
    }

    .comment-input-container {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 16px;
        margin-top: 15px;
    }

    /* Quill Rich Text Editor Styles for Comments */
    .comment-rich-text-editor-container {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    #commentEditor {
        height: 150px;
        font-family: inherit;
        font-size: 14px;
    }

    .comment-ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        background: #f8f9fa !important;
        border-radius: 4px 4px 0 0 !important;
    }

    .comment-ql-container.ql-snow {
        border: none !important;
        border-radius: 0 0 4px 4px !important;
        font-family: inherit !important;
        font-size: 14px !important;
    }

    .comment-ql-editor {
        padding: 12px 15px !important;
        color: #495057 !important;
        line-height: 1.5 !important;
    }

    .comment-ql-editor.ql-blank::before {
        color: #6c757d !important;
        font-style: italic !important;
        font-size: 14px !important;
    }

    /* Comment Editor Image Styles */
    #commentEditor .ql-editor img {
        max-width: 400px !important;
        height: auto !important;
        display: block;
        margin: 10px 0;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        cursor: zoom-in;
        transition: transform 0.2s ease;
    }

    #commentEditor .ql-editor img:hover {
        transform: scale(1.02);
    }

    .comment-attachment-section {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }

    .attach-files-container {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .attach-files-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0;
        font-size: 14px;
    }

    .attach-files-button {
        background: none;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 6px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #495057;
        transition: all 0.3s ease;
    }

    .attach-files-button:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .attach-files-button i {
        font-size: 14px;
    }

    .comment-file-preview {
        margin-top: 10px;
    }

    .comment-file-preview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
        font-size: 13px;
    }

    .comment-file-preview-item:hover {
        background-color: #f8f9fa;
        border-color: #0d6efd;
    }

    .comment-file-info {
        display: flex;
        align-items: center;
        flex: 1;
    }

    .comment-file-name {
        font-weight: 500;
        margin-right: 8px;
        color: #495057;
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .comment-file-size {
        color: #6c757d;
        font-size: 11px;
        white-space: nowrap;
    }

    .remove-comment-file {
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        font-size: 16px;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .remove-comment-file:hover {
        background-color: #f8d7da;
        transform: scale(1.1);
    }

    .comment-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 15px;
        gap: 8px;
    }

    .btn-cancel-comment {
        background-color: transparent;
        border: 1px solid #6c757d;
        color: #6c757d;
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-cancel-comment:hover {
        background-color: #f8f9fa;
    }

    .btn-comment {
        background-color: #0d6efd;
        border: 1px solid #0d6efd;
        color: white;
        padding: 8px 20px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-comment:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .btn-comment:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
    }

    /* Comment action buttons */
    .comment-time .comment-actions {
        display: inline-flex;
        gap: 5px;
        margin-left: 10px;
    }

    .comment-time .btn-sm {
        padding: 2px 8px;
        font-size: 11px;
    }

    /* File type badges for comment attachments */
    .file-type-badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-image {
        background-color: #28a745;
        color: white;
    }

    .badge-pdf {
        background-color: #dc3545;
        color: white;
    }

    .badge-doc {
        background-color: #0d6efd;
        color: white;
    }

    .badge-other {
        background-color: #6c757d;
        color: white;
    }
    </style>
</head>
<body>
<div class="main-wrapper">
    <?php include 'layouts/menu.php'; ?>
    <div class="page-wrapper">
        <div class="content content-two">
            
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Task Information</h5>
                <div>
                    <a href="edit-task.php?id=<?= $task_id ?>" class="btn btn-sm btn-primary">
                        <i class="isax isax-edit me-1"></i>Edit Task
                    </a>
                    <a href="project-details.php?id=<?= $task['project_id'] ?>" class="btn btn-outline-info">
                        <i class="isax isax-building-3 me-1"></i>View Project
                    </a>
                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                        <i class="isax isax-refresh me-1"></i>Change Status
                    </button>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskModal">
                        <i class="isax isax-trash me-1"></i>Delete Task
                    </button>
                    <a href="project-tasks.php" class="btn btn-sm btn-outline-white">
                        <i class="isax isax-arrow-left me-1"></i>Back to Tasks
                    </a>
                </div>
            </div>

            <!-- Task Info Card -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mb-3">
                            <h4 class="view-task-title">Task Name:</h4>
                            <span class="d-flex align-items-center mt-1 view-task-detais-text"><?= htmlspecialchars($task['task_name']) ?></span>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Status:</h4>
                            <span class="badge fs-12" style="background-color: <?= $status_color ?>; color: white;">
                                <?= htmlspecialchars($status_name) ?>
                            </span>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Project:</h4>
                            <div class="d-flex align-items-center mt-1 view-task-detais-text">
                                <i class="isax isax-building-3 me-2 text-primary"></i>
                                <span><?= htmlspecialchars($task['project_name']) ?></span>
                            </div>
                            <small class="text-muted">Code: <?= htmlspecialchars($task['project_code']) ?></small>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Client:</h4>
                            <div class="d-flex align-items-center mt-1">
                                <?php if (!empty($task['client_image'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($task['client_image']) ?>" 
                                         class="rounded-circle me-2" 
                                         style="width:24px; height:24px; object-fit:cover;"
                                         onerror="this.src='assets/img/users/user-16.jpg'">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" 
                                         style="width:24px; height:24px; font-size:10px;">
                                        <?= strtoupper(substr($task['client_name'] ?? 'C', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <span class="view-task-detais-text"><?= htmlspecialchars($task['client_name'] ?? 'Not assigned') ?></span>
                            </div>
                        </div>

                        <!-- Assigned Users Section -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Assigned Users:</h4>
                            <?php if (!empty($assigned_users)): ?>
                                <div class="mt-1">
                                    <?php foreach ($assigned_users as $user): ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <?php if (!empty($user['profile_img'])): ?>
                                                <img src="../uploads/<?= htmlspecialchars($user['profile_img']) ?>" 
                                                     class="rounded-circle me-2" 
                                                     style="width:24px; height:24px; object-fit:cover;"
                                                     onerror="this.src='assets/img/users/user-16.jpg'">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" 
                                                     style="width:24px; height:24px; font-size:10px;">
                                                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-medium"><?= htmlspecialchars($user['name']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($user['email']) ?></small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted mt-1 view-task-detais-text">No users assigned</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Start Date:</h4>
                            <div class="d-flex align-items-center mt-1">
                                <i class="isax isax-calendar-1 me-2 text-info"></i>
                                <span class="view-task-detais-text"><?= $start_date ?></span>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">End Date:</h4>
                            <div class="d-flex align-items-center mt-1">
                                <i class="isax isax-calendar-1 me-2 text-info"></i>
                                <span class="view-task-detais-text"><?= $end_date ?></span>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Estimated Hours:</h4>
                            <div class="d-flex align-items-center mt-1">
                                <i class="isax isax-clock me-2 text-warning"></i>
                                <span class="view-task-detais-text"><?= !empty($task['hour']) ? htmlspecialchars($task['hour']) . ' hours' : 'Not set' ?></span>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <h4 class="view-task-title">Created Date:</h4>
                            <div class="d-flex align-items-center mt-1">
                                <i class="isax isax-calendar-tick me-2 text-success"></i>
                                <span class="view-task-detais-text"><?= $created_date ?></span>
                            </div>
                        </div>
                        
                        <?php if (!empty($task['task_description'])): ?>
                        <div class="col-12 mb-3">
                            <h4 class="view-task-title">Description:</h4>
                            <div class="mt-2 p-3 view-task-detais-text bg-light rounded rich-text-content">
                                <?= $task['task_description'] ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Comment Section -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="comment-section">
                        <!-- Comment Tabs -->
                        <div class="comment-tabs">
                            <label>Comments</label>
                        </div>
                        
                        <!-- Existing Comments Display -->
                        <div id="existingComments" class="mb-3">
                            <!-- Comments will be loaded here via AJAX -->
                        </div>
                        
                        <!-- Comment Input with Rich Text Editor -->
                        <div class="comment-input-container">
                            <!-- Hidden field for editing existing comment -->
                            <input type="hidden" id="editing_comment_id" value="">
                            
                            <!-- Rich Text Editor -->
                            <div class="comment-rich-text-editor-container">
                                <div id="commentEditor"></div>
                            </div>
                            
                            <!-- Comment Attachment Section -->
                            <div class="comment-attachment-section">
                                <div class="attach-files-container">
                                    <span class="attach-files-label">Attach Files (Images, Documents, Archives)</span>
                                    <button type="button" class="attach-files-button" id="attachFilesButton">
                                        <i class="isax isax-document-upload"></i>
                                        Attach Files (Images, PDF, Excel, Word, etc.)
                                    </button>
                                </div>
                                
                                <div class="comment-file-preview" id="commentFilePreview"></div>
                            </div>
                            
                            <!-- Hidden file input for attachments -->
                            <input type="file" class="comment-file-input" id="comment_attachments" name="comment_attachments[]" multiple 
                                   accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.zip,.rar,.7z" style="display: none;">
                            
                            <div class="comment-actions">
                                <button type="button" class="btn-cancel-comment" id="cancelComment">Cancel</button>
                                <button type="button" class="btn-comment" id="submitComment">Add Comment</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Task Attachments Card -->
            <?php if (!empty($attachments)): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Task Attachments</h6>
                    <div class="row">
                        <?php foreach ($attachments as $attachment): 
                            $file_path = '../uploads/task_images/' . $attachment['image'];
                            $file_extension = pathinfo($attachment['image'], PATHINFO_EXTENSION);
                            $is_image = in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $file_icon = $is_image ? 'isax-gallery' : 'isax-document';
                            $file_type = $is_image ? 'IMAGE' : strtoupper($file_extension);
                        ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="attachment-item border rounded p-3">
                                <div class="d-flex align-items-center">
                                    <div class="attachment-icon me-3">
                                        <i class="isax <?= $file_icon ?> text-primary fs-4"></i>
                                    </div>
                                    <div class="attachment-details flex-grow-1">
                                        <div class="attachment-name fw-medium text-truncate">
                                            <?= htmlspecialchars($attachment['image']) ?>
                                        </div>
                                        <div class="attachment-meta text-muted small">
                                            <div>Type: <?= $file_type ?></div>
                                            <div>Uploaded: <?= date('d-m-Y', strtotime($attachment['created_at'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="attachment-actions mt-3 d-flex gap-2">
                                    <?php if ($is_image): ?>
                                        <a href="<?= $file_path ?>" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                                            <i class="isax isax-eye me-1"></i>View
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= $file_path ?>" download class="btn btn-sm btn-outline-primary flex-fill">
                                             <i class="fa-solid fa-download me-1"></i> Download
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= $file_path ?>" download class="btn btn-sm btn-outline-info">
                                        <i class="fa-solid fa-download me-1"></i> 
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Task Timeline Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Task Timeline</h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Task Created</h6>
                                <p class="text-muted mb-1"><?= $created_date ?></p>
                                <small class="text-muted">Task was created in the system</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Work Started</h6>
                                <p class="text-muted mb-1"><?= $start_date ?></p>
                                <small class="text-muted">Scheduled start date for the task</small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Expected Completion</h6>
                                <p class="text-muted mb-1"><?= $end_date ?></p>
                                <small class="text-muted">Scheduled completion date</small>
                            </div>
                        </div>
                        
                        <?php if ($status_id == 3): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Task Completed</h6>
                                <p class="text-muted mb-1"><?= date('d-m-Y') ?></p>
                                <small class="text-muted">Task marked as completed</small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Progress and Statistics Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Progress & Statistics</h6>
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="isax isax-clock text-primary fs-2"></i>
                                <h5 class="mt-2 mb-1"><?= !empty($task['hour']) ? htmlspecialchars($task['hour']) : '0' ?>h</h5>
                                <small class="text-muted">Estimated Hours</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="isax isax-calendar text-info fs-2"></i>
                                <h5 class="mt-2 mb-1">
                                    <?php 
                                    if (!empty($task['start_date']) && !empty($task['end_date'])) {
                                        $start = new DateTime($task['start_date']);
                                        $end = new DateTime($task['end_date']);
                                        $interval = $start->diff($end);
                                        echo $interval->days . ' days';
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </h5>
                                <small class="text-muted">Duration</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4 text-center mb-3">
                            <div class="p-3 border rounded">
                                <i class="isax isax-tick-circle text-success fs-2"></i>
                                <h5 class="mt-2 mb-1">
                                    <?php
                                    $progress = 0;
                                    switch($status_id) {
                                        case 1: $progress = 10; break; // Pending
                                        case 2: $progress = 50; break; // In Progress
                                        case 3: $progress = 100; break; // Completed
                                        case 4: $progress = 25; break; // On Hold
                                        case 5: $progress = 0; break; // Cancelled
                                    }
                                    echo $progress . '%';
                                    ?>
                                </h5>
                                <small class="text-muted">Progress</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Task Progress</span>
                            <span><?= $progress ?>%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%; background-color: <?= $status_color ?>;" 
                                 aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h6 class="mb-3">Quick Actions</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="edit-task.php?id=<?= $task_id ?>" class="btn btn-outline-primary">
                            <i class="isax isax-edit me-1"></i>Edit Task
                        </a>
                        <a href="project-details.php?id=<?= $task['project_id'] ?>" class="btn btn-outline-info">
                            <i class="isax isax-building-3 me-1"></i>View Project
                        </a>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changeStatusModal">
                            <i class="isax isax-refresh me-1"></i>Change Status
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteTaskModal">
                            <i class="isax isax-trash me-1"></i>Delete Task
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php include 'layouts/footer.php'; ?>
</div>

<!-- Change Status Modal -->
<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="process/action_update_task_status.php" method="POST">
                <input type="hidden" name="task_id" value="<?= $task_id ?>">
                <div class="modal-header">
                    <h6 class="modal-title">Change Task Status</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select New Status</label>
                        <select class="form-select" name="status_id" required>
                            <?php foreach ($statusOptions as $id => $status): ?>
                                <option value="<?= $id ?>" <?= $id == $status_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($status['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-white" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Task Modal -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-m">
        <div class="modal-content">
            <form method="POST" action="process/action_delete_task.php">
                <input type="hidden" name="id" value="<?= $task_id ?>">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <img src="assets/img/icons/delete.svg" alt="img">
                    </div>
                    <h6 class="mb-1">Delete Task</h6>
                    <p class="mb-3">Are you sure you want to delete "<?= htmlspecialchars($task['task_name']) ?>"?</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-white me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Zoom Modal for Description Images -->
<div class="image-zoom-modal" id="descImageZoomModal">
    <div class="image-zoom-content">
        <img id="descZoomedImage" src="" alt="Zoomed Image">
        <button class="image-zoom-close" id="descImageZoomClose">&times;</button>
        <div class="image-zoom-nav">
            <button class="image-zoom-prev" id="descImageZoomPrev">&#10094;</button>
            <button class="image-zoom-next" id="descImageZoomNext">&#10095;</button>
        </div>
        <div class="image-counter" id="descImageCounter"></div>
    </div>
</div>

<?php include 'layouts/vendor-scripts.php'; ?>

<!-- Quill.js Text Editor -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Comment functionality variables
let commentQuill;
let commentFiles = [];
let commentEditorImages = [];
let currentCommentImageIndex = 0;

// Initialize Comment Rich Text Editor with Image Support
function initCommentEditor() {
    commentQuill = new Quill('#commentEditor', {
        theme: 'snow',
        placeholder: 'Add Comment...',
        modules: {
            toolbar: {
                container: [
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ],
                handlers: {
                    'image': commentImageHandler
                }
            }
        },
        formats: [
            'bold', 'italic', 'underline',
            'list', 'bullet',
            'link', 'image'
        ]
    });
    
    // Update comment button state when editor content changes
    commentQuill.on('text-change', function() {
        updateCommentButtonState();
    });

    // Custom image handler for comment editor
    function commentImageHandler() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = async function() {
            const file = input.files[0];
            if (file) {
                // Check file size (max 5MB for images)
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('error', 'Image size should be less than 5MB');
                    return;
                }

                // Create a temporary image to get dimensions
                const img = new Image();
                const objectUrl = URL.createObjectURL(file);
                
                img.onload = function() {
                    // Resize image if it's too large
                    const maxWidth = 400;
                    const maxHeight = 300;
                    let { width, height } = img;

                    if (width > maxWidth || height > maxHeight) {
                        const ratio = Math.min(maxWidth / width, maxHeight / height);
                        width *= ratio;
                        height *= ratio;
                    }

                    // Create canvas for resizing
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = width;
                    canvas.height = height;

                    // Draw resized image
                    ctx.drawImage(img, 0, 0, width, height);

                    // Convert to blob
                    canvas.toBlob(function(blob) {
                        const reader = new FileReader();
                        reader.readAsDataURL(blob);
                        reader.onload = function() {
                            const base64data = reader.result;
                            
                            // Get current selection
                            const range = commentQuill.getSelection(true);
                            
                            // Insert the image
                            commentQuill.insertEmbed(range.index, 'image', base64data);
                            
                            // Move cursor after the image
                            commentQuill.setSelection(range.index + 1);
                            
                            // Clean up
                            URL.revokeObjectURL(objectUrl);
                        };
                    }, 'image/jpeg', 0.8);
                };

                img.src = objectUrl;
            }
        };
    }

    // Add click event listener to images in comment editor for zoom
    commentQuill.root.addEventListener('click', function(e) {
        if (e.target.tagName === 'IMG') {
            openCommentImageZoom(e.target.src);
        }
    });
}

// Initialize comment section
function initCommentSection() {
    const cancelBtn = document.getElementById('cancelComment');
    const commentBtn = document.getElementById('submitComment');
    const commentFileInput = document.getElementById('comment_attachments');
    const attachFilesButton = document.getElementById('attachFilesButton');
    
    // Handle attach files button click
    attachFilesButton.addEventListener('click', function() {
        commentFileInput.click();
    });
    
    // Handle cancel button
    cancelBtn.addEventListener('click', function() {
        resetCommentForm();
    });
    
    // Handle comment file selection
    commentFileInput.addEventListener('change', function(e) {
        handleCommentFiles(e.target.files);
    });
    
    function handleCommentFiles(files) {
        const maxSize = 10 * 1024 * 1024; // 10MB
        const allowedTypes = [
            // Images
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp', 'image/webp',
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed'
        ];
        
        // Also allow by file extension for better compatibility
        const allowedExtensions = [
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'ppt', 'pptx', 'txt',
            'zip', 'rar', '7z'
        ];
        
        for (let file of files) {
            // Check file size
            if (file.size > maxSize) {
                showAlert('error', `File "${file.name}" is too large. Maximum size is 10MB.`);
                continue;
            }
            
            // Get file extension
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            // Check file type and extension
            if (!allowedTypes.includes(file.type) && !allowedExtensions.includes(fileExtension)) {
                showAlert('error', `File type not supported for "${file.name}". Supported formats: Images, PDF, DOC, DOCX, XLS, XLSX, CSV, PPT, PPTX, TXT, ZIP, RAR, 7Z.`);
                continue;
            }
            
            // Add file to comment files
            commentFiles.push(file);
        }
        
        updateCommentFilePreview();
        updateCommentButtonState();
    }
    
    function updateCommentFilePreview() {
        const commentFilePreview = document.getElementById('commentFilePreview');
        
        commentFilePreview.innerHTML = '';
        
        if (commentFiles.length === 0) {
            return;
        }
        
        commentFiles.forEach((file, index) => {
            const fileSize = formatFileSize(file.size);
            const fileType = getCommentFileType(file);
            const fileIcon = getCommentFileIcon(file);
            const badgeClass = getCommentFileBadgeClass(file);
            
            const filePreviewItem = document.createElement('div');
            filePreviewItem.className = 'comment-file-preview-item';
            filePreviewItem.innerHTML = `
                <div class="comment-file-info">
                    <i class="${fileIcon} me-2" style="color: #6c757d;"></i>
                    <span class="comment-file-name">${file.name}</span>
                    <span class="comment-file-size ms-2">${fileSize}</span>
                    <span class="file-type-badge ${badgeClass} ms-2">${fileType}</span>
                </div>
                <button type="button" class="remove-comment-file" onclick="removeCommentFile(${index})">
                    <i class="isax isax-close-circle"></i>
                </button>
            `;
            commentFilePreview.appendChild(filePreviewItem);
        });
    }
    
    function updateCommentButtonState() {
        const hasText = commentQuill.getText().trim().length > 0;
        const hasFiles = commentFiles.length > 0;
        commentBtn.disabled = !(hasText || hasFiles);
    }
    
    // Handle comment button
    commentBtn.addEventListener('click', function() {
        submitComment();
    });
}

// Helper functions for file type detection
function getCommentFileType(file) {
    if (file.type.startsWith('image/')) return 'IMAGE';
    if (file.type === 'application/pdf') return 'PDF';
    if (file.type.includes('word') || file.type.includes('document')) return 'DOC';
    if (file.type.includes('excel') || file.type.includes('spreadsheet')) return 'XLS';
    if (file.type === 'text/csv') return 'CSV';
    if (file.type.includes('powerpoint') || file.type.includes('presentation')) return 'PPT';
    if (file.type === 'text/plain') return 'TXT';
    if (file.type.includes('zip') || file.type.includes('rar') || file.type.includes('7z')) return 'ARCHIVE';
    
    // Fallback to file extension
    const ext = file.name.split('.').pop().toUpperCase();
    return ext || 'FILE';
}

function getCommentFileIcon(file) {
    if (file.type.startsWith('image/')) return 'isax isax-gallery';
    if (file.type === 'application/pdf') return 'isax isax-document-text';
    if (file.type.includes('word') || file.type.includes('document')) return 'isax isax-document-text';
    if (file.type.includes('excel') || file.type.includes('spreadsheet')) return 'isax isax-table';
    if (file.type === 'text/csv') return 'isax isax-table';
    if (file.type.includes('powerpoint') || file.type.includes('presentation')) return 'isax isax-presention-chart';
    if (file.type === 'text/plain') return 'isax isax-document-text';
    if (file.type.includes('zip') || file.type.includes('rar') || file.type.includes('7z')) return 'isax isax-archive';
    return 'isax isax-document';
}

function getCommentFileBadgeClass(file) {
    if (file.type.startsWith('image/')) return 'badge-image';
    if (file.type === 'application/pdf') return 'badge-pdf';
    if (file.type.includes('word') || file.type.includes('document')) return 'badge-doc';
    if (file.type.includes('excel') || file.type.includes('spreadsheet')) return 'badge-doc';
    if (file.type === 'text/csv') return 'badge-doc';
    if (file.type.includes('powerpoint') || file.type.includes('presentation')) return 'badge-doc';
    if (file.type === 'text/plain') return 'badge-other';
    if (file.type.includes('zip') || file.type.includes('rar') || file.type.includes('7z')) return 'badge-other';
    return 'badge-other';
}

// Edit comment function
function editComment(commentId, commentText) {
    // Set editing mode
    document.getElementById('editing_comment_id').value = commentId;
    commentQuill.root.innerHTML = commentText;
    
    // Clear existing files when editing
    commentFiles = [];
    updateCommentFilePreview();
    
    // Change button text to "Update Comment"
    document.getElementById('submitComment').textContent = 'Update Comment';
    
    // Scroll to comment editor
    document.querySelector('.comment-input-container').scrollIntoView({ 
        behavior: 'smooth' 
    });
    
    // Update button state
    updateCommentButtonState();
}

// Delete comment function
function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: 'process/action_comment.php',
        type: 'POST',
        data: {
            action: 'delete_comment',
            comment_id: commentId,
            user_id: <?php echo $_SESSION['crm_user_id'] ?? 1; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Comment deleted successfully');
                // Remove the comment from the DOM
                $('#comment-' + commentId).remove();
                
                // Check if there are any comments left
                if ($('#existingComments .comment-box').length === 0) {
                    $('#existingComments').html('<div class="text-center text-muted py-3">No comments yet. Be the first to comment!</div>');
                }
            } else {
                showAlert('error', response.message || 'Error deleting comment');
            }
        },
        error: function(xhr, status, error) {
            showAlert('error', 'Error deleting comment: ' + error);
        }
    });
}

// Submit comment function
function submitComment() {
    const commentText = commentQuill.getText().trim();
    const commentHTML = commentQuill.root.innerHTML;
    const editingCommentId = document.getElementById('editing_comment_id').value;
    const taskId = <?php echo $task_id; ?>;
    
    if (!commentText && commentFiles.length === 0) {
        showAlert('error', 'Please enter a comment or attach files');
        return;
    }
    
    // Create FormData for AJAX request
    const formData = new FormData();
    formData.append('comment_text', commentHTML);
    formData.append('task_id', taskId);
    formData.append('user_id', <?php echo $_SESSION['crm_user_id'] ?? 1; ?>);
    
    if (editingCommentId) {
        formData.append('comment_id', editingCommentId);
        formData.append('action', 'update_comment');
    } else {
        formData.append('action', 'add_comment');
    }
    
    // Append files
    commentFiles.forEach((file, index) => {
        formData.append(`comment_files[]`, file);
    });
    
    // Show loading state
    const commentBtn = document.getElementById('submitComment');
    const originalText = commentBtn.textContent;
    commentBtn.textContent = 'Saving...';
    commentBtn.disabled = true;
    
    // Send AJAX request
    $.ajax({
        url: 'process/action_comment.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('success', editingCommentId ? 'Comment updated successfully' : 'Comment added successfully');
                resetCommentForm();
                loadComments(); // Reload comments
            } else {
                showAlert('error', response.message || 'Error saving comment');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            showAlert('error', 'Error saving comment: ' + error);
        },
        complete: function() {
            commentBtn.textContent = originalText;
            commentBtn.disabled = false;
        }
    });
}

// Reset comment form
function resetCommentForm() {
    commentQuill.setText('');
    commentFiles = [];
    updateCommentFilePreview();
    document.getElementById('submitComment').disabled = true;
    document.getElementById('editing_comment_id').value = '';
    document.getElementById('submitComment').textContent = 'Add Comment';
}

// Remove comment file
function removeCommentFile(index) {
    commentFiles.splice(index, 1);
    updateCommentFilePreview();
    updateCommentButtonState();
}

// Load existing comments
function loadComments() {
    const taskId = <?php echo $task_id; ?>;
    
    $.ajax({
        url: 'process/fetch_comments.php',
        type: 'POST',
        data: { task_id: taskId },
        success: function(response) {
            $('#existingComments').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error loading comments:', error);
            $('#existingComments').html('<div class="text-center text-muted py-3">Error loading comments</div>');
        }
    });
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Show alert function
function showAlert(type, message) {
    const alertClass = type === 'error' ? 'alert-danger' : 
                     type === 'warning' ? 'alert-warning' : 
                     type === 'success' ? 'alert-success' : 'alert-info';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="isax isax-${type === 'error' ? 'danger' : type} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    $('.comment-tabs').after(alertHtml);
    
    // Auto remove alert after 5 seconds
    setTimeout(() => {
        $('.alert').alert('close');
    }, 5000);
}

// Image zoom functionality for comment editor
function openCommentImageZoom(src) {
    // Get all images from comment editor
    commentEditorImages = Array.from(commentQuill.root.querySelectorAll('img')).map(img => img.src);
    currentCommentImageIndex = commentEditorImages.indexOf(src);
    
    if (currentCommentImageIndex === -1) return;
    
    const modal = document.getElementById('descImageZoomModal');
    const zoomedImage = document.getElementById('descZoomedImage');
    const imageCounter = document.getElementById('descImageCounter');
    
    zoomedImage.src = src;
    updateCommentImageCounter();
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function navigateCommentImage(direction) {
    currentCommentImageIndex += direction;
    
    if (currentCommentImageIndex < 0) {
        currentCommentImageIndex = commentEditorImages.length - 1;
    } else if (currentCommentImageIndex >= commentEditorImages.length) {
        currentCommentImageIndex = 0;
    }
    
    const zoomedImage = document.getElementById('descZoomedImage');
    zoomedImage.src = commentEditorImages[currentCommentImageIndex];
    updateCommentImageCounter();
}

function updateCommentImageCounter() {
    const imageCounter = document.getElementById('descImageCounter');
    imageCounter.textContent = `${currentCommentImageIndex + 1} / ${commentEditorImages.length}`;
}

// Image zoom functionality for description images
let descImages = [];
let currentDescImageIndex = 0;

function initDescImageZoom() {
    // Get all images from rich text content
    descImages = Array.from(document.querySelectorAll('.rich-text-content img')).map(img => img.src);
    
    // Add click event listeners to description images
    document.querySelectorAll('.rich-text-content img').forEach((img, index) => {
        img.addEventListener('click', function() {
            openDescImageZoom(this.src);
        });
    });
    
    // Initialize modal events
    const modal = document.getElementById('descImageZoomModal');
    const closeBtn = document.getElementById('descImageZoomClose');
    const prevBtn = document.getElementById('descImageZoomPrev');
    const nextBtn = document.getElementById('descImageZoomNext');

    closeBtn.addEventListener('click', closeDescImageZoom);
    prevBtn.addEventListener('click', () => navigateDescImage(-1));
    nextBtn.addEventListener('click', () => navigateDescImage(1));

    // Close modal when clicking outside the image
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeDescImageZoom();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (modal.style.display === 'flex') {
            if (e.key === 'Escape') closeDescImageZoom();
            if (e.key === 'ArrowLeft') navigateDescImage(-1);
            if (e.key === 'ArrowRight') navigateDescImage(1);
        }
    });
}

function openDescImageZoom(src) {
    currentDescImageIndex = descImages.indexOf(src);
    if (currentDescImageIndex === -1) return;
    
    const modal = document.getElementById('descImageZoomModal');
    const zoomedImage = document.getElementById('descZoomedImage');
    const imageCounter = document.getElementById('descImageCounter');
    
    zoomedImage.src = src;
    updateDescImageCounter();
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDescImageZoom() {
    const modal = document.getElementById('descImageZoomModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function navigateDescImage(direction) {
    currentDescImageIndex += direction;
    
    if (currentDescImageIndex < 0) {
        currentDescImageIndex = descImages.length - 1;
    } else if (currentDescImageIndex >= descImages.length) {
        currentDescImageIndex = 0;
    }
    
    const zoomedImage = document.getElementById('descZoomedImage');
    zoomedImage.src = descImages[currentDescImageIndex];
    updateDescImageCounter();
}

function updateDescImageCounter() {
    const imageCounter = document.getElementById('descImageCounter');
    imageCounter.textContent = `${currentDescImageIndex + 1} / ${descImages.length}`;
}

// Initialize everything when page loads
$(document).ready(function() {
    // Initialize comment editor and section
    initCommentEditor();
    initCommentSection();
    
    // Initialize image zoom for both description and comments
    initDescImageZoom();
    
    // Load existing comments
    loadComments();
});

// Global functions for comment actions - EXACTLY LIKE YOUR EDIT FILE
window.editComment = function(commentId, commentText) {
    console.log('Editing comment:', commentId, commentText);
    
    // Set editing mode
    document.getElementById('editing_comment_id').value = commentId;
    
    // Set the comment content in the editor
    if (commentQuill) {
        commentQuill.root.innerHTML = commentText;
    }
    
    // Clear existing files when editing
    commentFiles = [];
    updateCommentFilePreview();
    
    // Change button text to "Update Comment"
    document.getElementById('submitComment').textContent = 'Update Comment';
    
    // Scroll to comment editor
    document.querySelector('.comment-input-container').scrollIntoView({ 
        behavior: 'smooth' 
    });
    
    // Update button state
    updateCommentButtonState();
};

window.deleteComment = function(commentId) {
    console.log('Deleting comment:', commentId);
    
    if (!confirm('Are you sure you want to delete this comment? This action cannot be undone.')) {
        return;
    }
    
    $.ajax({
        url: 'process/action_comment.php',
        type: 'POST',
        data: {
            action: 'delete_comment',
            comment_id: commentId,
            user_id: <?php echo $_SESSION['crm_user_id'] ?? 1; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('success', 'Comment deleted successfully');
                // Remove the comment from the DOM
                $('#comment-' + commentId).remove();
                
                // Check if there are any comments left
                if ($('#existingComments .comment-box').length === 0) {
                    $('#existingComments').html('<div class="text-center text-muted py-3">No comments yet. Be the first to comment!</div>');
                }
            } else {
                showAlert('error', response.message || 'Error deleting comment');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', xhr.responseText);
            showAlert('error', 'Error deleting comment: ' + error);
        }
    });
};

// Event delegation for dynamically loaded comments - EXACTLY LIKE YOUR EDIT FILE
$(document).on('click', '.edit-comment-btn', function() {
    const commentId = $(this).data('comment-id');
    const commentText = $(this).data('comment-text');
    editComment(commentId, commentText);
});

// Handle delete comment button clicks using event delegation
$(document).on('click', '.delete-comment-btn', function() {
    const commentId = $(this).data('comment-id');
    deleteComment(commentId);
});
</script>

</body>
</html>