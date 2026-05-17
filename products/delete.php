<?php
header("Content-Type: application/json");

include '../db.php';

$id = $_POST['id'];

$sql = "DELETE FROM products WHERE id='$id'";

if($conn->query($sql)){
    echo json_encode([
        "status" => "deleted"
    ]);
}else{
    echo json_encode([
        "status" => "failed"
    ]);
}
?>