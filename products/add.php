<?php
header("Content-Type: application/json");

include '../db.php';

$name = $_POST['name'];
$price = $_POST['price'];

$sql = "INSERT INTO products(name, price)
VALUES('$name','$price')";

if($conn->query($sql)){
    echo json_encode([
        "status" => "success"
    ]);
}else{
    echo json_encode([
        "status" => "failed"
    ]);
}
?>