<?php include 'layouts/session.php'; ?>
<?php include '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'layouts/title-meta.php'; ?>
    <?php include 'layouts/head-css.php'; ?>
    <!-- Preview Modal CSS -->
    <!-- <style>
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
    </style> -->
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
    .preview-image-container {
        width: 120px;
        height: 120px;
        border: 1px dashed #dee2e6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        overflow: hidden;
    }
    .preview-image {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
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
</style>
</head>

<body>
    <div class="main-wrapper">
        <?php include 'layouts/menu.php'; ?>

        <div class="page-wrapper">
            <div class="content">
                <div class="row">
                    <div class="col-md-12 mx-auto">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6>Add Products</h6>
                                <a href="javascript:void(0);" class="btn btn-outline-white d-inline-flex align-items-center" id="previewBtn">
                                    <i class="isax isax-eye me-1"></i>Preview</a>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form action="process/action_add_product.php" method="POST" id="form" enctype="multipart/form-data">
                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['crm_user_id'] ?? 1; ?>">
                                        <div class="row gx-3">
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <span class="text-gray-9 fw-bold mb-2 d-flex">Product Image</span>
                                                    <div class="d-flex align-items-center">
                                                        <div id="add_image_preview" class="avatar avatar-xxl border border-dashed bg-light me-3 flex-shrink-0">
                                                            <i class="isax isax-image text-primary fs-24"></i>
                                                        </div>
                                                        <div class="d-inline-flex flex-column align-items-start">
                                                            <div class="drag-upload-btn btn btn-sm btn-primary position-relative mb-2">
                                                                <i class="isax isax-image me-1"></i>Upload Image
                                                                <input type="file" name="product_img" id="add_image" class="form-control image-sign" accept="image/*">
                                                            </div>
                                                            <span id="add_image_error" class="text-danger error-text fs-12"></span>
                                                            <span class="text-gray-9 fs-12">JPG or PNG format, not exceeding 5MB.</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Item Type</label>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="form-check me-3">
                                                            <input class="form-check-input" value="1" type="radio" name="item_type"  id="product_type" checked>
                                                            <label class="form-check-label">Product</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" value="0" type="radio" name="item_type" id="service_type">
                                                            <label class="form-check-label">Service</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                                                    <input type="text" name="name" id="name" class="form-control no-numbers">
                                                    <span class="text-danger error-text" id="name_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">HSN Code<span class="text-danger ms-1">*</span></label>
                                                    <input type="text" name="code" id="code" class="form-control">
                                                    <span class="text-danger error-text" id="code_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Category<span class="text-danger ms-1">*</span></label>
                                                    <select class="form-select" name="category_id" id="category_id">
                                                        <option value="">Select Category</option>
                                                        <?php $result = mysqli_query($conn, "SELECT * FROM category WHERE status=1");
                                                        while ($row = mysqli_fetch_assoc($result)) {
                                                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                                        } ?>
                                                    </select>
                                                    <span class="text-danger error-text" id="category_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 product-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Selling Price</label>
                                                    <input type="text" name="selling_price" id="selling_price" class="form-control">
                                                    <span class="text-danger error-text" id="selling_price_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label ">Purchase Price</label>
                                                    <input type="text" name="purchase_price" id="purchase_price" class="form-control">
                                                    <span class="text-danger error-text" id="purchase_price_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="text" name="quantity" id="quantity" class="form-control">
                                                    <span class="text-danger error-text" id="quantity_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Units</label>
                                                    <select class="form-select" name="unit_id" id="unit_id">
                                                        <option value="">Select unit</option>
                                                        <?php $unit = mysqli_query($conn, "SELECT * FROM units WHERE status=1");
                                                        while ($row = mysqli_fetch_assoc($unit)) {
                                                            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                                        } ?>
                                                    </select>
                                                    <span class="text-danger error-text" id="unit_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 product-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Discount Type</label>
                                                    <select class="form-select" name="discount_type" id="discount_type">
                                                        <option value="">Select</option>
                                                        <option value="%">%</option>
                                                        <option value="fixed">Fixed</option>
                                                    </select>
                                                    <span class="text-danger error-text" id="discount_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 product-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Tax</label>
                                                    <select class="form-select" name="tax_id"  id="tax_id">
                                                        <option value="">Select</option>
                                                        <?php 
                                                        $tax = mysqli_query($conn, "SELECT * FROM tax WHERE status=1");
                                                        while ($row = mysqli_fetch_assoc($tax)) {
                                                            echo '<option value="' . $row['id'] . '" data-rate="' . $row['rate'] . '">'
                                                                . $row['name'] . ' (' . $row['rate'] . '%)</option>';
                                                        }
                                                        ?>

                                                    </select>
                                                    <span class="text-danger error-text" id="tax_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 stock-only">
                                                <div class="mb-3">
                                                    <label class="form-label">Alert Quantity</label>
                                                    <input type="text" name="alert_quantity" id="alert_quantity" class="form-control">
                                                    <span class="text-danger error-text" id="alert_quantity_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 product-only">
                                        <div class="mb-3">
                                            <label class="form-label">Product Description</label>

                                            <!-- Quill Editor Container -->
                                            <div id="editorContainer" style="height: 200px; background-color: #fff;"></div>

                                            <!-- Hidden Textarea to store HTML content on submit -->
                                            <textarea name="description" id="productDescription" class="form-control d-none"></textarea>
                                        </div>
                                    </div>

                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <button type="button" class="btn btn-outline-white" onclick="window.location.href='products.php'">Cancel</button>
                                            <button type="submit" name="product" class="btn btn-primary">Create New</button>
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
                                        <i class="isax isax-image text-primary fs-24"></i>
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
                                        <label class="form-label preview-label">Name<span class="text-danger ms-1">*</span></label>
                                        <div class="preview-field" id="previewName"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">HSN Code<span class="text-danger ms-1">*</span></label>
                                        <div class="preview-field" id="previewCode"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Category<span class="text-danger ms-1">*</span></label>
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
                                        <label class="form-label preview-label">Units</label>
                                        <div class="preview-field" id="previewUnit"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Discount Type</label>
                                        <div class="preview-field" id="previewDiscountType"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Tax</label>
                                        <div class="preview-field" id="previewTax"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 stock-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Alert Quantity</label>
                                        <div class="preview-field" id="previewAlertQuantity"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12 product-preview">
                                    <div class="mb-3">
                                        <label class="form-label preview-label">Product Description</label>
                                        <div class="preview-quill" id="previewDescription"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-primary" onclick="document.getElementById('form').submit()">Create Product</button> -->
                </div>
            </div>
        </div>
    </div>

    <?php include 'layouts/vendor-scripts.php'; ?>
<!-- Include Quill JS if not already -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
var quill = new Quill('#editorContainer', {
    theme: 'snow'
});

$('#form').on('submit', function (e) {
    const html = quill.root.innerHTML.trim();
    $('#productDescription').val(html);
});

// Preview functionality
// Preview functionality
document.getElementById('previewBtn').addEventListener('click', function() {
    // Get form values
    const itemType = document.querySelector('input[name="item_type"]:checked').value;
    const name = document.getElementById('name').value;
    const code = document.getElementById('code').value;
    const categorySelect = document.getElementById('category_id');
    const category = categorySelect.options[categorySelect.selectedIndex].text;
    const sellingPrice = document.getElementById('selling_price').value;
    const purchasePrice = document.getElementById('purchase_price').value;
    const quantity = document.getElementById('quantity').value;
    const unitSelect = document.getElementById('unit_id');
    const unit = unitSelect.options[unitSelect.selectedIndex].text;
    const discountTypeSelect = document.getElementById('discount_type');
    const discountType = discountTypeSelect.options[discountTypeSelect.selectedIndex].text;
    const taxSelect = document.getElementById('tax_id');
    const tax = taxSelect.options[taxSelect.selectedIndex].text;
    const alertQuantity = document.getElementById('alert_quantity').value;
    const description = quill.root.innerHTML;
    
    // Get image preview
    const imagePreview = document.querySelector('#add_image_preview img');
    const imageSrc = imagePreview ? imagePreview.src : '';
    
    // Set preview values
    document.getElementById('previewItemType').textContent = itemType == 1 ? 'Product' : 'Service';
    document.getElementById('previewName').textContent = name || 'Not provided';
    document.getElementById('previewCode').textContent = code || 'Not provided';
    document.getElementById('previewCategory').textContent = category || 'Not selected';
    document.getElementById('previewSellingPrice').textContent = sellingPrice ? '$' + sellingPrice : 'Not provided';
    document.getElementById('previewPurchasePrice').textContent = purchasePrice ? '$' + purchasePrice : 'Not provided';
    document.getElementById('previewQuantity').textContent = quantity || 'Not provided';
    document.getElementById('previewUnit').textContent = unit || 'Not selected';
    document.getElementById('previewDiscountType').textContent = discountType || 'Not selected';
    document.getElementById('previewTax').textContent = tax || 'Not selected';
    document.getElementById('previewAlertQuantity').textContent = alertQuantity || 'Not provided';
    document.getElementById('previewDescription').innerHTML = description || 'No description provided';
    
    // Show/hide product-specific fields based on item type
    if (itemType == 1) {
        document.querySelectorAll('.product-preview').forEach(el => el.style.display = 'block');
        document.querySelectorAll('.stock-preview').forEach(el => el.style.display = 'block');
    } else {
        document.querySelectorAll('.product-preview').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.stock-preview').forEach(el => el.style.display = 'none');
    }
    
    // Set image preview
    const previewImageContainer = document.getElementById('preview_image_container');
    if (imageSrc) {
        previewImageContainer.innerHTML = `
            <div class="preview-image-container">
                <img src="${imageSrc}" class="preview-image" alt="Preview">
            </div>
        `;
    } else {
        previewImageContainer.innerHTML = `
            <div class="preview-image-container">
                <i class="isax isax-image text-primary fs-24"></i>
            </div>
        `;
    }
    
    // Show the modal
    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();
});

// Toggle fields based on item type in the actual form
$(document).ready(function () {
    function toggleProductFields() {
        const itemType = $('input[name="item_type"]:checked').val();
        if (itemType === '1') {
            $('.product-only').show();
            $('.stock-only').show();
        } else {
            $('.product-only').hide();
            $('.stock-only').hide();
        }
    }

    // Run on page load
    toggleProductFields();

    // Run when item_type is changed
    $('input[name="item_type"]').on('change', toggleProductFields);
});
</script>

<script>
    // Allow only numbers and dot in numeric fields
    $('#selling_price, #purchase_price, #quantity, #alert_quantity, #code').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    // Disallow digits in product name
    $('#name').on('input', function () {
        this.value = this.value.replace(/[0-9]/g, '');
    });

    $(document).ready(function () {
        $('#form').on('submit', function (e) {
            let valid = true;
            $('.error-text').text('');

            // Image validation
            // if (!$('#add_image').val()) {
            //     $('#add_image_error').text('Please upload a product image.');
            //     valid = false;
            // }

            // Text inputs
            if (!$('#name').val().trim()) {
                $('#name_error').text('Product name is required.');
                valid = false;
            }

            if (!$('#code').val().trim()) {
                $('#code_error').text('Product HSNcode is required.');
                valid = false;
            }

            // Dropdowns
            if (!$('#category_id').val()) {
                $('#category_error').text('Please select a category.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.error-text:contains("required")').first().offset().top - 100
                }, 400);
            }
        });
    });
</script>


<script>
$(document).ready(function () {
    $('#add_image').change(function () {
        const file = this.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                $('#add_image_error').text('File size must be less than 5MB');
                $(this).val('');
                $('#add_image_preview').empty();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $('#add_image_preview').html(`
                    <img src="${e.target.result}" class="avatar avatar-xl border rounded" alt="Preview" style="max-width: 150px;">
                `);
                $('#add_image_error').text('');
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>

<script>
    $(document).ready(function () {
    // Load category on page load (default = product)
    loadCategory(1);

    // On radio change
    $('input[name="item_type"]').change(function () {
        let itemType = $(this).val();
        loadCategory(itemType);
    });

    function loadCategory(type) {
        $.ajax({
            url: 'process/get_categories_by_type.php',
            type: 'POST',
            data: { category_type: type },
            success: function (response) {
                $('#category_id').html(response);
            }
        });
    }
});
</script>

</body>
</html>