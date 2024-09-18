<?php
session_start();
include 'db.php'; // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href='login.php'>log in</a> to request a booking.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the garage ID from the query string
if (isset($_GET['garage_id'])) {
    $garage_id = mysqli_real_escape_string($conn, $_GET['garage_id']);
    
    // Fetch the garage details
    $query = "SELECT * FROM garages WHERE id = '$garage_id'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        $garage = $result->fetch_assoc();
    } else {
        echo "<p>Garage not found.</p>";
        exit;
    }
} else {
    echo "<p>No garage ID specified.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Details</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header class="top-bar">
        <h1>Garage Details</h1>
        <?php if (isset($_SESSION['user_id'])) { ?>
            <nav class="top-bar-nav">
                <a href="home.php" class="top-bar-btn">Home</a>
                <a href="dashboard.php" class="top-bar-btn">Dashboard</a>
                <a href="logout.php" class="top-bar-btn">Logout</a>
            </nav>
        <?php } ?>
    </header>

    <main>
        <section class="garage-details">
            <h2><?php echo htmlspecialchars($garage['headline']); ?></h2>
            <div class="garage-images">
                <?php if (!empty($garage['image1'])) { ?>
                    <img src="<?php echo htmlspecialchars($garage['image1']); ?>" alt="Image 1" class="garage-image">
                <?php } ?>
                <?php if (!empty($garage['image2'])) { ?>
                    <img src="<?php echo htmlspecialchars($garage['image2']); ?>" alt="Image 2" class="garage-image">
                <?php } ?>
                <?php if (!empty($garage['image3'])) { ?>
                    <img src="<?php echo htmlspecialchars($garage['image3']); ?>" alt="Image 3" class="garage-image">
                <?php } ?>
            </div>
            <div class="garage-info">
                <p><strong>Owner Name:</strong> <?php echo htmlspecialchars($garage['name']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($garage['location']); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($garage['city']); ?></p>
                <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($garage['phone_number']); ?></p>
                <p><strong>Additional Info:</strong> <?php echo htmlspecialchars($garage['additional_info']); ?></p>
                <p><strong>Price:</strong> per month / TK<?php echo htmlspecialchars($garage['price']); ?></p>
            </div>

            <?php if ($garage['owner_id'] != $user_id) { ?>
                <!-- Booking Request Form -->
                <form method="POST" action="request_booking.php">
                    <input type="hidden" name="garage_id" value="<?php echo $garage['id']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <button type="submit">Request to Rent</button>
                </form>
            <?php } else { ?>
                <p><em>You are the owner of this garage and cannot send a booking request.</em></p>
            <?php } ?>
        </section>
    </main>
</body>
</html>