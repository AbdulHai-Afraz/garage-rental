<!-- manage_garages.php -->
<?php
session_start();
include 'db.php'; // Include the database connection

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php"); // Redirect if not logged in or not an owner
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all garages listed by the owner
$garages_query = "SELECT * FROM garages WHERE owner_id='$user_id'";
$result = $conn->query($garages_query);

// Handle garage removal
if (isset($_GET['remove_id'])) {
    $remove_id = (int)$_GET['remove_id'];
    $conn->query("DELETE FROM garages WHERE id='$remove_id' AND owner_id='$user_id'");

    // Check if the owner has any garages left
    $checkGarages = "SELECT * FROM garages WHERE owner_id='$user_id'";
    $garageResult = $conn->query($checkGarages);
    if ($garageResult->num_rows == 0) {
        // Revert role to 'user' if no more garages
        $conn->query("UPDATE users SET role='user' WHERE id='$user_id'");
        $_SESSION['role'] = 'user'; // Update session role
    }

    header("Location: manage_garages.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Garages - Garage Rental System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Your Listed Garages</h2>
    <div class="garages-list">
        <?php
        if ($result->num_rows > 0) {
            while ($garage = $result->fetch_assoc()) {
                echo "<div class='garage-item'>";
                echo "<h3>" . htmlspecialchars($garage['name']) . "</h3>";
                echo "<p>Location: " . htmlspecialchars($garage['location']) . "</p>";
                echo "<p>Size: " . htmlspecialchars($garage['size']) . "</p>";
                echo "<p>Price: $" . htmlspecialchars($garage['price']) . "</p>";
                echo "<a href='manage_garages.php?remove_id=" . $garage['id'] . "' onclick='return confirm(\"Are you sure you want to remove this garage?\")'>Remove</a>";
                echo "</div>";
            }
        } else {
            echo "<p>You have no garages listed.</p>";
        }
        ?>
    </div>
</body>
</html>