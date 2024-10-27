<?php
include 'conndb.php'; 

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT livre.id, titre, pages, description, image, categorie_id 
            FROM livre 
            WHERE livre.id = ?";
    
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode([]);
    }
}
?>
