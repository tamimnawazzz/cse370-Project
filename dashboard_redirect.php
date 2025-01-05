<?php
session_start();
include 'connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$query = "SELECT user_type FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $user_type = $row['user_type'];

    if ($user_type == 'Individual') {
        header('Location: individual_dashboard.php');
    } elseif ($user_type == 'Organization') {
        header('Location: organizer_dashboard.php');
    } else {
        echo "Invalid user type.";
    }
} else {
    echo "User not found.";
}
?>
