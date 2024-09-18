<?php
session_start();
include 'db.php'; // Include the database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the booking ID and action are set
if (isset($_POST['booking_id']) && isset($_POST['action'])) {
    $booking_id = intval($_POST['booking_id']);
    $action = $_POST['action'];

    // Validate action: only "accept" or "reject" are allowed
    if ($action === 'accept') {
        $new_status = 'confirmed';
        $garage_status = 'rented'; // Set garage status to rented
    } elseif ($action === 'reject') {
        $new_status = 'cancelled';
        $garage_status = null; // No change to garage status
    } else {
        echo "Invalid action.";
        exit;
    }

    // Update the booking status in the database
    $update_booking_query = "UPDATE bookings SET status = '$new_status' WHERE id = '$booking_id'";
    if ($conn->query($update_booking_query) === TRUE) {
        // If the booking was accepted, update the garage status
        if ($garage_status) {
            $booking_query = "SELECT garage_id FROM bookings WHERE id = '$booking_id'";
            $booking_result = $conn->query($booking_query);

            if ($booking_result->num_rows == 1) {
                $booking = $booking_result->fetch_assoc();
                $garage_id = intval($booking['garage_id']);

                $update_garage_query = "UPDATE garages SET status = '$garage_status' WHERE id = '$garage_id'";
                $conn->query($update_garage_query);
            }
        }
        header("Location: dashboard.php"); // Redirect back to dashboard after action
        exit;
    } else {
        echo "Error updating booking status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>