<?php
session_start();
include 'db.php'; // Include the database connection

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle acknowledgment of a booking request
if (isset($_POST['acknowledge_id'])) {
    $acknowledge_id = mysqli_real_escape_string($conn, $_POST['acknowledge_id']);

    // Update the booking status to 'acknowledged'
    $update_query = "UPDATE bookings SET status = 'acknowledged' WHERE id = '$acknowledge_id' AND user_id = '$user_id'";
    $conn->query($update_query);

    // Redirect to avoid resubmitting the form
    header("Location: user_dashboard.php");
    exit;
}

// Fetch the booking requests made by the logged-in user, excluding 'acknowledged' requests
$booking_query = "
    SELECT bookings.*, garages.headline, garages.location, garages.price, garages.city
    FROM bookings
    JOIN garages ON bookings.garage_id = garages.id
    WHERE bookings.user_id = '$user_id' AND bookings.status <> 'acknowledged'";
$booking_result = $conn->query($booking_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header class="top-bar">
        <h1>User Dashboard</h1>
        <nav class="top-bar-nav">
            <a href="home.php" class="top-bar-btn">Home</a>
            <a href="logout.php" class="top-bar-btn">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Your Booking Requests</h2>
        <section class="booking-requests">
            <?php
            if ($booking_result->num_rows > 0) {
                while ($booking = $booking_result->fetch_assoc()) {
                    $status_class = 'status-' . htmlspecialchars($booking['status']);
                    echo "<div class='booking-request'>";
                    echo "<h3>Garage: " . htmlspecialchars($booking['headline']) . "</h3>";
                    echo "<p><strong>Location:</strong> " . htmlspecialchars($booking['location']) . ", " . htmlspecialchars($booking['city']) . "</p>";
                    echo "<p><strong>Price:</strong> $" . htmlspecialchars($booking['price']) . "</p>";
                    echo "<p><strong>Start Date:</strong> " . htmlspecialchars($booking['start_date']) . "</p>";
                    echo "<p><strong>End Date:</strong> " . htmlspecialchars($booking['end_date']) . "</p>";
                    echo "<p><strong>Status:</strong> <span class='$status_class'>" . htmlspecialchars($booking['status']) . "</span></p>";

                    // Show OK button if the status is 'confirmed', 'cancelled', or 'acknowledged'
                    if (in_array($booking['status'], ['confirmed', 'cancelled', 'acknowledged'])) {
                        echo "<form method='POST' action='user_dashboard.php'>";
                        echo "<input type='hidden' name='acknowledge_id' value='" . htmlspecialchars($booking['id']) . "'>";
                        echo "<button type='submit' class='ok-button'>OK</button>";
                        echo "</form>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>You have not made any booking requests.</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>