<!-- login.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Garage Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Login</h2>
        
        <!-- PHP Code for Handling Login -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include 'db.php'; // Include the database connection file

            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $password = $_POST['password'];

            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    // Redirect to the home page
                    header("Location: home.php");
                    exit;
                } else {
                    echo "<p style='color: red;'>Incorrect password. <a href='login.php'>Try again</a></p>";
                }
            } else {
                echo "<p style='color: red;'>No account found with that email. <a href='signup.php'>Sign up here</a></p>";
            }

            $conn->close();
        }
        ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
        </form>
    </div>
</body>
</html>