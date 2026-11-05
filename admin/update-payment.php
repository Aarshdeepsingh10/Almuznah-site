<?php
session_start();
if( $_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['logged-in']) || empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin'){
    header('location: /../index.php');
    exit();
}

try{
    require_once __DIR__ . '/../config.php';
    $order_id = $_POST['order_id'] ?? '';
    if($order_id === '' || !ctype_digit($order_id) || (int)$order_id <= 0){
        http_response_code(400);
        exit('invalid order id');
    }
    $payment_status = $_POST['payment_status'] ?? '';
    $order_status= $_POST['order_status'] ?? '';

    $sql= 'update tbl_orders set
    payment_status=?,order_status=? where order_id=?';
    
    $stmt= $conn->prepare($sql);
    
    $stmt->bind_param('ssi',$payment_status,$order_status,$order_id);
    
    $stmt->execute();
    $stmt->close();
    
    $sql = 'UPDATE tbl_payments SET
   payment_status=? WHERE order_id=?';

   $stmt = $conn->prepare($sql);
   $stmt->bind_param('si', $payment_status, $order_id);
   $stmt->execute();
   $stmt->close();
   $conn->close();
   echo 'Payment status updated successfully';
   header('location:orders.php');


}catch(mysqli_sql_exception $e){
    error_log($e->getMessage());
    http_response_code(500);
    exit('something went wrong. Please try again later.');

}
