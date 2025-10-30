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
    <style>
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
                    <div class="col-md-10 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6><a href="products.php"><i class="isax isax-arrow-left me-2"></i>Products</a></h6>
                                <a href="#" class="btn btn-outline-white d-inline-flex align-items-center"><i class="isax isax-eye me-1"></i>Preview</a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="mb-3">Basic Details</h6>
                                    <form id="productForm" action="process/action_edit_product.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="current_image" value="<?= $row['product_img'] ?>">
                                        <input type="hidden" name="remove_main_image" id="remove_main_image" value="0">
                                        
                                        <!-- Product Image Field -->
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

                                        <!-- Item Type Field -->
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

                                        <!-- Main Form Fields -->
                                        <div class="row gx-3">
                                            <!-- Name Field -->
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
                                        <textarea name="description" id="productDescription" class="form-control d-none"><?= htmlspecialchars($row['description']) ?></</textarea>
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
            
            // Clear previous previews (optional)
            // preview.empty();
            
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

    // Optional: block paste
    // $('#selling_price, #purchase_price, #quantity, #alert_quantity, #name, #code').on('paste', function (e) {
    //     e.preventDefault();
    // });

        // Form validation
       $('#productForm').submit(function(e) {
        let isValid = true;

        // Reset errors
        $('.error-message').text('');
        $('.is-invalid');

        // Required field checks
        // if (!$('input[name="item_type"]:checked').length) {
        //     $('#item_type_error').text('Please select item type');
        //     isValid = false;
        // }

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
        // if (!$('#image_upload').val()) {
        //         $('#image_error').text('Please upload a product image.');
        //         valid = false;
        //     }

        if ($('#category_id').val() === '') {
            $('#category_error').text('Category is required');
            $('#category_id').addClass('is-invalid');
            isValid = false;
        }

        // if ($('#selling_price').val().trim() === '' || isNaN($('#selling_price').val())) {
        //     $('#selling_price_error').text('Valid selling price is required');
        //     $('#selling_price').addClass('is-invalid');
        //     isValid = false;
        // }

        // if ($('#purchase_price').val().trim() === '' || isNaN($('#purchase_price').val())) {
        //     $('#purchase_price_error').text('Valid purchase price is required');
        //     $('#purchase_price').addClass('is-invalid');
        //     isValid = false;
        // }

        // if ($('#quantity').val().trim() === '' || isNaN($('#quantity').val())) {
        //     $('#quantity_error').text('Valid quantity is required');
        //     $('#quantity').addClass('is-invalid');
        //     isValid = false;
        // }

        // if ($('#unit_id').val() === '') {
        //     $('#unit_error').text('Unit is required');
        //     $('#unit_id').addClass('is-invalid');
        //     isValid = false;
        // }

        // if ($('#discount_type').val() === '') {
        //     $('#discount_type_error').text('Discount type is required');
        //     $('#discount_type').addClass('is-invalid');
        //     isValid = false;
        // }

        // if ($('#alert_quantity').val().trim() === '' || isNaN($('#alert_quantity').val())) {
        //     $('#alert_quantity_error').text('Valid alert quantity is required');
        //     $('#alert_quantity').addClass('is-invalid');
        //     isValid = false;
        // }

        // if ($('#tax_id').val() === '') {
        //     $('#tax_error').text('Tax is required');
        //     $('#tax_id').addClass('is-invalid');
        //     isValid = false;
        // }

        // Validate main image size
        // const imageInput = $('#image_upload')[0];
        // if (imageInput && imageInput.files.length > 0) {
        //     const file = imageInput.files[0];
        //     if (file.size > 5 * 1024 * 1024) {
        //         $('#image_error').text('File size must be less than 5MB');
        //         $('#image_upload').addClass('is-invalid');
        //         isValid = false;
        //     }
        // }
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