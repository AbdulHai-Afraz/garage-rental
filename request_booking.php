<?php
session_start();
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $garage_id = mysqli_real_escape_string($conn, $_POST['garage_id']);
    
    // Check if this user already has a pending booking for this garage
    $check_query = "SELECT * FROM bookings WHERE user_id = '$user_id' AND garage_id = '$garage_id' AND status = 'pending'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Redirect with an error message
        header("Location: home.php?message=You+already+have+a+pending+booking+request+for+this+garage.");
        exit;
    }

    // Insert the booking request into the database
    $insert_query = "INSERT INTO bookings (user_id, garage_id, booking_date, status) VALUES ('$user_id', '$garage_id', NOW(), 'pending')";
    
    if ($conn->query($insert_query) === TRUE) {
        // Redirect to home.php with a success message
        header("Location: home.php?message=Booking+request+sent+successfully!+Please+wait+for+the+owner's+confirmation.");
        exit;
    } else {
        // Redirect with an error message
        header("Location: home.php?message=Error:+". urlencode($conn->error));
        exit;
    }
} else {
    // Redirect with an invalid request message
    header("Location: home.php?message=Invalid+request.");
    exit;
}
?>