<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the user's bids
$bids_query = "
    SELECT 
        bids.bid_amount, 
        bids.bid_time, 
        items.title AS item_title, 
        items.description AS item_description, 
        items.current_price 
    FROM bids
    JOIN items ON bids.item_id = items.item_id
    WHERE bids.user_id = '$user_id'
    ORDER BY bids.bid_time DESC
";
$bids_result = mysqli_query($conn, $bids_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bids - BidRush</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background: #333;
            color: #fff;
            padding: 1rem 2rem;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        nav {
            background: #444;
            padding: 0.5rem 2rem;
            text-align: right;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        nav ul li {
            display: inline;
            margin-right: 1rem;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            padding: 2rem;
        }

        h2 {
            margin-top: 2rem;
            font-size: 1.5rem;
            color: #222;
            border-bottom: 2px solid #ddd;
            padding-bottom: 0.5rem;
        }

        .bid {
            background: #fff;
            margin: 1rem 0;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .bid h3 {
            margin: 0;
            color: #444;
        }

        .bid p {
            margin: 0.5rem 0;
        }

        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>My Bids</h1>
    </header>
    <nav>
        <ul>
            <li><a href="individual_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <h2>Bids I've Placed</h2>
        <?php if (mysqli_num_rows($bids_result) > 0): ?>
            <?php while ($bid = mysqli_fetch_assoc($bids_result)): ?>
                <div class="bid">
                    <h3><?= htmlspecialchars($bid['item_title']); ?></h3>
                    <p>Description: <?= htmlspecialchars($bid['item_description']); ?></p>
                    <p>Your Bid: <strong><?= htmlspecialchars($bid['bid_amount']); ?></strong></p>
                    <p>Current Price: <?= htmlspecialchars($bid['current_price']); ?></p>
                    <p>Bid Time: <?= date('F j, Y, g:i a', strtotime($bid['bid_time'])); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't placed any bids yet.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?= date('Y'); ?> BidRush. All rights reserved.</p>
    </footer>
</body>
</html>
