
<?php
session_start();

if (
    empty($_SESSION['logged-in']) ||
    empty($_SESSION['user_id'])
) {
    header('Location: ../sign-in.php');
    exit();
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: addproducts.php');
    exit();
}

require_once __DIR__ . '/../config.php';

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$summary = trim($_POST['summary'] ?? '');
$value = $_POST['value'] ?? '';

$colors = $_POST['colors'] ?? [];
$sizes = $_POST['sizes'] ?? [];

$stock = filter_var(
    $_POST['stock_quantity'] ?? null,
    FILTER_VALIDATE_INT
);

if (
    $name === '' ||
    $description === '' ||
    $summary === '' ||
    !is_numeric($value) ||
    !is_finite((float) $value) ||
    (float) $value <= 0 ||
    (float) $value > 999999999999.99 ||
    $stock === false ||
    $stock === null ||
    $stock < 0 ||
    $stock > 100000 ||
    !is_array($colors) ||
    !is_array($sizes) ||
    empty($colors) ||
    empty($sizes)
) {
    header('Location: addproducts.php?error=' . urlencode(
        'Enter valid details and select at least one color and size.'
    ));
    exit();
}

$colors = array_values(array_unique($colors));
$sizes = array_values(array_unique($sizes));

foreach (array_merge($colors, $sizes) as $id) {
    if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0) {
        header('Location: addproducts.php?error=' . urlencode(
            'Invalid color or size selection.'
        ));
        exit();
    }
}

$colors = array_map('intval', $colors);
$sizes = array_map('intval', $sizes);

if (
    count($colors) > 100 ||
    count($sizes) > 100 ||
    count($colors) * count($sizes) > 1000
) {
    header('Location: addproducts.php?error=' . urlencode(
        'Too many variants selected.'
    ));
    exit();
}

if (
    !isset($_FILES['cover']) ||
    $_FILES['cover']['error'] !== UPLOAD_ERR_OK ||
    $_FILES['cover']['size'] > 5 * 1024 * 1024
) {
    header('Location: addproducts.php?error=' . urlencode(
        'Upload an image smaller than 5 MB.'
    ));
    exit();
}

$tmpPath = $_FILES['cover']['tmp_name'];
$imageInfo = getimagesize($tmpPath);

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

$mimeType = $imageInfo['mime'] ?? '';

if ($imageInfo === false || !isset($allowedTypes[$mimeType])) {
    header('Location: addproducts.php?error=' . urlencode(
        'Upload a valid JPG, PNG, or WebP image.'
    ));
    exit();
}

$uploadDirectory = __DIR__ . '/../images/products/';

if (
    !is_dir($uploadDirectory) &&
    !mkdir($uploadDirectory, 0755, true)
) {
    header('Location: addproducts.php?error=' . urlencode(
        'Could not create the product image folder.'
    ));
    exit();
}

$fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
$coverPath = 'images/products/' . $fileName;
$fullPath = $uploadDirectory . $fileName;

$uploaded = false;
$transactionStarted = false;

try {
    foreach ([
        ['table' => 'tbl_color', 'column' => 'color_id', 'ids' => $colors],
        ['table' => 'tbl_size', 'column' => 'size_id', 'ids' => $sizes]
    ] as $selection) {
        $placeholders = implode(
            ', ',
            array_fill(0, count($selection['ids']), '?')
        );

        $sql = "SELECT {$selection['column']}
                FROM {$selection['table']}
                WHERE {$selection['column']} IN ($placeholders)";

        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($selection['ids']));
        $stmt->bind_param($types, ...$selection['ids']);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows !== count($selection['ids'])) {
            $stmt->close();
            throw new Exception('Invalid color or size selected.');
        }

        $stmt->close();
    }

    if (!move_uploaded_file($tmpPath, $fullPath)) {
        throw new Exception('Image upload failed.');
    }

    $uploaded = true;

    $conn->begin_transaction();
    $transactionStarted = true;

    $price = (float) $value;

    $stmt = $conn->prepare(
        "INSERT INTO tbl_product
            (name, description, summory, cover, value)
         VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        'ssssd',
        $name,
        $description,
        $summary,
        $coverPath,
        $price
    );

    $stmt->execute();
    $productId = $conn->insert_id;
    $stmt->close();

    $stmt = $conn->prepare(
        "INSERT INTO product_skus
            (product_id, color_id, size_id, stock_quantity)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($colors as $colorId) {
        foreach ($sizes as $sizeId) {
            $stmt->bind_param(
                'iiii',
                $productId,
                $colorId,
                $sizeId,
                $stock
            );

            $stmt->execute();
        }
    }

    $stmt->close();

    $conn->commit();
    $transactionStarted = false;

    $conn->close();

    header('Location: addproducts.php?success=1');
    exit();

} catch (Throwable $e) {
    if ($transactionStarted) {
        $conn->rollback();
    }

    if ($uploaded && file_exists($fullPath)) {
        unlink($fullPath);
    }

    error_log($e->getMessage());
    $conn->close();

    header('Location: addproducts.php?error=' . urlencode(
        'Product could not be saved. Check the PHP error log and database structure.'
    ));
    exit();
}
