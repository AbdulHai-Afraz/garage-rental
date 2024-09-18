<?php
session_start();
include 'db.php'; // Include the database connection

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit;
}
$user_id = $_SESSION['user_id'];
$query = "SELECT username FROM users WHERE id = '$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$username = $user['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $headline = mysqli_real_escape_string($conn, $_POST['headline']);
    $name = $username;
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $size = implode(', ', $_POST['size']); // Join selected sizes into a comma-separated string
    $price = (float)$_POST['price'];
    $additional_info = mysqli_real_escape_string($conn, $_POST['additional_info']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $owner_id = $_SESSION['user_id'];

    // Handle file uploads
    $targetDir = "uploads/garages/";
    $imagePaths = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    // Ensure the uploads directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        $totalImages = count($_FILES['images']['name']);

        if ($totalImages > 3) {
            echo "<p style='color: red;'>You can only upload a maximum of 3 images.</p>";
            exit;
        }

        for ($i = 0; $i < $totalImages; $i++) {
            $imageTmpName = $_FILES['images']['tmp_name'][$i];
            $imageType = mime_content_type($imageTmpName);

            // Validate file type
            if (!in_array($imageType, $allowedTypes)) {
                echo "<p style='color: red;'>Only JPG and PNG images are allowed.</p>";
                exit;
            }

            // Define the new image name and path
            $imageName = uniqid() . "-" . basename($_FILES['images']['name'][$i]);
            $targetFilePath = $targetDir . $imageName;

            if (move_uploaded_file($imageTmpName, $targetFilePath)) {
                $imagePaths[] = $targetFilePath; // Store the image path
            } else {
                echo "<p style='color: red;'>There was an error uploading the image.</p>";
                exit;
            }
        }
    }

    // Insert data into the garages table
    $image1 = $imagePaths[0] ?? null;
    $image2 = $imagePaths[1] ?? null;
    $image3 = $imagePaths[2] ?? null;

    $sql = "INSERT INTO garages (owner_id, headline, name, location, city, size, price, additional_info, phone_number, image1, image2, image3) 
            VALUES ('$owner_id', '$headline', '$name', '$location', '$city', '$size', '$price', '$additional_info', '$phone_number', '$image1', '$image2', '$image3')";

    if ($conn->query($sql) === TRUE) {
        // Update user's role to 'owner'
        $updateRole = "UPDATE users SET role='owner' WHERE id='$owner_id'";
        $conn->query($updateRole);

        echo "<p style='color: green;'>Garage added successfully.</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Garage - Garage Rental System</title>
    <link rel="stylesheet" href="add_garage.css">
</head>
<body>
    <header class="top-bar">
        <h1>RentMyGarage</h1>
        <nav class="top-bar-nav">
            <a href="home.php" class="top-bar-btn">Home</a>
            <a href="logout.php" class="top-bar-btn">Logout</a>
        </nav>
    </header>

    <main>
        <h2>Add a New Garage</h2>
        <form action="add_garage.php" method="POST" enctype="multipart/form-data" class="garage-form">
            <!-- Headline Input -->
            <label for="headline">Headline:</label>
            <input type="text" name="headline" id="headline" placeholder="Enter Headline" required>

            <!-- Area Selection -->
            <label for="location">Select Area:</label>
            <select name="location" id="location" required>
                <option value="" disabled selected>Select Area</option>
                <option value="Kalabagan">Kalabagan</option>
                <option value="Dhanmondi">Dhanmondi</option>
                <option value="Mirpur">Mirpur</option>
                <option value="Gulshan">Gulshan</option>
                <!-- Add more options as needed -->
            </select>

            <!-- City Selection -->
            <label for="city">Select City:</label>
            <select name="city" id="city" required>
                <option value="" disabled selected>Select City</option>
                <option value="Dhaka">Dhaka</option>
                <option value="Chittagong">Chittagong</option>
                <option value="Khulna">Khulna</option>
                <!-- Add more options as needed -->
            </select>

            <!-- Garage Size (Checkboxes) -->
            <fieldset>
                <legend>Garage Size (Select all that apply):</legend>
                <label><input type="checkbox" name="size[]" value="Car"> Car</label>
                <label><input type="checkbox" name="size[]" value="Bike"> Bike</label>
                <label><input type="checkbox" name="size[]" value="Cycle"> Cycle</label>
            </fieldset>

            <!-- Price Input -->
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" placeholder="Enter Price" step="0.01" required>

            <!-- Additional Info -->
            <label for="additional_info">Additional Info:</label>
            <textarea name="additional_info" id="additional_info" placeholder="Provide additional details"></textarea>

            <!-- Phone Number -->
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" placeholder="Enter Phone Number" required>

            <!-- Image Upload -->
            <label for="images">Upload Garage Pictures (Min 1, Max 3):</label>
            <input type="file" name="images[]" id="images" accept="image/*" multiple required>

            <!-- Submit Button -->
            <button type="submit">Add Garage</button>
        </form>
    </main>
</body>
</html>