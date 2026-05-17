<?php
header("Content-Type: application/json");

include '../db.php';

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];

$sql = "UPDATE products
SET name='$name', price='$price'
WHERE id='$id'";

if($conn->query($sql)){
    echo json_encode([
        "status" => "updated"
    ]);
}else{
    echo json_encode([
        "status" => "failed"
    ]);
}
?>
