<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $current_price = mysqli_real_escape_string($conn, $_POST['current_price']);
    if ($current_price < 0) {
        echo "<script>alert('Current price cannot be negative. Please enter a valid value.'); window.history.back();</script>";
        exit;
    }
    $event_id = mysqli_real_escape_string($conn, $_POST['event_id']);
    
    $image = $_FILES['image']['name'];
    $target = "images/" . basename($image);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $query = "INSERT INTO items (title, description, image, current_price, event_id)
                  VALUES ('$title', '$description', '$image', '$current_price', '$event_id')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Item posted successfully!'); window.location.href='organizer_dashboard.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload image.');</script>";
    }
}

// Fetch upcoming or live events for dropdown
$events_query = "SELECT event_id, title FROM events WHERE user_id = {$_SESSION['user_id']} AND status IN ('upcoming', 'live')";
$events_result = mysqli_query($conn, $events_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        input[type="text"], input[type="number"], input[type="file"], textarea, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        textarea {
            resize: vertical;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
        <h2>Post an Item</h2>
        <label for="title">Item Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <label for="current_price">Base Price:</label>
        <input type="number" id="current_price" name="current_price" step="0.01" required>
        <label for="image">Item Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        <label for="event_id">Select Event:</label>
        <select id="event_id" name="event_id" required>
            <?php while ($row = mysqli_fetch_assoc($events_result)) { ?>
                <option value="<?php echo $row['event_id']; ?>"><?php echo $row['title']; ?></option>
            <?php } ?>
        </select>
        <button type="submit">Post Item</button>
    </form>
</body>
</html>
