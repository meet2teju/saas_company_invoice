<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>
<?php
$product_id = $_GET['id'];
$query = "SELECT * FROM product WHERE id = $product_id";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?> 
    <?php include 'layouts/head-css.php'; ?>
    <!-- Preview Modal CSS -->
    <style>
        .preview-modal .modal-dialog {
            max-width: 900px;
        }
        .preview-modal .modal-content {
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
        }
        .preview-modal .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
        }
        .preview-modal .modal-body {
            padding: 1.5rem;
            max-height: 70vh;
            overflow-y: auto;
        }
        .preview-modal .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
        }
        .preview-field {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 8px 12px;
            min-height: 38px;
        }
        .preview-image {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .preview-quill {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            min-height: 200px;
        }
        .form-label.preview-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .preview-radio {
            margin-right: 10px;
        }
        .preview-radio-label {
            margin-right: 20px;
        }
        .error-message {
            display: block;
            margin-top: 5px;
            font-size: 12px;
        }
        .form-label .required {
            color: #dc3545;
            margin-left: 3px;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>

<body>
    <!-- Start Main Wrapper -->
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <!-- Page Content -->
        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Edit Products</h6>
                                <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" id="previewBtn">
                                    <i class="isax isax-eye me-1"></i>Preview</a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form id="productForm" action="process/action_edit_product.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="current_image" value="<?= $row['product_img'] ?>">
                                        <input type="hidden" name="remove_main_image" id="remove_main_image" value="0">
                                        
                                        <!-- Main Form Fields -->
                                        <div class="row gx-3">
                                            <!-- Name Field -->
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Product Image </label>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                                            <div class="position-relative d-flex align-items-center">
                                                                <?php
                                                                $default_image = 'assets/img/products/default.png';
                                                                $image_path = '../uploads/' . $row['product_img'];
                                                                $product_img = (!empty($row['product_img']) && file_exists($image_path)) ? $image_path : $default_image;
                                                                ?>
                                                                <img src="<?= $product_img ?>"  id="image_preview" class="avatar avatar-xl" alt="Product Image">
                                                                <a href="javascript:void(0);" class="rounded-trash trash-top d-flex align-items-center justify-content-center" id="remove_image">
                                                                    <i class="isax isax-trash"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="d-inline-flex flex-column align-items-start">
                                                            <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                                <i class="isax isax-image me-1"></i>Upload Image
                                                                <input type="file" id="image_upload" name="product_img" class="form-control image-sign" accept="image/jpeg,image/png">
                                                            </div>
                                                            <span class="text-gray-9 fs-12">JPG or PNG format, not exceeding 5MB.</span>
                                                            <span id="image_error" class="error-message text-danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Item Type</label>
                                                    <div class="d-flex align-items-center">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" type="radio" name="item_type" id="Radio-sm-1" value="1" <?= ($row['item_type'] == '1') ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="Radio-sm-1">Product</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="item_type" id="Radio-sm-2" value="0" <?= ($row['item_type'] == '0') ? 'checked' : '' ?>>
                                                            <label class="form-check-label" for="Radio-sm-2">Service</label>
                                                        </div>
                                                    </div>
                                                    <!-- <span id="item_type_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Name <span class="required">*</span></label>
                                                    <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($row['name']) ?>">
                                                    <span id="name_error" class="error-message text-danger"></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Code Field -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">HSN Code<span class="required">*</span> </label>
                                                    <input type="text" class="form-control" name="code" id="code" value="<?= htmlspecialchars($row['code']) ?>">
                                                    <span id="code_error" class="error-message text-danger"></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Category Field -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Category <span class="required">*</span></label>
                                                    <select class="form-select" name="category_id" id="category_id">
                                                        <option value="">Select</option>
                                                        <?php
                                                        $cat_query = mysqli_query($conn, "SELECT * FROM category WHERE is_deleted = 0");
                                                        while ($cat = mysqli_fetch_assoc($cat_query)) {
                                                            $selected = ($cat['id'] == $row['category_id']) ? 'selected' : '';
                                                            echo "<option value='{$cat['id']}' $selected>{$cat['name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <span id="category_error" class="error-message text-danger"></span>
                                                </div>
                                            </div>
                                            
                                            <!-- Price Fields -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label ">Selling Price</label>
                                                    <input type="text" class="form-control" name="selling_price" id="selling_price" value="<?= $row['selling_price'] ?>">
                                                    <!-- <span id="selling_price_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label ">Purchase Price</label>
                                                    <input type="text" class="form-control" name="purchase_price" id="purchase_price" value="<?= $row['purchase_price'] ?>">
                                                    <!-- <span id="purchase_price_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <!-- Quantity Fields -->
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="text" class="form-control" name="quantity" id="quantity" value="<?= $row['quantity'] ?>">
                                                    <!-- <span id="quantity_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <!-- Unit Field -->
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Units </label>
                                                    <select class="form-select" name="unit_id" id="unit_id">
                                                        <option value="">Select</option>
                                                        <?php
                                                        $unit_query = mysqli_query($conn, "SELECT * FROM units WHERE is_deleted = 0");
                                                        while ($unit = mysqli_fetch_assoc($unit_query)) {
                                                            $selected = ($unit['id'] == $row['unit_id']) ? 'selected' : '';
                                                            echo "<option value='{$unit['id']}' $selected>{$unit['name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <!-- <span id="unit_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <!-- Discount Type Field -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Discount Type </label>
                                                    <select class="form-select" name="discount_type" id="discount_type">
                                                        <option value="">Select</option>
                                                        <option value="%" <?php echo ($row['discount_type'] == '%') ? 'selected' : ''; ?>>%</option>
                                                        <option value="Fixed" <?php echo ($row['discount_type'] == 'Fixed') ? 'selected' : ''; ?>>Fixed</option>
                                                    </select>

                                                    <!-- <span id="discount_type_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <!-- Alert Quantity Field -->
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Alert Quantity </label>
                                                    <input type="text" class="form-control" name="alert_quantity" id="alert_quantity" value="<?= $row['alert_quantity'] ?>">
                                                    <!-- <span id="alert_quantity_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <!-- Tax Field -->
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Tax </label>
                                                    <select class="form-select" name="tax_id" id="tax_id" >
                                                        <option value="">Select</option>
                                                       <?php
                                                    $tax_query = mysqli_query($conn, "SELECT * FROM tax WHERE status = 1");
                                                    while ($tax = mysqli_fetch_assoc($tax_query)) {
                                                        $selected = ($tax['id'] == $row['tax_id']) ? 'selected' : '';
                                                        echo "<option value='{$tax['id']}' data-rate='{$tax['rate']}' $selected>
                                                                {$tax['name']} ({$tax['rate']}%)
                                                            </option>";
                                                    }
                                                    ?>

                                                    </select>
                                                    <!-- <span id="tax_error" class="error-message text-danger"></span> -->
                                                </div>
                                            </div>
                                            
                                            <!-- Description Field -->
                                            
                                            <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="form-label">Product Description</label>

                                        <!-- Quill Editor Container -->
                                        <div id="editorContainer" style="height: 200px; background-color: #fff;"></div>

                                        <!-- Hidden Textarea to store HTML content on submit -->
                                        <textarea name="description" id="productDescription" class="form-control d-none"><?= htmlspecialchars($row['description']) ?></textarea>
                                    </div>
                                </div>
                                            <!-- Gallery Images Field -->
                                            <div class="col-lg-12">
                                            <div class="mb-3 pb-3 border-bottom">
                                                <label class="form-label">Gallery Images</label>
                                                <div class="file-upload drag-file w-100 d-flex align-items-center justify-content-center flex-column mb-3">
                                                    <span class="upload-img d-block mb-2"><i class="isax isax-image text-primary"></i></span>
                                                    <p class="mb-0 text-gray-9 fw-semibold">Drop Your Files or <a href="#" class="text-primary text-decoration-underline">Browse</a></p>
                                                    <input type="file" id="gallery_images" name="gallery_img[]" multiple accept="image/png, image/jpeg" class="form-control">
                                                    <p class="fs-13">Max Upload Size 800x800px. PNG / JPEG file, Maximum Upload size 5MB</p>
                                                </div>
                                                <div id="gallery_preview" class="d-flex align-items-center gap-3 flex-wrap">
                                                    <?php
                                                    $gallery_query = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = $product_id");
                                                    while ($img = mysqli_fetch_assoc($gallery_query)) {
                                                        $img_path = '../uploads/' . $img['gallery_img'];
                                                        if (file_exists($img_path)) {
                                                            echo '<div class="avatar avatar-xl border gallery-img p-1 position-relative" data-id="'.$img['id'].'">';
                                                            echo '<img src="' . $img_path . '" alt="Gallery" style="width: 80px; height: 80px; object-fit: cover;">';
                                                            echo '<a href="javascript:void(0);" class="rounded-trash gallery-trash d-flex align-items-center justify-content-center" onclick="deleteGalleryImage('.$img['id'].', this)"><i class="isax isax-trash"></i></a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <input type="hidden" name="deleted_images" id="deleted_images" value="">
                                                <span id="gallery_error" class="error-message text-danger"></span>
                                            </div>
                                        </div>
                                        </div>
                                        
                                        <div class="d-flex align-items-center justify-content-between">
                                            <button type="button" class="btn btn-outline-white" onclick="window.location.href='products.php'">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include 'layouts/footer.php'; ?>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade preview-modal" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Product Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Basic Details</h6>
                            
                            <div class="mb-3">
                                <span class="text-gray-9 fw-bold mb-2 d-flex">Product Image</span>
                                <div class="d-flex align-items-center">
                                    <div id="preview_image_container" class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                        <div class="position-relative d-flex align-items-center">
                                            <img id="previewImage" class="avatar avatar-xl" alt="Product Image">
                                        </div>
                                    </div>
                                    <div class="d-inline-flex flex-column align-items-start">
                                        <span class="text-gray-9 fs-12">JPG or PNG format, not exceeding 5MB.</span>
                                    </div>
                                </div>
                            </div>
                            
                            <label class="form-label preview-label">Item Type</label>
                            <div class="d-flex align-items-center mb-3">
                                <div class="preview-radio-label">
                                    <input class="form-check-input preview-radio" type="radio" disabled>
                                    <label class="form-check-label" id="previewItemType">Product</label>
                                </div>
                            </div>
                            
                            <div class="row gx-3">
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Name <span class="required">*</span></label>
                                        <div class="preview-field" id="previewName"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">HSN Code<span class="required">*</span> </label>
                                        <div class="preview-field" id="previewCode"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Category <span class="required">*</span></label>
                                        <div class="preview-field" id="previewCategory"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Selling Price</label>
                                        <div class="preview-field" id="previewSellingPrice"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 stock-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Purchase Price</label>
                                        <div class="preview-field" id="previewPurchasePrice"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 stock-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Quantity</label>
                                        <div class="preview-field" id="previewQuantity"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 stock-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Units </label>
                                        <div class="preview-field" id="previewUnit"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Discount Type </label>
                                        <div class="preview-field" id="previewDiscountType"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 stock-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Alert Quantity </label>
                                        <div class="preview-field" id="previewAlertQuantity"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Tax </label>
                                        <div class="preview-field" id="previewTax"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Product Description</label>
                                        <div class="preview-quill" id="previewDescription"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3 pb-3 border-bottom">
                                        <label class="form-label preview-label">Gallery Images</label>
                                        <div id="previewGallery" class="d-flex align-items-center gap-3 flex-wrap"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary" onclick="document.getElementById('productForm').submit()">Save Changes</button> -->
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>

<!-- Quill JS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Initialize Quill
    var quill = new Quill('#editorContainer', {
        theme: 'snow'
    });

    // Set Quill content from database (PHP)
    var existingDescription = <?= json_encode($row['description']) ?>;
    quill.root.innerHTML = existingDescription;

    // Copy content to hidden textarea on form submit
    $('#productForm').on('submit', function () {
        const html = quill.root.innerHTML.trim();
        $('#productDescription').val(html);
    });
</script>

    <script>
$(document).ready(function() {
    // Image upload preview
    $('#image_upload').change(function() {
        const file = this.files[0];
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png'];
            
            // Check file type
            if (!allowedTypes.includes(file.type)) {
                $('#image_error').text('Only JPG and PNG files are allowed');
                $(this).addClass('is-invalid');
                $(this).val(''); // reset file input
                return;
            }

            // Check file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                $('#image_error').text('File size must be less than 5MB');
                $(this).addClass('is-invalid');
                $(this).val('');
                return;
            }
            
            // Preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image_preview').attr('src', e.target.result);
                $('#image_error').text('');
                $('#image_upload').removeClass('is-invalid');
            }
            reader.readAsDataURL(file);
        }
    });

     
        // Gallery images preview
        $('#gallery_images').on('change', function() {
            const preview = $('#gallery_preview');
            const files = this.files;
            
            // Validate each file
            let hasError = false;
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.size > 5 * 1024 * 1024) {
                    $('#gallery_error').text('One or more files exceed 5MB limit');
                    $(this).addClass('is-invalid');
                    hasError = true;
                    continue;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const box = `<div class="avatar avatar-xl border gallery-img p-1 position-relative">
                        <img src="${e.target.result}" style="width:80px; height:80px; object-fit:cover;" alt="New Image">
                        <a href="javascript:void(0);" class="rounded-trash gallery-trash d-flex align-items-center justify-content-center">
                            <i class="isax isax-trash"></i>
                        </a>
                    </div>`;
                    preview.append(box);
                    $('#gallery_error').text('');
                    $('#gallery_images').removeClass('is-invalid');
                }
                reader.readAsDataURL(file);
            }
            
            if (hasError) {
                $(this).val('');
            }
        });

   $('#selling_price, #purchase_price, #quantity, #alert_quantity, #code').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, ''); // only numbers and dot
    });

    $('#name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, ''); // block numbers
    });

        // Form validation
       $('#productForm').submit(function(e) {
        let isValid = true;

        // Reset errors
        $('.error-message').text('');
        $('.is-invalid');

        // Required field checks
        if ($('#name').val().trim() === '') {
            $('#name_error').text('Product name is required');
            $('#name').addClass('is-invalid');
            isValid = false;
        }

        if ($('#code').val().trim() === '') {
            $('#code_error').text('Product HSNcode is required');
            $('#code').addClass('is-invalid');
            isValid = false;
        }

        if ($('#category_id').val() === '') {
            $('#category_error').text('Category is required');
            $('#category_id').addClass('is-invalid');
            isValid = false;
        }

        const imageInput = $('#image_upload')[0];
        if (imageInput && imageInput.files.length > 0) {
            const file = imageInput.files[0];
            
            // Validate file size (5MB limit)
            if (file.size > 5 * 1024 * 1024) {
                $('#image_error').text('File size must be less than 5MB');
                $('#image_upload').addClass('is-invalid');
                isValid = false;
            }
            
            // Validate file type (JPG/PNG only)
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                $('#image_error').text('Only JPG and PNG formats are allowed');
                $('#image_upload').addClass('is-invalid');
                isValid = false;
            }
            
            // Clear previous errors if validation passes
            if (isValid) {
                $('#image_error').text('');
                $('#image_upload').removeClass('is-invalid');
            }
        }
   
        // Validate gallery image sizes
        const galleryInput = $('#gallery_images')[0];
        if (galleryInput && galleryInput.files.length > 0) {
            for (let i = 0; i < galleryInput.files.length; i++) {
                if (galleryInput.files[i].size > 5 * 1024 * 1024) {
                    $('#gallery_error').text('One or more gallery images exceed 5MB limit');
                    $('#gallery_images').addClass('is-invalid');
                    isValid = false;
                    break;
                }
            }
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error if any
            $('html, body').animate({
                scrollTop: $('.is-invalid:first').offset().top - 100
            }, 500);
        }
    });
    
    // Preview functionality
    $('#previewBtn').click(function() {
        // Get form values
        const itemType = $('input[name="item_type"]:checked').val();
        const name = $('#name').val();
        const code = $('#code').val();
        const category = $('#category_id option:selected').text();
        const sellingPrice = $('#selling_price').val();
        const purchasePrice = $('#purchase_price').val();
        const quantity = $('#quantity').val();
        const unit = $('#unit_id option:selected').text();
        const discountType = $('#discount_type option:selected').text();
        const alertQuantity = $('#alert_quantity').val();
        const tax = $('#tax_id option:selected').text();
        const description = quill.root.innerHTML;
        
        // Get image preview
        const imagePreview = $('#image_preview').attr('src');
        
        // Set preview values
        $('#previewItemType').text(itemType == 1 ? 'Product' : 'Service');
        $('#previewName').text(name || 'Not provided');
        $('#previewCode').text(code || 'Not provided');
        $('#previewCategory').text(category || 'Not selected');
        $('#previewSellingPrice').text(sellingPrice ? '$' + sellingPrice : 'Not provided');
        $('#previewPurchasePrice').text(purchasePrice ? '$' + purchasePrice : 'Not provided');
        $('#previewQuantity').text(quantity || 'Not provided');
        $('#previewUnit').text(unit || 'Not selected');
        $('#previewDiscountType').text(discountType || 'Not selected');
        $('#previewAlertQuantity').text(alertQuantity || 'Not provided');
        $('#previewTax').text(tax || 'Not selected');
        $('#previewDescription').html(description || 'No description provided');
        
        // Set image preview
        if (imagePreview) {
            $('#previewImage').attr('src', imagePreview);
        } else {
            $('#previewImage').attr('src', 'assets/img/products/default.png');
        }
        
        // Show gallery images in preview
        const galleryPreview = $('#previewGallery');
        galleryPreview.empty();
        
        // Existing gallery images
        $('.gallery-img').each(function() {
            const imgSrc = $(this).find('img').attr('src');
            if (imgSrc) {
                galleryPreview.append(`
                    <div class="avatar avatar-xl border p-1">
                        <img src="${imgSrc}" style="width:80px; height:80px; object-fit:cover;" alt="Gallery Image">
                    </div>
                `);
            }
        });
        
        // New gallery images
        const galleryInput = $('#gallery_images')[0];
        if (galleryInput && galleryInput.files.length > 0) {
            for (let i = 0; i < galleryInput.files.length; i++) {
                const file = galleryInput.files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    galleryPreview.append(`
                        <div class="avatar avatar-xl border p-1">
                            <img src="${e.target.result}" style="width:80px; height:80px; object-fit:cover;" alt="New Gallery Image">
                        </div>
                    `);
                }
                reader.readAsDataURL(file);
            }
        }
        
        // Show/hide product-specific fields based on item type
        if (itemType == 1) {
            $('.product-preview').show();
            $('.stock-preview').show();
        } else {
            $('.product-preview').hide();
            $('.stock-preview').hide();
        }
        
        // Show the modal
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        previewModal.show();
    });
});
    </script>
    <script>
        // Function to handle gallery image deletion
function deleteGalleryImage(imageId, element) {
    // Add the ID to the deleted_images hidden field
    let deletedIds = $('#deleted_images').val();
    if (deletedIds) {
        deletedIds += ',' + imageId;
    } else {
        deletedIds = imageId;
    }
    $('#deleted_images').val(deletedIds);
    
    // Remove the image element from the DOM
    $(element).closest('.gallery-img').remove();
}

// Handle click on existing trash icons
$(document).on('click', '.gallery-trash', function() {
    const imgDiv = $(this).closest('.gallery-img');
    if (imgDiv.data('id')) {
        deleteGalleryImage(imgDiv.data('id'), this);
    } else {
        // For newly added images that haven't been saved yet
        imgDiv.remove();
    }
});
</script>
<script>
    // Update your image removal function to this:
$(document).on('click', '#remove_image', function() {
    $('#image_preview').attr('src', 'assets/img/products/default.png');
    $('#remove_main_image').val('1'); // This tells PHP to delete the image
    
    // Clear any selected file but keep the input element
    $('#image_upload').val('').removeClass('is-invalid');
    $('#image_error').text('');
});
</script>
<script>
$(document).ready(function () {
    function toggleStockFields() {
        const itemType = $('input[name="item_type"]:checked').val();
        if (itemType === '1') {
            $('.stock-only').show();   // Product → show
        } else {
            $('.stock-only').hide();   // Service → hide
        }
    }

    // Run on page load
    toggleStockFields();

    // Run when item_type changes
    $('input[name="item_type"]').on('change', toggleStockFields);
});
</script>

</body>
</html>