<!-- signup.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Garage Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        
        <!-- PHP Code for Handling Signup -->
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            include 'db.php'; // Include the database connection file

            // Validate and sanitize input
            $username = mysqli_real_escape_string($conn, trim($_POST['username']));
            $email = mysqli_real_escape_string($conn, trim($_POST['email']));
            $password = trim($_POST['password']);

            // Basic server-side validation
            if (empty($username) || empty($email) || empty($password)) {
                echo "<p style='color: red;'>All fields are required. <a href='signup.php'>Try again</a></p>";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<p style='color: red;'>Invalid email format. <a href='signup.php'>Try again</a></p>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash password

                // Check if the email already exists
                $checkEmail = "SELECT * FROM users WHERE email='$email'";
                $result = $conn->query($checkEmail);

                if ($result->num_rows > 0) {
                    echo "<p style='color: red;'>Email already exists. <a href='signup.php'>Try again</a></p>";
                } else {
                    // Insert new user into the database
                    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<p style='color: green;'>New account created successfully. <a href='login.php'>Login here</a></p>";
                    } else {
                        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                    }
                }
                $conn->close();
            }
        }
        ?>

        <!-- Signup Form -->
        <form action="signup.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</body>
</html>