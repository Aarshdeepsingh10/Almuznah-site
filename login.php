
<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {
        require_once __DIR__ . '/config.php';

        $email = $_POST["email"];
        $password = $_POST["password"];

        $sql = "SELECT id, email, password FROM tbl_almuzah_register WHERE email = ? AND password = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            $_SESSION['email'] = $user['email'];
            $_SESSION['logged-in'] = true;
            $_SESSION['user_id'] = $user['id'];

            $stmt->close();
            $conn->close();

            header("Location: shop.php");
            exit();

        } else {
            echo "Invalid email or password";
        }

        $stmt->close();
        $conn->close();

    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());

        http_response_code(500);
        exit("Something went wrong. Please try again later.");
    }
}
?>
