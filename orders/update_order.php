<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "ordering_system");

$id = $_POST['id'];
$status = $_POST['status'];

$sql = "UPDATE orders
SET status='$status'
WHERE id='$id'";

if($conn->query($sql)){

    echo json_encode([
        "status" => true,
        "message" => "Order Updated"
    ]);

}else{

    echo json_encode([
        "status" => false
    ]);

}

?>