<?php

header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "ordering_system");

$id = $_POST['id'];

$sql = "DELETE FROM orders WHERE id='$id'";

if($conn->query($sql)){

    echo json_encode([
        "status" => true,
        "message" => "Deleted"
    ]);

}else{

    echo json_encode([
        "status" => false
    ]);

}

?>