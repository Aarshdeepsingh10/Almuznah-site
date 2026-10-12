<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $conn = new mysqli("localhost", "root", "", "user", 3307);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT id, email, password FROM tbl_almuzah_register WHERE email = ? AND password = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        echo "Login successful";

        $_SESSION['email'] = $user['email'];
       
        $_SESSION['logged-in'] = true;
        $_SESSION['user_id'] = $user['id'];

        header("Location: dashboard.php");
        exit();

    } else {

        echo "Invalid email or password";
    }

    $stmt->close();
    $conn->close();
}

?>