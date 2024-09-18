<?php
session_start();
include 'db.php'; // Include the database connection

// Fetch available garages with filters if applied
$filter_query = "SELECT * FROM garages WHERE status = 'available'";

// Apply filters based on user input
if (isset($_GET['city']) && $_GET['city'] != '') {
    $city = mysqli_real_escape_string($conn, $_GET['city']);
    $filter_query .= " AND city = '$city'";
}

if (isset($_GET['location']) && $_GET['location'] != '') {
    $location = mysqli_real_escape_string($conn, $_GET['location']);
    $filter_query .= " AND location = '$location'";
}

if (isset($_GET['size']) && is_array($_GET['size'])) {
    // Sanitize each size and construct the query
    $sizes = array_map(function($size) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $size) . "'";
    }, $_GET['size']);
    $size_filter = implode(",", $sizes);
    $filter_query .= " AND size IN ($size_filter)";
}

// Filter by price range if both values are provided
if (!empty($_GET['price_min']) && !empty($_GET['price_max'])) {
    $price_min = (float) $_GET['price_min'];
    $price_max = (float) $_GET['price_max'];
    $filter_query .= " AND price BETWEEN $price_min AND $price_max";
}

// Sort by price (ascending or descending)
if (isset($_GET['sort_price']) && $_GET['sort_price'] != '') {
    $sort_price = $_GET['sort_price'] === 'asc' ? 'ASC' : 'DESC';
    $filter_query .= " ORDER BY price $sort_price";
} else {
    // Default sorting by ascending order of price if no sorting is chosen
    $filter_query .= " ORDER BY price ASC";
}

// Debugging: Print the final SQL query
// echo $filter_query;

$result = $conn->query($filter_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentMyGarage</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header class="top-bar">
        <h1>RentMyGarage</h1>

        <?php if (isset($_SESSION['user_id'])) { ?>
            <nav class="top-bar-nav">
                <a href="user_dashboard.php" class="top-bar-btn">User</a>
                <a href="dashboard.php" class="top-bar-btn">Dashboard</a>
                <a href="logout.php" class="top-bar-btn">Logout</a>
            </nav>
        <?php } ?>
    </header>

    <main>
        <section class="filter-section">
            <h2>Available Garages for Rent</h2>
            <form method="GET" action="home.php" class="filter-form">

                <select name="city" id="city">
                    <option value="">All Cities</option> <!-- Allow 'unselect' option -->
                    <option value="Dhaka" <?php echo isset($_GET['city']) && $_GET['city'] == 'Dhaka' ? 'selected' : ''; ?>>Dhaka</option>
                    <option value="Chittagong" <?php echo isset($_GET['city']) && $_GET['city'] == 'Chittagong' ? 'selected' : ''; ?>>Chittagong</option>
                    <option value="Khulna" <?php echo isset($_GET['city']) && $_GET['city'] == 'Khulna' ? 'selected' : ''; ?>>Khulna</option>
                </select>

                <!-- Location Selection -->
                <select name="location" id="location">
                    <option value="">All Areas</option> <!-- Allow 'unselect' option -->
                    <option value="Kalabagan" <?php echo isset($_GET['location']) && $_GET['location'] == 'Kalabagan' ? 'selected' : ''; ?>>Kalabagan</option>
                    <option value="Dhanmondi" <?php echo isset($_GET['location']) && $_GET['location'] == 'Dhanmondi' ? 'selected' : ''; ?>>Dhanmondi</option>
                    
                    <option value="Gulshan" <?php echo isset($_GET['location']) && $_GET['location'] == 'Gulshan' ? 'selected' : ''; ?>>Gulshan</option>
                </select>

                <!-- Garage Size (Checkboxes) -->
                <fieldset>
                    <legend>Garage Size:</legend>
                    <label><input type="checkbox" name="size[]" value="Car" <?php echo isset($_GET['size']) && in_array('Car', $_GET['size']) ? 'checked' : ''; ?>> Car</label>
                    <label><input type="checkbox" name="size[]" value="Bike" <?php echo isset($_GET['size']) && in_array('Bike', $_GET['size']) ? 'checked' : ''; ?>> Bike</label>
                    <label><input type="checkbox" name="size[]" value="Cycle" <?php echo isset($_GET['size']) && in_array('Cycle', $_GET['size']) ? 'checked' : ''; ?>> Cycle</label>
                </fieldset>

                <!-- Price Range -->
                <input type="number" name="price_min" placeholder="Min Price" value="<?php echo isset($_GET['price_min']) ? htmlspecialchars($_GET['price_min']) : ''; ?>">
                <input type="number" name="price_max" placeholder="Max Price" value="<?php echo isset($_GET['price_max']) ? htmlspecialchars($_GET['price_max']) : ''; ?>">

                <!-- Sort by Price -->
                <select name="sort_price">
                    <option value="" disabled selected>Sort by Price</option>
                    <option value="asc" <?php echo isset($_GET['sort_price']) && $_GET['sort_price'] == 'asc' ? 'selected' : ''; ?>>Low to High</option>
                    <option value="desc" <?php echo isset($_GET['sort_price']) && $_GET['sort_price'] == 'desc' ? 'selected' : ''; ?>>High to Low</option>
                </select>

                <button type="submit">Filter</button>
            </form>
        </section>

        <section class="garages-list">
            <?php
            if ($result->num_rows > 0) {
                while ($garage = $result->fetch_assoc()) {
                    echo "<a href='garage_details.php?garage_id=" . urlencode($garage['id']) . "' class='garage-item-link'>";
                    echo "<div class='garage-item'>";
                    echo "<div class='garage-item-content'>";
                    echo "<h3>" . htmlspecialchars($garage['headline']) . "</h3>";

                    echo "<p><strong>Area:</strong> " . htmlspecialchars($garage['location']) . "</p>";
                    echo "<p><strong>City:</strong> " . htmlspecialchars($garage['city']) . "</p>";
                    echo "<p><strong>Size:</strong> " . htmlspecialchars($garage['size']) . "</p>";
                    echo "<p><strong>Price:</strong> per month/ TK " . htmlspecialchars($garage['price'])  . "</p>";
                    echo "<p><strong>Status:</strong> " . htmlspecialchars($garage['status']) . "</p>";
                    echo "</div>";
                    
                    // Display images if they exist
                    if (!empty($garage['image1'])) {
                        echo "<img src='" . htmlspecialchars($garage['image1']) . "' alt='Garage Image 1' class='garage-image'>";
                    }
                    if (!empty($garage['image2'])) {
                        echo "<img src='" . htmlspecialchars($garage['image2']) . "' alt='Garage Image 2' class='garage-image'>";
                    }
                    if (!empty($garage['image3'])) {
                        echo "<img src='" . htmlspecialchars($garage['image3']) . "' alt='Garage Image 3' class='garage-image'>";
                    }
                    
                    echo "</div>";
                    echo "</a>";
                }
            } else {
                echo "<p>No garages available for rent.</p>";
            }
            ?>
        </section>
    </main>
</body>
</html>