<?php
session_start();
include '../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = intval($_POST['task_id']);
    $current_user_id = isset($_SESSION['crm_user_id']) ? intval($_SESSION['crm_user_id']) : 0;
    
    $comments_query = "
        SELECT c.*, u.name as user_name, u.profile_img,
               (SELECT COUNT(*) FROM project_task_comment_files WHERE comment_id = c.id) as file_count
        FROM project_task_comments c
        LEFT JOIN login u ON c.user_id = u.id
        WHERE c.task_id = '$task_id' AND c.is_deleted = 0
        ORDER BY c.created_at DESC
    ";
    
    $comments_result = mysqli_query($conn, $comments_query);
    
    if (mysqli_num_rows($comments_result) === 0) {
        echo '<div class="text-center text-muted py-3">No comments yet. Be the first to comment!</div>';
        exit;
    }
    
    while ($comment = mysqli_fetch_assoc($comments_result)) {
        $is_owner = ($comment['user_id'] == $current_user_id);
        ?>
        <div class="comment-box" id="comment-<?php echo $comment['id']; ?>">
            <div class="comment-user">
                <div class="user-avatar">
                    <?php 
                    if (!empty($comment['profile_img'])) {
                        echo '<img src="../uploads/profiles/' . $comment['profile_img'] . '" alt="' . $comment['user_name'] . '" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">';
                    } else {
                        echo substr($comment['user_name'], 0, 1);
                    }
                    ?>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($comment['user_name']); ?></div>
                <div class="comment-time">
                    <?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?>
                    <?php if ($comment['created_at'] != $comment['updated_at']): ?>
                        <small>(edited)</small>
                    <?php endif; ?>
                    
                    <!-- Edit/Delete buttons -->
                    <!-- <?php if ($is_owner): ?>
                        <div class="comment-actions">
                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                    onclick="editComment(<?php echo $comment['id']; ?>, `<?php echo addslashes($comment['comment_text']); ?>`)">
                                <i class="isax isax-edit me-1"></i>Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                <i class="isax isax-trash me-1"></i>Delete
                            </button>
                        </div>
                    <?php endif; ?> -->
                    <!-- Edit/Delete buttons -->
<?php if ($is_owner): ?>
    <div class="comment-actions">
        <button type="button" class="btn btn-sm btn-outline-primary edit-comment-btn" 
                data-comment-id="<?php echo $comment['id']; ?>"
                data-comment-text="<?php echo htmlspecialchars($comment['comment_text'], ENT_QUOTES); ?>">
            <i class="isax isax-edit me-1"></i>Edit
        </button>
        <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" 
                data-comment-id="<?php echo $comment['id']; ?>">
            <i class="isax isax-trash me-1"></i>Delete
        </button>
    </div>
<?php endif; ?>
                </div>
            </div>
            
            <div class="comment-text">
                <?php echo $comment['comment_text']; ?>
            </div>
            
            <!-- Display comment files -->
            <?php
            $files_query = "SELECT * FROM project_task_comment_files WHERE comment_id = '{$comment['id']}'";
            $files_result = mysqli_query($conn, $files_query);
            
            if (mysqli_num_rows($files_result) > 0) {
                echo '<div class="comment-file-preview mt-2">';
                while ($file = mysqli_fetch_assoc($files_result)) {
                    $fileSize = formatFileSize($file['file_size']);
                    echo '
                    <div class="comment-file-preview-item">
                        <div class="comment-file-info">
                            <span class="comment-file-name">' . htmlspecialchars($file['file_name']) . '</span>
                            <span class="comment-file-size">' . $fileSize . '</span>
                        </div>
                        <a href="../uploads/comment_files/' . $file['file_path'] . '" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                    </div>';
                }
                echo '</div>';
            }
            ?>
        </div>
        <?php
    }
} else {
    echo '<div class="text-center text-muted py-3">Invalid request method</div>';
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 Bytes';
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>