<?php

include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    
    $query = "INSERT INTO users (user_type, name, email, phone, street, zip, city, password) 
              VALUES ('$user_type', '$name', '$email', '$phone', '$street', '$zip', '$city', '$password')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Signup Successful!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - BidRush</title>
    <!-- <link rel="stylesheet" href="css/style.css"> -->
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }
    .navbar {
        background-color: #000;
        padding: 10px 20px;
        display: flex;
        align-items: center;
    }
    .navbar .logo a {
        color: #fff;
        text-decoration: none;
        font-size: 24px;
        font-weight: bold;
    }
    main {
        max-width: 400px;
        margin: 50px auto;
        padding: 40px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }
    label {
        display: block;
        margin-bottom: 5px;
        color: #555;
    }
    select,
    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    button {
        width: 100%;
        padding: 10px;
        background-color: #000;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background-color: #333;
    }
    p {
        text-align: center;
        color: #777;
    }
    p a {
        color: #000;
        text-decoration: none;
    }
    p a:hover {
        text-decoration: underline;
    }
</style>

   
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">BidRush</a></div>
        </nav>
    </header>
    <main>
        <h2>Signup</h2>
        <form method="POST" action="signup.php">
            <label for="user_type">I am signing up as:</label>
            <select name="user_type" id="user_type" required>
                <option value="Individual">Individual</option>
                <option value="Organization">Organization</option>
            </select>

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" required>

            <label for="street">Street:</label>
            <input type="text" name="street" id="street" required>

            <label for="zip">ZIP Code:</label>
            <input type="text" name="zip" id="zip" required>

            <label for="city">City:</label>
            <input type="text" name="city" id="city" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Signup</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </main>
</body>
</html>
