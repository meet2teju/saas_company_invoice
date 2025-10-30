<?php
session_start();
include '../../config/config.php';

// Set header to return HTML
header('Content-Type: text/html');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $current_user_id = isset($_SESSION['crm_user_id']) ? intval($_SESSION['crm_user_id']) : 0;
    $current_role_id = isset($_SESSION['role_id']) ? intval($_SESSION['role_id']) : 0;
    
    if (empty($task_id)) {
        echo '<div class="text-center text-muted py-3">Task ID is required</div>';
        exit;
    }
    
    try {
        // Base query to fetch comments
        $query = "
            SELECT 
                c.*,
                u.name as user_name,
                u.profile_img,
                u.role_id as user_role_id,
                ur.name as role_name
            FROM project_task_comments c
            LEFT JOIN login u ON c.user_id = u.id
            LEFT JOIN user_role ur ON u.role_id = ur.id
            WHERE c.task_id = '$task_id' AND c.is_deleted = 0
        ";
        
        // Role-based filtering for comment visibility
        if ($current_role_id != 1) { // If not admin
            // Show comments from:
            // 1. The current user
            // 2. Admin users (role_id = 1)
            // 3. Users who are assigned to this task's project
            $query .= " AND (
                c.user_id = '$current_user_id' 
                OR u.role_id = 1 
                OR EXISTS (
                    SELECT 1 FROM project_users pu 
                    WHERE pu.project_id = (
                        SELECT project_id FROM project_task WHERE id = '$task_id'
                    ) 
                    AND pu.user_id = c.user_id
                    AND pu.user_id = '$current_user_id'
                )
            )";
        }
        
        $query .= " ORDER BY c.created_at ASC";
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception("Database error: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($result) === 0) {
            echo '<div class="text-center text-muted py-3">No comments yet. Be the first to comment!</div>';
            exit;
        }
        
        // Fetch all comments
        $comments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $comments[] = $row;
        }
        
        // Display comments
        foreach ($comments as $comment) {
            $comment_id = $comment['id'];
            $user_name = htmlspecialchars($comment['user_name']);
            $comment_text = $comment['comment_text'];
            $created_at = date('M j, Y g:i A', strtotime($comment['created_at']));
            $profile_img = $comment['profile_img'];
            $user_role_id = $comment['user_role_id'];
            $role_name = $comment['role_name'];
            $comment_user_id = $comment['user_id'];
            
            // Get user initials for avatar
            $initials = '';
            if (!empty($user_name)) {
                $name_parts = explode(' ', $user_name);
                $initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
            }
            
            // Fetch files for this comment
            $files_query = "SELECT * FROM project_task_comment_files WHERE comment_id = '$comment_id'";
            $files_result = mysqli_query($conn, $files_query);
            $files = [];
            while ($file = mysqli_fetch_assoc($files_result)) {
                $files[] = $file;
            }
            
            ?>
            <div class="comment-box" id="comment-<?php echo $comment_id; ?>">
                <div class="comment-user">
                    <div class="user-avatar">
                        <?php if (!empty($profile_img)): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($profile_img); ?>" 
                                 alt="<?php echo $user_name; ?>" 
                                 style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                        <?php else: ?>
                            <?php echo $initials; ?>
                        <?php endif; ?>
                    </div>
                    <div class="user-name">
                        <?php echo $user_name; ?>
                        <?php if ($user_role_id == 1): ?>
                            <span class="badge bg-primary ms-1" style="font-size: 10px;">Admin</span>
                        <?php endif; ?>
                    </div>
                    <div class="comment-time">
                        <?php echo $created_at; ?>
                        
                        <!-- Edit/Delete buttons -->
                        <?php 
                        // Show edit button if:
                        // 1. User owns the comment OR
                        // 2. User is admin (can edit any comment)
                        $show_edit_button = ($comment_user_id == $current_user_id || $current_role_id == 1);
                        
                        // Show delete button if:
                        // 1. User owns the comment OR
                        // 2. User is admin (can delete any comment)
                        $show_delete_button = ($comment_user_id == $current_user_id || $current_role_id == 1);
                        ?>
                        
                        <?php if ($show_edit_button || $show_delete_button): ?>
                            <div class="comment-actions">
                                <?php if ($show_edit_button): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary edit-comment-btn" 
                                            data-comment-id="<?php echo $comment_id; ?>"
                                            data-comment-text="<?php echo htmlspecialchars($comment_text); ?>">
                                        <i class="isax isax-edit"></i>
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($show_delete_button): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" 
                                            data-comment-id="<?php echo $comment_id; ?>">
                                        <i class="isax isax-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="comment-text">
                    <?php echo $comment_text; ?>
                </div>
                
                <!-- Display attached files -->
                <?php if (!empty($files)): ?>
                    <div class="comment-files mt-3">
                        <div class="attach-files-label mb-2">Attached Files:</div>
                        <div class="file-list">
                            <?php foreach ($files as $file): 
                                $file_name = htmlspecialchars($file['file_name']);
                                $file_path = $file['file_path'];
                                $file_type = $file['file_type'];
                                $file_size = $file['file_size'];
                                
                                // Determine file icon and badge class
                                $file_icon = 'isax isax-document';
                                $badge_class = 'badge-other';
                                
                                if (strpos($file_type, 'image/') === 0) {
                                    $file_icon = 'isax isax-gallery';
                                    $badge_class = 'badge-image';
                                } elseif ($file_type === 'application/pdf') {
                                    $file_icon = 'isax isax-document-text';
                                    $badge_class = 'badge-pdf';
                                } elseif (strpos($file_type, 'word') !== false || strpos($file_type, 'document') !== false) {
                                    $file_icon = 'isax isax-document-text';
                                    $badge_class = 'badge-doc';
                                } elseif (strpos($file_type, 'excel') !== false || strpos($file_type, 'spreadsheet') !== false || $file_type === 'text/csv') {
                                    $file_icon = 'isax isax-table';
                                    $badge_class = 'badge-doc';
                                } elseif (strpos($file_type, 'powerpoint') !== false || strpos($file_type, 'presentation') !== false) {
                                    $file_icon = 'isax isax-presention-chart';
                                    $badge_class = 'badge-doc';
                                } elseif (strpos($file_type, 'zip') !== false || strpos($file_type, 'rar') !== false || strpos($file_type, '7z') !== false) {
                                    $file_icon = 'isax isax-archive';
                                    $badge_class = 'badge-other';
                                }
                                
                                // Determine file type text
                                $file_type_text = 'FILE';
                                if (strpos($file_type, 'image/') === 0) $file_type_text = 'IMAGE';
                                elseif ($file_type === 'application/pdf') $file_type_text = 'PDF';
                                elseif (strpos($file_type, 'word') !== false || strpos($file_type, 'document') !== false) $file_type_text = 'DOC';
                                elseif (strpos($file_type, 'excel') !== false || strpos($file_type, 'spreadsheet') !== false) $file_type_text = 'XLS';
                                elseif ($file_type === 'text/csv') $file_type_text = 'CSV';
                                elseif (strpos($file_type, 'powerpoint') !== false || strpos($file_type, 'presentation') !== false) $file_type_text = 'PPT';
                                elseif ($file_type === 'text/plain') $file_type_text = 'TXT';
                                elseif (strpos($file_type, 'zip') !== false || strpos($file_type, 'rar') !== false || strpos($file_type, '7z') !== false) $file_type_text = 'ARCHIVE';
                                
                                // Format file size
                                $file_size_formatted = '';
                                if ($file_size > 0) {
                                    if ($file_size < 1024) {
                                        $file_size_formatted = $file_size . ' B';
                                    } elseif ($file_size < 1048576) {
                                        $file_size_formatted = round($file_size / 1024, 2) . ' KB';
                                    } else {
                                        $file_size_formatted = round($file_size / 1048576, 2) . ' MB';
                                    }
                                }
                            ?>
                            <div class="comment-file-preview-item mb-2">
                                <div class="comment-file-info">
                                    <i class="<?php echo $file_icon; ?> me-2" style="color: #6c757d;"></i>
                                    <a href="../uploads/comment_files/<?php echo $file_path; ?>" 
                                       target="_blank" 
                                       class="comment-file-name text-decoration-none">
                                        <?php echo $file_name; ?>
                                    </a>
                                    <?php if ($file_size_formatted): ?>
                                        <span class="comment-file-size ms-2"><?php echo $file_size_formatted; ?></span>
                                    <?php endif; ?>
                                    <span class="file-type-badge <?php echo $badge_class; ?> ms-2"><?php echo $file_type_text; ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
        
    } catch (Exception $e) {
        echo '<div class="text-center text-danger py-3">Error loading comments: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="text-center text-muted py-3">Invalid request method</div>';
}
?>