<?php

session_start();

if (!isset($_SESSION["logged-in"]) || $_SESSION["logged-in"] !== true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['cart_id'])) {

    $conn = new mysqli("localhost", "root", "", "user", 3307);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $cart_id = (int) $_POST['cart_id'];
    $user_id = (int) $_SESSION["user_id"];

    $sql = "DELETE FROM tbl_cart
            WHERE cart_id = ? AND user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: shopcart.php");
    exit();
}

echo "Invalid request";

?>