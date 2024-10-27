<?php
require "conndb.php";

$titre = '';
$nbrPage = '';
$description = '';
$image = '';
$categorie_name = '';

$edit_id = '';
$edit_titre = '';
$edit_nbrPage = '';
$edit_description = '';
$edit_categorie_name = '';

//================= Insert =================
if (isset($_POST['add'])) {
    $titre = $_POST['titre'];
    $nbrPage = $_POST['nbrPage'];
    $description = $_POST['description'];
    $categorie_name = $_POST['categorie'];

    // File upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $image = $_FILES['image']['name'];

    // Check if file is a valid image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        echo "<script> Swal.fire({ title: 'Error!', text: 'File is not an image!', icon: 'error', confirmButtonText: 'Ok' }); </script>";
        exit;
    }

    // Move uploaded file
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        echo "<script> Swal.fire({ title: 'Error!', text: 'File upload failed!', icon: 'error', confirmButtonText: 'Ok' }); </script>";
        exit;
    }

    // Validate category existence
    $categorie_query = "SELECT id FROM Catégorie WHERE nom = ?";
    $categorie_stmt = $connection->prepare($categorie_query);
    $categorie_stmt->bind_param("s", $categorie_name);
    $categorie_stmt->execute();
    $categorie_result = $categorie_stmt->get_result();

    if ($categorie_result->num_rows === 0) {
        echo "<script> Swal.fire({ title: 'Error!', text: 'Selected category does not exist!', icon: 'error', confirmButtonText: 'Ok' }); </script>";
        exit;
    }

    $row = $categorie_result->fetch_assoc();
    $categorie_id = $row['id'];

    // Insert into database
    $sql = "INSERT INTO livre (titre, pages, description, image, categorie_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ssssi", $titre, $nbrPage, $description, $image, $categorie_id);

    if ($stmt->execute()) {
        echo "<script> Swal.fire({ title: 'Success!', text: 'New record added successfully!', icon: 'success', showConfirmButton: false, timer: 1500 }).then(function() { location.reload(); }); </script>";
    } else {
        echo "<script> Swal.fire({ title: 'Error!', text: 'Error adding record: " . $connection->error . "', icon: 'error', confirmButtonText: 'Ok' }); </script>";
    }

    // Close statement
    $stmt->close();
}

//====== Update ========
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    // Fetch book data based on ID
    $sql = "SELECT titre, pages, description, categorie_id FROM livre WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Initialize variables with the book data to edit
        $edit_titre = $row['titre'];
        $edit_nbrPage = $row['pages'];
        $edit_description = $row['description'];
        $edit_categorie_id = $row['categorie_id'];

        // Fetch category name from category ID
        $categorie_query = "SELECT nom FROM Catégorie WHERE id = ?";
        $categorie_stmt = $connection->prepare($categorie_query);
        $categorie_stmt->bind_param("i", $edit_categorie_id);
        $categorie_stmt->execute();
        $categorie_result = $categorie_stmt->get_result();

        if ($categorie_result->num_rows > 0) {
            $categorie_row = $categorie_result->fetch_assoc();
            $edit_categorie_name = $categorie_row['nom'];
        }
    }
    // Close statements
    $stmt->close();
    $categorie_stmt->close();
}

if (isset($_POST['edit'])) {
    $edit_titre = $_POST['titre'];
    $edit_nbrPage = $_POST['nbrPage'];
    $edit_description = $_POST['description'];
    $edit_categorie_name = $_POST['categorie'];

    // Update the corresponding record in the database
    $update_query = "UPDATE livre SET titre = ?, pages = ?, description = ?, categorie_id = (SELECT id FROM Catégorie WHERE nom = ?) WHERE id = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("ssssi", $edit_titre, $edit_nbrPage, $edit_description, $edit_categorie_name, $edit_id);

    if ($stmt->execute()) {
        // Display success message
        echo "<script> Swal.fire({ title: 'Success!', text: 'Record updated successfully!', icon: 'success', showConfirmButton: false, timer: 1500 }).then(function() { location.reload(); }); </script>";
    } else {
        // Display error message
        echo "<script> Swal.fire({ title: 'Error!', text: 'Error updating record: " . $connection->error . "', icon: 'error', confirmButtonText: 'Ok' }); </script>";
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-rq0U8I7ImjgtzhWrhcL8iRxj0sBW4quUK9tR9T2ejRtOjBlW8Rt6Em/kBDZUzE+W" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/5hb6bA67y/N7EzPNEZ+F+d8ZxABtZC8FZlwqQ3" crossorigin="anonymous"></script>
    <style>
        #logo {
            position: absolute;
            top: 0; 
            right: 0; 
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand text-secondary" href="#">KB Livres</a>
        <img id="logo" width="111px" src="./uploads/logo3.jpg">
    </div>
</nav>
<div class="container">
    <h2 class="text-center">Gestion des Livres</h2>
    <button class="btn btn-info" type="button" data-bs-toggle="modal" data-bs-target="#addBookModal">Ajouter un Nouveau Livre</button>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Veuillez saisir les informations du livre :</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="index.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" name="titre" required>
                        </div>
                        <div class="mb-3">
                            <label for="nbrPage" class="form-label">Nombre de pages</label>
                            <input type="number" class="form-control" name="nbrPage" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="categorie" class="form-label">Catégorie</label>
                            <select name="categorie" class="form-control" required>
                                <option value="">Sélectionner une catégorie</option>
                                <?php
                                $categorie_sql = "SELECT nom FROM Catégorie";
                                $categorie_result = $connection->query($categorie_sql);
                                while ($categorie_row = $categorie_result->fetch_assoc()) {
                                    echo '<option value="' . $categorie_row['nom'] . '">' . $categorie_row['nom'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required>
                        </div>
                        <button type="submit" name="add" class="btn btn-success">Ajouter le Livre</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editBookForm" method="POST" action="update.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="bookId" value="">
                    <div class="mb-3">
                        <label for="titre" class="form-label">Titre</label>
                        <input type="text" class="form-control" name="titre" id="titre" required>
                    </div>
                    <div class="mb-3">
                        <label for="pages" class="form-label">Pages</label>
                        <input type="number" class="form-control" name="pages" id="pages" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" name="image" id="image">
                    </div>
                    <div class="mb-3">
                        <label for="categorie" class="form-label">Catégorie</label>
                        <select class="form-select" name="categorie_id" id="categorie" required>
                            <?php
                            $categories = $connection->query("SELECT * FROM Catégorie");
                            while ($cat = $categories->fetch_assoc()) {
                                echo "<option value='{$cat['id']}'>{$cat['nom']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Enregistrer les modifications </button>
                </form>
            </div>
        </div>
    </div>
</div>



<table class="table table-striped">
    <thead>
        <tr>
            <th>Titre</th>
            <th>Pages</th>
            <th>Description</th>
            <th>Image</th>
            <th>Catégorie</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT livre.id, titre, pages, livre.description, image, Catégorie.nom AS categorie_name 
                FROM livre 
                JOIN Catégorie ON livre.categorie_id = Catégorie.id";
        $result = $connection->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['titre']}</td>
                    <td>{$row['pages']}</td>
                    <td>{$row['description']}</td>
                    <td><img src='uploads/{$row['image']}' alt='Image' width='100'></td>
                    <td>{$row['categorie_name']}</td>
                    <td>
                        <a href='index.php?id={$row['id']}' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#editBookModal'>Modifier</a>
                        <a href='delete.php?id={$row['id']}' class='btn btn-danger'> </a>
                    </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

</div>
<script>
$(document).ready(function() {
    $('.btn-warning').on('click', function() {
        var id = $(this).attr('href').split('=')[1]; 
        $.ajax({
            url: 'fetch_book.php', 
            type: 'GET',
            data: { id: id },
            success: function(response) {
                var book = JSON.parse(response);
                $('#bookId').val(book.id);
                $('#titre').val(book.titre);
                $('#pages').val(book.pages);
                $('#description').val(book.description);
                $('#categorie').val(book.categorie_id);
                $('#editBookModal').modal('show');
            }
        });
    });
});

</script>
</body>
</html>
