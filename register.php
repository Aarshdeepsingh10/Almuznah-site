<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("This page must be accessed through the form.");
}

$conn = new mysqli("localhost", "root", "", "user", 3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$username = $_POST["username"];
$lastname= $_POST["lastname"];
$contact =$_POST["contact"];
$email=$_POST["email"];
$password = $_POST["password"];
$confirm_password = $_POST['confirm_password'];

if ($password !== $confirm_password) {
    die("Passwords do not match");
}

$sql = "INSERT INTO tbl_almuzah_register ( username,lastname,contact,email, password) VALUES (?, ?,?,?,?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sssss",  $username,$lastname,$contact,$email,$password);

if ($stmt->execute()) {
    echo "User registered successfully";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>