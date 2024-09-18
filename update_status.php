<?php
session_start();
include 'db.php'; // Include the database connection

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if garage_id and status are set
if (isset($_POST['garage_id']) && isset($_POST['status'])) {
    $garage_id = (int) $_POST['garage_id'];
    $status = $conn->real_escape_string($_POST['status']);

    // Ensure the garage belongs to the logged-in user
    $check_ownership_query = "SELECT * FROM garages WHERE id = '$garage_id' AND owner_id = '$user_id'";
    $check_result = $conn->query($check_ownership_query);

    if ($check_result->num_rows > 0) {
        // Update the garage status
        $update_query = "UPDATE garages SET status = '$status' WHERE id = '$garage_id'";
        if ($conn->query($update_query) === TRUE) {
            header("Location: dashboard.php?message=Status+updated+successfully");
        } else {
            echo "Error updating status: " . $conn->error;
        }
    } else {
        echo "You do not have permission to update this garage.";
    }
} else {
    echo "No garage ID or status provided.";
}
?>