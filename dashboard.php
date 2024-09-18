<?php
session_start();
include 'db.php'; // Include the database connection

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the garages owned by the logged-in user
$owner_query = "SELECT * FROM garages WHERE owner_id = '$user_id'";
$owner_result = $conn->query($owner_query);

// Fetch pending booking requests for the owner's garages
$booking_query = "
    SELECT bookings.*, users.username, garages.headline
    FROM bookings
    JOIN garages ON bookings.garage_id = garages.id
    JOIN users ON bookings.user_id = users.id
    WHERE garages.owner_id = '$user_id' AND bookings.status = 'pending'";
$booking_result = $conn->query($booking_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header class="top-bar">
        <h1>Owner Dashboard</h1>
        <nav class="top-bar-nav">
            <a href="home.php" class="top-bar-btn">Home</a>
            <a href="add_garage.php" class="top-bar-btn">Add a Garage</a>
            <a href="logout.php" class="top-bar-btn">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Your Listed Garages</h2>
        <section class="garages-list">
            <?php
            if ($owner_result->num_rows > 0) {
                while ($garage = $owner_result->fetch_assoc()) {
                    echo "<div class='garage-item'>";
                    echo "<h3>" . htmlspecialchars($garage['headline']) . "</h3>"; // Display headline instead of name
                    echo "<p>Location: " . htmlspecialchars($garage['location']) . "</p>";
                    echo "<p>Size: " . htmlspecialchars($garage['size']) . "</p>";
                    echo "<p>Price: per month/ TK " . htmlspecialchars($garage['price']) . "</p>";
                    echo "<p>Status: " . htmlspecialchars($garage['status']) . "</p>";

                    // Status update form
                    echo "<form method='POST' action='update_status.php' style='display:inline;'>";
                    echo "<input type='hidden' name='garage_id' value='" . htmlspecialchars($garage['id']) . "'>";
                    echo "<select name='status'>";
                    echo "<option value='available'" . ($garage['status'] === 'available' ? ' selected' : '') . ">Available</option>";
                    echo "<option value='rented'" . ($garage['status'] === 'rented' ? ' selected' : '') . ">Rented</option>";
                    echo "</select>";
                    echo "<button type='submit'>Update Status</button>";
                    echo "</form>";

                    // Delete button
                    echo "<form method='POST' action='delete_garage.php' style='display:inline;'>";
                    echo "<input type='hidden' name='garage_id' value='" . htmlspecialchars($garage['id']) . "'>";
                    echo "<button type='submit' onclick='return confirm(\"Are you sure you want to delete this garage?\")'>Delete</button>";
                    echo "</form>";

                    echo "</div>";
                }
            } else {
                echo "<p>You have no garages listed.</p>";
            }
            ?>
        </section>

        <h2>Pending Booking Requests</h2>
        <section class="booking-requests">
            <?php
            if ($booking_result->num_rows > 0) {
                while ($booking = $booking_result->fetch_assoc()) {
                    echo "<div class='booking-request'>";
                    echo "<p><strong>Requester:</strong> " . htmlspecialchars($booking['username']) . "</p>";
                    echo "<p><strong>Garage:</strong> " . htmlspecialchars($booking['headline']) . "</p>";
                    echo "<p><strong>Requested On:</strong> " . htmlspecialchars($booking['booking_date']) . "</p>";
                    echo "<p><strong>Start Date:</strong> " . htmlspecialchars($booking['start_date']) . "</p>";
                    echo "<p><strong>End Date:</strong> " . htmlspecialchars($booking['end_date']) . "</p>";
                    echo "<p><strong>Status:</strong> " . htmlspecialchars($booking['status']) . "</p>";
                    // Add action buttons for accepting or rejecting the booking
                    echo "<form method='POST' action='process_booking.php'>";
                    echo "<input type='hidden' name='booking_id' value='" . htmlspecialchars($booking['id']) . "'>";
                    echo "<button type='submit' name='action' value='accept'>Accept</button>";
                    echo "<button type='submit' name='action' value='reject'>Reject</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>No pending booking requests.</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>