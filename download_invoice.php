<?php
session_start();
if(empty($_SESSION['logged-in']) || empty($_SESSION['user_id'])){
    header('location: sign-in.php');
    exit();
}

$order_id= $_GET['order_id'] ?? '';
if((int)$order_id<1 || !ctype_digit((string)$order_id) || empty($order_id)){
    header('location: index.php');
    exit();
}
try{
    require_once __DIR__ .'/config.php';
    require_once __DIR__ . '/vendor/autoload.php';
    $user_id=$_SESSION['user_id'];
    $sql='SELECT order_id,customer_name,email,grand_total,payment_method from tbl_orders where order_id=? AND user_id=?';
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('ii',$order_id,$user_id);
    $stmt->execute();
    $orderdetails = $stmt->get_result()->fetch_assoc();
    if(!$orderdetails){
        http_response_code(404);
        exit();
    }
    $stmt->close();
    
    if($orderdetails['payment_method']!=='cod'){
        header('location: index.php');
        exit();
    }
    $sql='SELECT p.name,c.color_name,s.size_name, oi.sku_id, oi.quantity, oi.price
    FROM tbl_order_items oi
    JOIN product_skus sk ON oi.sku_id=sk.sku_id
          JOIN tbl_product p ON sk.product_id=p.id
          JOIN tbl_color c on sk.color_id= c.color_id
          JOIN tbl_size s on sk.size_id=s.size_id
          where oi.order_id=?';
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $invoicedetails=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
   
    if(!$invoicedetails){
        http_response_code(404);
        exit();
    }
    $dompdf = new \Dompdf\Dompdf();
    $html = '
    <p>Customer Name: ' . htmlspecialchars($orderdetails['customer_name']) . '</p>
    <p>Email: ' . htmlspecialchars($orderdetails['email']) . '</p>
    
    ';
    $html .= '
    <h1>Almuznah</h1>
    <h2>INVOICE</h2>
    <p>Invoice Number: INV-' . str_pad($orderdetails['order_id'], 6, '0', STR_PAD_LEFT) . '</p>
    ';
    $html .= '
        <table border="1" cellpadding="2" cellspacing="0" width="100%">
            <thead>
                <tr>
                <th>Product</th>
                <th>Color</th>
                <th>Size</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
         </thead>
        <tbody>
        

';

foreach ($invoicedetails as $invoice) {
    $html .= '
       
            <tr>
                <td>' . htmlspecialchars($invoice['name'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($invoice['color_name'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . htmlspecialchars($invoice['size_name'], ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . (int)$invoice['sku_id'] . '</td>
                <td>' . (int)$invoice['quantity'] . '</td>
                <td>' . number_format((float)$invoice['price'], 2) . '</td>
                <td>' . number_format((float)$invoice['price'] * (int)$invoice['quantity'], 2) . '</td>
            </tr>
        
    ';
}
$subtotal = 0;

foreach ($invoicedetails as $invoice) {
    $subtotal += (float)$invoice['price'] * (int)$invoice['quantity'];
}
$html .= '
        </tbody>
    </table>

    <div class="summary">
        <p>Subtotal: AED ' . number_format($subtotal, 2) . '</p>

        <h3>
            Grand Total: AED ' .
            number_format((float)$orderdetails['grand_total'], 2) . '
        </h3>
    </div>
';


$html .= '
        </tbody>
    </table>
';
$html = '
<html>
<head>
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
    }

    h1, h2,h3{
        text-align: center;
    }
   
    table {
        border-collapse: collapse;
        margin-top: 20px;
    }

    th {
        background-color: #eeeeee;
    }

    th, td{
        border: 1px solid #a02626;
        padding: 8px;
        text-align: left;
    }
    .summary {
    width: 40%;
    margin-left: auto;
    margin-top: 20px;
    text-align: right;
}

    .summary p {
        font-size: 13px;
    }
        

.summary h3 {
    background-color: #eeeeee;
    padding: 12px;
    color: #2563a6;
    font-size: 16px;
} 
</style>
</head>
<body>
' . $html . '
</body>
</html>';
$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream('invoice.pdf', ['Attachment' => false]);



exit();
    
    
    






}catch(throwable $e){
    error_log($e->getMessage());
    http_response_code(500);
    exit();
}




?>