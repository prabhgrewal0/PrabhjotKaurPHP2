<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "feedback";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM book_reviews WHERE id = '$id'";
        $conn->query($sql);
    } elseif (isset($_POST['add'])) {
        $item_name = $_POST['item_name'];
        $brand = $_POST['brand'];
        $year = $_POST['year'];
        $edition = $_POST['edition'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        // Handle file upload
        $target_dir = "img/";
        $target_file = $target_dir . basename($_FILES["Acc_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["Acc_image"]["tmp_name"], $target_file)) {
            $book_image = $target_file;
            $sql = "INSERT INTO feedback_reviews (item_name, brand, year, edition, rating, review, Acc_image) VALUES ('$item_name', '$brand', '$year', '$edition', '$rating', '$review', '$Acc_image')";
            $conn->query($sql);
        } else {
            echo "Error uploading the image.";
        }
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $item_name = $_POST['item_name'];
        $brand = $_POST['brand'];
        $year = $_POST['year'];
        $edition = $_POST['edition'];
        $rating = $_POST['rating'];
        $review = $_POST['review'];
        $Acc_image = $_POST['Acc_image'];

        $sql = "UPDATE book_reviews SET item_name='$item_name', brand='$brand', year='$year', edition='$edition', rating='$rating', review='$review', Acc_image='$Acc_image' WHERE id='$id'";
        $conn->query($sql);
    }
}

$sql = "SELECT * FROM feedback_reviews";
$result = $conn->query($sql);

if (!$result) {
    echo "Error: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function editReview(id, item_name, brand, year, edition, rating, review, Acc_image) {
            document.getElementById("editForm").style.display = "block";
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_item_name").value = item_name;
            document.getElementById("edit_brand").value = brand;
            document.getElementById("edit_year").value = year;
            document.getElementById("edit_edition").value = edition;
            document.getElementById("edit_rating").value = rating;
            document.getElementById("edit_review").value = review;
            document.getElementById("edit_Acc_image").value = Acc_image;
        }
    </script>
</head>
<body>
    <nav>
        <div class="brand-logo">
            <a href="index.php">Feedback</a>
        </div>
        <a href="index.php">Login</a>
        <a href="content.php">View Content</a>
    </nav>

    <form action="content.php" method="POST" enctype="multipart/form-data">
        <label for="add_review" class=addreview>ADD REVIEW</label>
        <label for="item_image">Item Image:</label>
        <input type="file" name="Acc_image" id="Acc_image" required>
        <br>
        <label for="item_name">Item Name:</label>
        <input type="text" name="item_name" id="item_name" required>
        <br>
        <label for="brand">Brand:</label>
        <input type="text" name="author" id="author" required>
        <br>
        <label for="year">Year:</label>
        <input type="number" name="year" id="year" required>
        <br>
        <label for="edition">Edition:</label>
        <input type="text" name="edition" id="edition" required>
        <br>
        <label for="rating">Rating:</label>
        <input type="number" name="rating" id="rating" step="0.1" min="0" max="10" required>
        <br>
        <label for="review">Review:</label>
        <textarea name="review" id="review" required></textarea>
        <br>
        <input type="submit" name="add" value="Add Review">
    </form>
    <form id="editForm" action="content.php" method="POST" style="display: none;">
            <input type="hidden" name="id" id="edit_id">
            <label for="edit_item_image">Item Image URL:</label>
            <input type="text" name="item_image" id="edit_item_image" required>
            <br>
            <label for="edit_item_name">Item Name:</label>
            <input type="text" name="item_name" id="edit_item_name" required>
            <br>
            <label for="edit_brand">Brand:</label>
            <input type="text" name="brand" id="edit_brand" required>
            <br>
            <label for="edit_year">Year:</label>
            <input type="number" name="year" id="edit_year" required>
            <br>
            <label for="edit_edition">Edition:</label>
            <input type="text" name="edition" id="edit_edition" required>
            <br>
            <label for="edit_rating">Rating:</label>
            <input type="number" name="rating" id="edit_rating" step="0.1" min="0" max="10" required>
            <br>
            <label for="edit_review">Review:</label>
            <textarea name="review" id="edit_review" required></textarea>
            <br>
            <input type="submit" name="edit" value="Edit Review">
        </form>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='review-container'>";
                echo "<div class='button-wrapper'>";
                echo "<button class='edit-button' onclick=\"editReview('" . $row['id'] . "','" . addslashes($row['item_name']) . "','" . addslashes($row['brand']) . "','" . $row['year'] . "','" . addslashes($row['edition']) . "','" . $row['rating'] . "','" . addslashes($row['review']) . "','" . $row['Acc_image'] . "')\">Edit</button>";
                echo "</div>"; // Close the button-wrapper div
                echo "<div class='review-image'><img src='" . $row['item_image'] . "' alt='" . $row['item_name'] . "' width='200'></div>";
                echo "<div class='review-content'>";
                echo "<h2>" . $row['item_name'] . "</h2>";
                echo "<p><strong>Author:</strong> " . $row['brand'] . "</p>";
                echo "<p><strong>Year:</strong> " . $row['year'] . "</p>";
                echo "<p><strong>Edition:</strong> " . $row['edition'] . "</p>";
                echo "<p><strong>Rating:</strong> " . $row['rating'] . "/10</p>";
                echo "<p><strong>Review:</strong> " . $row['review'] . "</p>";
                echo "</div>"; // Close the review-content div
                
                // delet button wrap
                echo "<div class='delete-button-wrapper'>";
                echo "<form action='content.php' method='POST'>";
                echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                echo "<input class='delete-button' type='submit' name='delete' value='Delete'>";
                echo "</form>";
                echo "</div>"; // Close 
                
                echo "</div>";            
            }
        } else {
            echo "No reviews posted yet!";
        }
        $conn->close();
        ?>
    </body>
</html>
