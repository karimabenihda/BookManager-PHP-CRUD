<?php
include 'conndb.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $titre = $_POST['titre'];
    $pages = $_POST['pages'];
    $description = $_POST['description'];
    $categorie_id = $_POST['categorie_id'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $sql = "UPDATE livre SET titre = ?, pages = ?, description = ?, image = ?, categorie_id = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("siisii", $titre, $pages, $description, $image, $categorie_id, $id);
    } else {
        $sql = "UPDATE livre SET titre = ?, pages = ?, description = ?, categorie_id = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("siisi", $titre, $pages, $description, $categorie_id, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}
?>
