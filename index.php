<?php
session_start();

$errMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_or_email']) && !empty($_POST['passwd'])) {
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "Feedback";

    // Connect to the database
    $db_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    // Check connection
    if ($db_conn->connect_error) {
        die("Connection failed: " . $db_conn->connect_error);
    }

    $user_or_email = $_POST['user_or_email'];
    $passwd = $_POST['passwd'];

    $query = "SELECT * FROM users WHERE (username = '$user_or_email' OR email = '$user_or_email') AND password = '$passwd'";
    $res = $db_conn->query($query);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        header("Location: content.php");
        exit;
    } else {
        $errMsg = "Invalid username/email or password.";
    }

    $db_conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <div class="brand-logo">
        <a href="index.php">FEEDBACK</a>
        </div>
        <a href="index.php">Login</a>
        <a href="content.php">View Content</a>
    </nav>

    <?php
    if (!empty($errMsg)) {
        echo "<p style='color: red;'>" . $errMsg . "</p>";
    }
    ?>
    <div>
    <br>
    <form class="loginform" action="index.php" method="POST">
        <label for="user_or_email">Username or Email:</label>
        <input type="text" name="user_or_email" id="user_or_email" required>
        <br>
        <label for="passwd">Password:</label>
        <input type="password" name="passwd" id="passwd" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <br>
    <form class="signup" action="signup.php">
            <br>
     <input type="submit" value="Sign Up">
    </form>
    </div>
</body>
</html>
