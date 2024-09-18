<?php
session_start();
include 'db.php'; // Include the database connection

// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if garage_id is set
if (isset($_POST['garage_id'])) {
    $garage_id = (int) $_POST['garage_id'];

    // Ensure the garage belongs to the logged-in user
    $check_ownership_query = "SELECT * FROM garages WHERE id = '$garage_id' AND owner_id = '$user_id'";
    $check_result = $conn->query($check_ownership_query);

    if ($check_result->num_rows > 0) {
        // Delete the garage
        $delete_query = "DELETE FROM garages WHERE id = '$garage_id'";
        if ($conn->query($delete_query) === TRUE) {
            header("Location: dashboard.php?message=Garage+deleted+successfully");
        } else {
            echo "Error deleting garage: " . $conn->error;
        }
    } else {
        echo "You do not have permission to delete this garage.";
    }
} else {
    echo "No garage ID provided.";
}
?>