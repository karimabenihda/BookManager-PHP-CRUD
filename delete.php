<?php
require 'conndb.php';

$id = $_GET["id"];
$sql = "DELETE FROM livre WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $id); 

if ($stmt->execute()) {
    header("location:index.php");
} else {
    echo "Error deleting record: " . $connection->error;
    exit;
}
?>
