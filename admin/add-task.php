<?php include 'layouts/session.php'; ?>
<?php
include '../config/config.php';

// Fetch available projects
$projects = [];
$project_query = "SELECT id, project_name FROM project WHERE is_deleted = 0 ORDER BY project_name ASC";
$project_result = mysqli_query($conn, $project_query);
while ($row = mysqli_fetch_assoc($project_result)) {
    $projects[] = $row;
}

// Fetch all users from login table for assignment
$users = [];
$user_query = "
    SELECT id, name, email, profile_img 
    FROM login 
    WHERE is_deleted = 0 
    ORDER BY name ASC
";
$user_result = mysqli_query($conn, $user_query);
while ($row = mysqli_fetch_assoc($user_result)) {
    $users[] = $row;
}

// Fetch statuses from project_status table
$statuses = [];
$status_query = "SELECT id, status_name FROM project_status WHERE is_deleted = 0 ORDER BY id";
$status_result = mysqli_query($conn, $status_query);
while ($row = mysqli_fetch_assoc($status_result)) {
    $statuses[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    
    <!-- Additional CSS for datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Quill.js Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <!-- CSS for custom multi-select dropdown -->
    <style>
      .multiselect-container {
    position: relative;
    width: 100%;
}

.multiselect-dropdown {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    background-color: white;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.multiselect-dropdown:after {
    content: "▼";
    font-size: 12px;
}

.multiselect-options {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.multiselect-options.show {
    display: block;
}

.multiselect-option {
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.multiselect-option:hover {
    background-color: #f8f9fa;
}

.multiselect-option input {
    margin-right: 8px;
}

.selected-users-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 40px;
    align-items: center;
}

.selected-user {
    background-color: #e9ecef;
    border-radius: 16px;
    padding: 4px 12px;
    display: flex;
    align-items: center;
    font-size: 14px;
}

.selected-user .remove-user {
    margin-left: 6px;
    cursor: pointer;
    font-weight: bold;
}

.no-users-message {
    color: #6c757d;
    font-style: italic;
}

.loading-message {
    color: #0d6efd;
    font-style: italic;
}

.search-container {
    padding: 8px 12px;
    border-bottom: 1px solid #e9ecef;
}

.search-input {
    width: 100%;
    padding: 6px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
}

.search-input:focus {
    outline: none;
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* File Upload Styles */
.file-upload-container {
    border: 2px dashed #dee2e6;
    border-radius: 0.375rem;
    padding: 20px;
    text-align: center;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.file-upload-container.dragover {
    border-color: #0d6efd;
    background-color: #e7f1ff;
}

.file-upload-label {
    cursor: pointer;
    display: block;
}

.file-upload-icon {
    font-size: 48px;
    color: #6c757d;
    margin-bottom: 10px;
}

.file-input {
    display: none;
}

.file-preview-container {
    margin-top: 15px;
}

.file-preview {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    margin-bottom: 8px;
}

.file-preview-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.file-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 4px;
    margin-right: 12px;
    font-size: 20px;
}

.file-details {
    flex: 1;
}

.file-name {
    font-weight: 500;
    margin-bottom: 2px;
}

.file-size {
    font-size: 12px;
    color: #6c757d;
}

.remove-file {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    font-size: 18px;
    padding: 5px;
}

.remove-file:hover {
    color: #c82333;
}

.file-type-badge {
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
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

/* Text Editor Styles */
.ql-toolbar.ql-snow {
    border: 1px solid #ced4da;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
}

.ql-container.ql-snow {
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    font-family: inherit;
}

.ql-editor {
    min-height: 150px;
    font-size: 14px;
    line-height: 1.5;
}

.ql-editor.ql-blank::before {
    color: #6c757d;
    font-style: italic;
    font-size: 14px;
}

/* Customize toolbar buttons */
.ql-toolbar .ql-stroke {
    stroke: #495057;
}

.ql-toolbar .ql-fill {
    fill: #495057;
}

.ql-toolbar button:hover .ql-stroke,
.ql-toolbar button:focus .ql-stroke,
.ql-toolbar button.ql-active .ql-stroke {
    stroke: #0d6efd;
}

.ql-toolbar button:hover .ql-fill,
.ql-toolbar button:focus .ql-fill,
.ql-toolbar button.ql-active .ql-fill {
    fill: #0d6efd;
}

/* Image size constraints in editor */
.ql-editor img {
    max-width: 400px !important;
    height: auto !important;
    display: block;
    margin: 10px 0;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: zoom-in;
    transition: transform 0.2s ease;
}

.ql-editor img:hover {
    transform: scale(1.02);
}

/* Image Zoom Modal */
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
    </style>
</head>

<body>

    <!-- Start Main Wrapper -->
    <div class="main-wrapper">

        <?php include 'layouts/menu.php'; ?>

        <!-- ========================
            Start Page Content
        ========================= -->

        <div class="page-wrapper">

            <!-- Start Content -->
            <div class="content">

                <!-- Start row  -->
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Add Tasks</h6>
                                <a href="project-tasks.php" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_add_task.php" method="POST" id="form" enctype="multipart/form-data">
                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? 1; ?>">

                                        <div class="border-bottom mb-3 pb-1">
                                            <!-- start row -->
                                 <div class="row justify-content-between">
    <div class="col-12">
        <div class="row gx-3">
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3">
                    <label class="form-label">Project <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="project_id" id="project_id">
                        <option value="">Select Project</option>
                        <?php
                        foreach ($projects as $project) {
                            echo '<option value="' . $project['id'] . '">' . htmlspecialchars($project['project_name']) . '</option>';
                        }
                        ?>
                    </select>
                    <span class="text-danger error-text" id="project_error"></span>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3">
                    <label class="form-label">Task Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="task_name" id="task_name" placeholder="Enter task name">
                    <span class="text-danger error-text" id="task_name_error"></span>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <div class="input-group position-relative">
                        <input type="text" class="form-control datepicker" id="start_date" name="start_date">
                        <span class="input-group-text">
                            <i class="isax isax-calendar-2"></i>
                        </span>
                    </div>
                    <span class="text-danger error-text" id="start_date_error"></span>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <div class="input-group position-relative">
                        <input type="text" class="form-control datepicker" id="end_date" name="end_date">
                        <span class="input-group-text">
                            <i class="isax isax-calendar-2"></i>
                        </span>
                    </div>
                    <span class="text-danger error-text" id="end_date_error"></span>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3">
                    <label class="form-label">Estimated Hours</label>
                    <input type="text" class="form-control" name="hour" id="hour" placeholder="Enter hours">
                    <span class="text-danger error-text" id="hour_error"></span>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="status_id" id="status_id">
                        <option value="">Select Status</option>
                        <?php
                        foreach ($statuses as $status) {
                            echo '<option value="' . $status['id'] . '">' . htmlspecialchars($status['status_name']) . '</option>';
                        }
                        ?>
                    </select>
                    <span class="text-danger error-text" id="status_error"></span>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="mb-3"> <label class="form-label">Assign Users <span class="text-danger">*</span></label>
                    <div class="multiselect-container">
                        <div class="multiselect-dropdown" id="multiselectDropdown">
                            Select Users
                        </div>
                        <div class="multiselect-options" id="multiselectOptions">
                            <div class="no-users-message p-2">Please select a project first</div>
                        </div>
                    </div>
                    <input type="hidden" name="assigned_users" id="assignedUsersInput">
                    <span class="text-danger error-text" id="users_error"></span>
                </div>
            </div>
            
            <!-- Selected Users Display -->
            <div class="col-12">
                <div class="selected-users-container mb-3" id="selectedUsersContainer">
                    <div class="no-users-message">No users selected</div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="mb-3">
                    <label for="task_description" class="form-label">Task Description</label>
                    <div id="text-editor-container">
                        <!-- Text editor will be initialized here -->
                    </div>
                    <input type="hidden" name="task_description" id="task_description">
                    <div class="form-text">You can format your text using the toolbar above. Images will be automatically resized to 400px width.</div>
                </div>
            </div>

            <!-- File Attachment Field -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label">Attachments</label>
                    <div class="file-upload-container" id="fileUploadContainer">
                        <label class="file-upload-label" for="task_attachments">
                            <div class="file-upload-icon">
                                <i class="isax isax-document-upload"></i>
                            </div>
                            <h5>Drop files here or click to upload</h5>
                            <p class="text-muted">Supports: Images, PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</p>
                            <p class="text-muted">Max file size: 10MB</p>
                        </label>
                        <input type="file" class="file-input" id="task_attachments" name="task_attachments[]" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
                    </div>
                    <div class="file-preview-container" id="filePreviewContainer"></div>
                    <span class="text-danger error-text" id="file_error"></span>
                </div>
            </div>

        </div>
    </div><!-- end col -->
</div>
<!-- end row -->


                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <button type="button" class="btn btn-outline-white" onclick="window.location.href='project-tasks.php'">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Task</button>
                                        </div>
                                    </form>
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

            </div>
            <!-- End Content -->

            <?php include 'layouts/footer.php'; ?>

        </div>

        <!-- ========================
            End Page Content
        ========================= -->

    </div>
    <!-- End Main Wrapper -->

    <!-- Image Zoom Modal -->
    <div class="image-zoom-modal" id="imageZoomModal">
        <div class="image-zoom-content">
            <img id="zoomedImage" src="" alt="Zoomed Image">
            <button class="image-zoom-close" id="imageZoomClose">&times;</button>
            <div class="image-zoom-nav">
                <button class="image-zoom-prev" id="imageZoomPrev">&#10094;</button>
                <button class="image-zoom-next" id="imageZoomNext">&#10095;</button>
            </div>
            <div class="image-counter" id="imageCounter"></div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <!-- Additional JS for datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- Quill.js Text Editor -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    
    <script>
        // Initialize text editor
        let quill;
        let editorImages = [];
        let currentImageIndex = 0;

        function initTextEditor() {
            quill = new Quill('#text-editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: {
                        container: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            [{ 'font': [] }, { 'size': [] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'script': 'sub'}, { 'script': 'super' }],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'indent': '-1'}, { 'indent': '+1' }, { 'align': [] }],
                            ['blockquote', 'code-block'],
                            ['link', 'image', 'video'],
                            ['clean']
                        ],
                        handlers: {
                            'image': imageHandler
                        }
                    }
                },
                placeholder: 'Enter task description...',
                formats: [
                    'header', 'font', 'size',
                    'bold', 'italic', 'underline', 'strike',
                    'color', 'background',
                    'script',
                    'list', 'bullet', 'indent', 'align',
                    'blockquote', 'code-block',
                    'link', 'image', 'video'
                ]
            });

            // Custom image handler
            function imageHandler() {
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
                            const maxWidth = 800;
                            const maxHeight = 600;
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
                                    const range = quill.getSelection(true);
                                    
                                    // Insert the image
                                    quill.insertEmbed(range.index, 'image', base64data);
                                    
                                    // Move cursor after the image
                                    quill.setSelection(range.index + 1);
                                    
                                    // Clean up
                                    URL.revokeObjectURL(objectUrl);
                                };
                            }, 'image/jpeg', 0.8);
                        };

                        img.src = objectUrl;
                    }
                };
            }

            // Update hidden input with HTML content when editor changes
            quill.on('text-change', function() {
                const content = quill.root.innerHTML;
                document.getElementById('task_description').value = content;
                
                // Update images array for zoom functionality
                updateEditorImages();
            });

            // Add click event listener to images for zoom
            quill.root.addEventListener('click', function(e) {
                if (e.target.tagName === 'IMG') {
                    openImageZoom(e.target.src);
                }
            });

            // Initial update of images
            setTimeout(updateEditorImages, 100);
        }

        // Update editor images array
        function updateEditorImages() {
            editorImages = Array.from(quill.root.querySelectorAll('img')).map(img => img.src);
        }

        // Image zoom functionality
        function openImageZoom(src) {
            currentImageIndex = editorImages.indexOf(src);
            if (currentImageIndex === -1) return;
            
            const modal = document.getElementById('imageZoomModal');
            const zoomedImage = document.getElementById('zoomedImage');
            const imageCounter = document.getElementById('imageCounter');
            
            zoomedImage.src = src;
            updateImageCounter();
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeImageZoom() {
            const modal = document.getElementById('imageZoomModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function navigateImage(direction) {
            currentImageIndex += direction;
            
            if (currentImageIndex < 0) {
                currentImageIndex = editorImages.length - 1;
            } else if (currentImageIndex >= editorImages.length) {
                currentImageIndex = 0;
            }
            
            const zoomedImage = document.getElementById('zoomedImage');
            zoomedImage.src = editorImages[currentImageIndex];
            updateImageCounter();
        }

        function updateImageCounter() {
            const imageCounter = document.getElementById('imageCounter');
            imageCounter.textContent = `${currentImageIndex + 1} / ${editorImages.length}`;
        }

        // Initialize image zoom modal events
        function initImageZoom() {
            const modal = document.getElementById('imageZoomModal');
            const closeBtn = document.getElementById('imageZoomClose');
            const prevBtn = document.getElementById('imageZoomPrev');
            const nextBtn = document.getElementById('imageZoomNext');

            closeBtn.addEventListener('click', closeImageZoom);
            prevBtn.addEventListener('click', () => navigateImage(-1));
            nextBtn.addEventListener('click', () => navigateImage(1));

            // Close modal when clicking outside the image
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageZoom();
                }
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (modal.style.display === 'flex') {
                    if (e.key === 'Escape') closeImageZoom();
                    if (e.key === 'ArrowLeft') navigateImage(-1);
                    if (e.key === 'ArrowRight') navigateImage(1);
                }
            });
        }

        // Function to set editor content
        function setEditorContent(htmlContent) {
            if (quill) {
                quill.root.innerHTML = htmlContent;
                document.getElementById('task_description').value = htmlContent;
                updateEditorImages();
            }
        }

        // Initialize datepicker and other components
        $(document).ready(function() {
            $('.datepicker').flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true,
                defaultDate: new Date(),
                clickOpens: true
            });
            
            // Initialize select2
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            
            // Initialize multiselect dropdown
            initMultiselect();
            
            // Initialize file upload
            initFileUpload();
            
            // Initialize text editor
            initTextEditor();
            
            // Initialize image zoom
            initImageZoom();
        });

        // Store project users data
        let projectUsers = {};
        let selectedUsers = [];
        let currentFilteredUsers = [];

        // File upload functionality
        function initFileUpload() {
            const fileInput = document.getElementById('task_attachments');
            const fileUploadContainer = document.getElementById('fileUploadContainer');
            const filePreviewContainer = document.getElementById('filePreviewContainer');

            // Click on container to trigger file input
            fileUploadContainer.addEventListener('click', function(e) {
                if (e.target !== fileInput) {
                    fileInput.click();
                }
            });

            // Drag and drop functionality
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadContainer.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadContainer.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadContainer.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                fileUploadContainer.classList.add('dragover');
            }

            function unhighlight() {
                fileUploadContainer.classList.remove('dragover');
            }

            // Handle dropped files
            fileUploadContainer.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles(files);
            }

            // Handle file input change
            fileInput.addEventListener('change', function() {
                handleFiles(this.files);
            });

            function handleFiles(files) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                const allowedTypes = [
                    'image/jpeg', 'image/jpg', 'image/png', 'image/gif',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'text/plain'
                ];

                for (let file of files) {
                    // Check file size
                    if (file.size > maxSize) {
                        showAlert('error', `File "${file.name}" is too large. Maximum size is 10MB.`);
                        continue;
                    }

                    // Check file type
                    if (!allowedTypes.includes(file.type)) {
                        showAlert('error', `File type not supported for "${file.name}".`);
                        continue;
                    }

                    addFilePreview(file);
                }
            }

            function addFilePreview(file) {
                const fileId = 'file-' + Date.now();
                const fileSize = formatFileSize(file.size);
                const fileType = getFileType(file);
                
                const filePreview = document.createElement('div');
                filePreview.className = 'file-preview';
                filePreview.id = fileId;
                
                filePreview.innerHTML = `
                    <div class="file-preview-info">
                        <div class="file-icon">
                            <i class="${getFileIcon(file)}"></i>
                        </div>
                        <div class="file-details">
                            <div class="file-name">${file.name}</div>
                            <div class="file-size">${fileSize}</div>
                        </div>
                        <span class="file-type-badge ${getFileBadgeClass(file)}">${fileType}</span>
                    </div>
                    <button type="button" class="remove-file" onclick="removeFile('${fileId}')">
                        <i class="isax isax-close-circle"></i>
                    </button>
                `;
                
                filePreviewContainer.appendChild(filePreview);
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            function getFileType(file) {
                const type = file.type.split('/')[1];
                return type ? type.toUpperCase() : 'FILE';
            }

            function getFileIcon(file) {
                if (file.type.startsWith('image/')) return 'isax isax-gallery';
                if (file.type === 'application/pdf') return 'isax isax-document';
                if (file.type.includes('word') || file.type.includes('document')) return 'isax isax-document-text';
                if (file.type.includes('excel') || file.type.includes('spreadsheet')) return 'isax isax-table';
                if (file.type.includes('powerpoint') || file.type.includes('presentation')) return 'isax isax-presention';
                return 'isax isax-document';
            }

            function getFileBadgeClass(file) {
                if (file.type.startsWith('image/')) return 'badge-image';
                if (file.type === 'application/pdf') return 'badge-pdf';
                if (file.type.includes('word') || file.type.includes('document')) return 'badge-doc';
                if (file.type.includes('excel') || file.type.includes('spreadsheet')) return 'badge-doc';
                if (file.type.includes('powerpoint') || file.type.includes('presentation')) return 'badge-doc';
                return 'badge-other';
            }
        }

        // Remove file from preview
        function removeFile(fileId) {
            const fileElement = document.getElementById(fileId);
            if (fileElement) {
                fileElement.remove();
            }
        }

        // Initialize multiselect functionality
        function initMultiselect() {
            const dropdown = document.getElementById('multiselectDropdown');
            const options = document.getElementById('multiselectOptions');
            
            // Toggle dropdown
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                options.classList.toggle('show');
                
                // Focus on search input when dropdown opens
                if (options.classList.contains('show')) {
                    setTimeout(() => {
                        const searchInput = document.getElementById('userSearchInput');
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }, 100);
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                options.classList.remove('show');
            });
            
            // Prevent dropdown from closing when clicking inside
            options.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Fetch project users when project is selected
        $('#project_id').on('change', function() {
            const projectId = $(this).val();
            
            // Clear selected users when project changes
            selectedUsers = [];
            updateSelectedUsersDisplay();
            updateDropdownText();
            
            if (!projectId) {
                // Clear users if no project selected
                updateUserOptions([]);
                return;
            }
            
            // Show loading message
            const optionsContainer = document.getElementById('multiselectOptions');
            optionsContainer.innerHTML = '<div class="loading-message p-2">Loading users...</div>';
            optionsContainer.classList.add('show');
            
            // Fetch users assigned to this project
            $.ajax({
                url: 'process/fetch_project_users.php',
                type: 'POST',
                data: { project_id: projectId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        projectUsers = response.users;
                        currentFilteredUsers = [...response.users]; // Initialize filtered users
                        updateUserOptions(response.users);
                    } else {
                        console.error('Error fetching project users:', response.message);
                        updateUserOptions([]);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    updateUserOptions([]);
                }
            });
        });

        // Update the dropdown options with project users
        function updateUserOptions(users) {
            const optionsContainer = document.getElementById('multiselectOptions');
            
            if (users.length === 0) {
                optionsContainer.innerHTML = '<div class="no-users-message p-2">No users assigned to this project</div>';
                return;
            }
            
            let optionsHtml = '';
            
            // Add search box
            optionsHtml += `
                <div class="search-container">
                    <input type="text" class="search-input" id="userSearchInput" placeholder="Search users..." onkeyup="filterUsers()" onclick="event.stopPropagation()">
                </div>
            `;
            
            users.forEach(user => {
                // Remove checkbox and email - just show the user name
                optionsHtml += `
                    <div class="multiselect-option" onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                        ${user.name}
                    </div>
                `;
            });
            
            optionsContainer.innerHTML = optionsHtml;
            currentFilteredUsers = [...users]; // Store current users for filtering
        }

        // Filter users based on search input
        function filterUsers() {
            const searchInput = document.getElementById('userSearchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const optionsContainer = document.getElementById('multiselectOptions');
            
            if (!searchTerm) {
                // If no search term, show all users
                renderUserOptions(projectUsers);
                currentFilteredUsers = [...projectUsers];
                return;
            }
            
            // Filter users based on search term
            const filteredUsers = projectUsers.filter(user => 
                user.name.toLowerCase().includes(searchTerm)
            );
            
            renderUserOptions(filteredUsers);
            currentFilteredUsers = filteredUsers;
        }

        // Render user options (helper function for filtering)
        function renderUserOptions(users) {
            const optionsContainer = document.getElementById('multiselectOptions');
            
            if (users.length === 0) {
                optionsContainer.innerHTML = `
                    <div class="search-container">
                        <input type="text" class="search-input" id="userSearchInput" placeholder="Search users..." onkeyup="filterUsers()" onclick="event.stopPropagation()">
                    </div>
                    <div class="no-users-message p-2">No users found</div>
                `;
                return;
            }
            
            let optionsHtml = '';
            
            // Add search box
            optionsHtml += `
                <div class="search-container">
                    <input type="text" class="search-input" id="userSearchInput" placeholder="Search users..." onkeyup="filterUsers()" onclick="event.stopPropagation()">
                </div>
            `;
            
            users.forEach(user => {
                optionsHtml += `
                    <div class="multiselect-option" onclick="selectUser(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                        ${user.name}
                    </div>
                `;
            });
            
            optionsContainer.innerHTML = optionsHtml;
        }

        // Select user when clicked
        function selectUser(userId, userName) {
            // Check if user is already selected
            const isAlreadySelected = selectedUsers.some(user => user.id === userId);
            
            if (!isAlreadySelected) {
                // Add user to selection
                selectedUsers.push({
                    id: userId,
                    name: userName
                });
                
                updateSelectedUsersDisplay();
                updateDropdownText();
                
                // Close dropdown after selection
                document.getElementById('multiselectOptions').classList.remove('show');
            }
        }

        // Update the display of selected users
        function updateSelectedUsersDisplay() {
            const container = document.getElementById('selectedUsersContainer');
            const hiddenInput = document.getElementById('assignedUsersInput');
            
            if (selectedUsers.length === 0) {
                container.innerHTML = '<div class="no-users-message">No users selected</div>';
                hiddenInput.value = '';
                return;
            }
            
            let usersHtml = '';
            const userIds = [];
            
            selectedUsers.forEach(user => {
                usersHtml += `
                    <div class="selected-user">
                        ${user.name}
                        <span class="remove-user" onclick="removeUser(${user.id})">×</span>
                    </div>
                `;
                userIds.push(user.id);
            });
            
            container.innerHTML = usersHtml;
            hiddenInput.value = JSON.stringify(userIds);
        }

        // Remove a user from selection
        function removeUser(userId) {
            selectedUsers = selectedUsers.filter(user => user.id !== userId);
            updateSelectedUsersDisplay();
            updateDropdownText();
        }

        // Update dropdown text based on selection
        function updateDropdownText() {
            const dropdown = document.getElementById('multiselectDropdown');
            
            if (selectedUsers.length === 0) {
                dropdown.textContent = 'Select Users';
            } else if (selectedUsers.length === 1) {
                dropdown.textContent = selectedUsers[0].name;
            } else {
                dropdown.textContent = `${selectedUsers.length} users selected`;
            }
        }

        // Form validation
        $('#form').on('submit', function(e) {
            e.preventDefault();
            var isValid = true;
            
            // Validate required fields
            if ($('#project_id').val() === '') {
                $('#project_error').text('Please select a project');
                isValid = false;
            } else {
                $('#project_error').text('');
            }
            
            if ($('#task_name').val() === '') {
                $('#task_name_error').text('Please enter task name');
                isValid = false;
            } else {
                $('#task_name_error').text('');
            }
            
            if ($('#start_date').val() === '') {
                $('#start_date_error').text('Please select start date');
                isValid = false;
            } else {
                $('#start_date_error').text('');
            }
            
            if ($('#end_date').val() === '') {
                $('#end_date_error').text('Please select end date');
                isValid = false;
            } else {
                $('#end_date_error').text('');
            }
            
            if ($('#status_id').val() === '') {
                $('#status_error').text('Please select status');
                isValid = false;
            } else {
                $('#status_error').text('');
            }
            
            // Validate at least one user is assigned
            if (selectedUsers.length === 0) {
                $('#users_error').text('Please assign at least one user');
                isValid = false;
            } else {
                $('#users_error').text('');
            }
            
            // Validate description is not empty (check both text content and HTML)
            const descriptionText = quill.getText().trim();
            const descriptionHtml = quill.root.innerHTML;
            if (descriptionText === '' || descriptionHtml === '<p><br></p>' || descriptionHtml === '<p></p>') {
                showAlert('warning', 'Please enter a task description');
                isValid = false;
            }
            
            // Validate end date is after start date
            if ($('#start_date').val() && $('#end_date').val()) {
                var startDate = new Date($('#start_date').val());
                var endDate = new Date($('#end_date').val());
                
                if (endDate < startDate) {
                    $('#end_date_error').text('End date cannot be before start date');
                    isValid = false;
                }
            }
            
            // Validate hours is a number
            if ($('#hour').val() && isNaN($('#hour').val())) {
                $('#hour_error').text('Please enter a valid number for hours');
                isValid = false;
            } else {
                $('#hour_error').text('');
            }
            
            if (isValid) {
                // Ensure the hidden input has the latest content
                document.getElementById('task_description').value = quill.root.innerHTML;
                this.submit();
            }
        });

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
            
            $('.selected-users-container').before(alertHtml);
            
            // Auto remove alert after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    </script>
    
    <script>
    $(document).ready(function () {
        // === Allow only numbers for hours ===
        $('#hour').on('input', function () {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });
    });
    </script>

</body>

</html>