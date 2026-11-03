
<?php
require_once __DIR__ . '/config.php';

$colorQuery = "SELECT color_id, color_name FROM tbl_color";
$colorResult = $conn->query($colorQuery);

$sizeQuery = "SELECT size_id, size_name FROM tbl_size";
$sizeResult = $conn->query($sizeQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="cddr">

                    <form
                        method="POST"
                        action="save-product.php"
                        enctype="multipart/form-data"
                        novalidate
                    >

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4>Add New Product</h4>
                                <p>You can see all sales analysis results more clearly and completely</p>
                            </div>

                            <div>
                                <span>cancel</span>
                                <span>save</span>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-12 col-xl-4">

                                <div class="mb-4">
                                    <div class="card myy-card">
                                        <div class="card-header">Thumbnail</div>

                                        <div class="card-body text-center">
                                            <div class="llct">
                                                <img
                                                    id="thumbnail-preview"
                                                    src=""
                                                    class="img-fluid"
                                                    alt="Product thumbnail preview"
                                                >
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            Set the product thumbnail image.
                                            PNG, JPG, and JPEG files are accepted.
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="card myy-card">
                                        <div class="card-header">Add Image</div>

                                        <div class="card-body">
                                            <input
                                                type="file"
                                                class="form-control"
                                                id="product-image"
                                                name="cover"
                                                accept=".png,.jpg,.jpeg"
                                                required
                                            >
                                        </div>

                                        <div class="card-footer">
                                            Less than 5 MB
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="card myy-card summary-card">
                                        <div class="card-header">Add Summary</div>

                                        <div class="card-body">
                                            <textarea
                                                class="form-control summary-input"
                                                name="summary"
                                                maxlength="250"
                                            ></textarea>
                                        </div>

                                        <div class="card-footer">
                                            Maximum 250 characters
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-12 col-xl-8">

                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <button
                                            class="nav-link active"
                                            id="general-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#general"
                                            type="button"
                                        >
                                            General
                                        </button>
                                    </li>

                                    <li class="nav-item">
                                        <button
                                            class="nav-link"
                                            id="advance-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#advance"
                                            type="button"
                                        >
                                            Advance
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content p-3">

                                    <div
                                        class="tab-pane fade show active"
                                        id="general"
                                        role="tabpanel"
                                        aria-labelledby="general-tab"
                                    >

                                        <div class="lddr">
                                            <h4>General</h4>
                                            <hr>

                                            <div class="mb-3">
                                                <label for="product-name" class="form-label">
                                                    Product Name
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="product-name"
                                                    name="name"
                                                    required
                                                >
                                            </div>

                                            <div class="mb-3">
                                                <label for="description" class="form-label">
                                                    Description
                                                </label>

                                                <textarea
                                                    class="form-control"
                                                    id="description"
                                                    name="description"
                                                    required
                                                ></textarea>
                                            </div>
                                        </div>

                                        <div class="lddr">
                                            <h4>Pricing</h4>
                                            <hr>

                                            <div class="mb-3">
                                                <label for="base-price" class="form-label">
                                                    Base Price (AED)
                                                </label>

                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    id="base-price"
                                                    name="value"
                                                    min="1"
                                                    step="0.1"
                                                    required
                                                >
                                            </div>
                                        </div>

                                    </div>

                                    <div
                                        class="tab-pane fade"
                                        id="advance"
                                        role="tabpanel"
                                        aria-labelledby="advance-tab"
                                    >

                                        <div class="lddr">
                                            <h4>Colors</h4>
                                            <hr>

                                            <?php while ($color = $colorResult->fetch_assoc()) { ?>

                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="colors[]"
                                                        value="<?php echo $color['color_id']; ?>"
                                                        id="color-<?php echo $color['color_id']; ?>"
                                                    >

                                                    <label
                                                        class="form-check-label"
                                                        for="color-<?php echo $color['color_id']; ?>"
                                                    >
                                                        <?php echo htmlspecialchars($color['color_name']); ?>
                                                    </label>
                                                </div>

                                            <?php } ?>
                                        </div>

                                        <div class="lddr mt-4">
                                            <h4>Sizes</h4>
                                            <hr>

                                            <?php while ($size = $sizeResult->fetch_assoc()) { ?>

                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="sizes[]"
                                                        value="<?php echo $size['size_id']; ?>"
                                                        id="size-<?php echo $size['size_id']; ?>"
                                                    >

                                                    <label
                                                        class="form-check-label"
                                                        for="size-<?php echo $size['size_id']; ?>"
                                                    >
                                                        <?php echo htmlspecialchars($size['size_name']); ?>
                                                    </label>
                                                </div>

                                            <?php } ?>
                                        </div>

                                        <div class="lddr mt-4">
                                            <h4>Stock</h4>
                                            <hr>

                                            <div class="mb-3">
                                                <label for="stock-quantity" class="form-label">
                                                    Stock Quantity
                                                </label>

                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    id="stock-quantity"
                                                    name="stock_quantity"
                                                    min="0"
                                                    step="1"
                                                    required
                                                >
                                            </div>
                                        </div>

                                    </div>

                                </div>

                                <button class="btn btn-danger mt-3" type="submit">
                                    Submit
                                </button>

                            </div>

                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('product-image');
        const thumbnail = document.getElementById('thumbnail-preview');

        imageInput.addEventListener('change', function () {
            const file = imageInput.files[0];

            if (file) {
                thumbnail.src = URL.createObjectURL(file);
            } else {
                thumbnail.removeAttribute('src');
            }
        });

        const productForm = document.querySelector('form');

        productForm.addEventListener('submit', function (event) {
            const requiredFields = productForm.querySelectorAll(
                '#general [required], #advance [required], #product-image'
            );

            for (const field of requiredFields) {
                if (!field.checkValidity()) {
                    event.preventDefault();

                    const tabPane = field.closest('.tab-pane');

                    if (tabPane) {
                        const tabButton = document.querySelector(
                            '[data-bs-target="#' + tabPane.id + '"]'
                        );

                        bootstrap.Tab.getOrCreateInstance(tabButton).show();
                    }

                    field.focus();
                    field.reportValidity();

                    return;
                }
            }

            const colors = productForm.querySelectorAll(
                'input[name="colors[]"]:checked'
            );

            const sizes = productForm.querySelectorAll(
                'input[name="sizes[]"]:checked'
            );

            if (colors.length === 0 || sizes.length === 0) {
                event.preventDefault();

                bootstrap.Tab.getOrCreateInstance(
                    document.getElementById('advance-tab')
                ).show();

                alert('Please select at least one color and one size.');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
