<?php
session_start();
include 'connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bid'])) {
        $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
        $bid_amount = mysqli_real_escape_string($conn, $_POST['bid_amount']);

        // Fetch current price
        $query = "SELECT current_price FROM items WHERE item_id = '$item_id'";
        $result = mysqli_query($conn, $query);
        $item = mysqli_fetch_assoc($result);

        if ($result && $item) {
            $current_price = $item['current_price'];

            // Validate bid
            if (($current_price == 0 && $bid_amount > 0) || ($bid_amount > $current_price && $bid_amount >= $current_price + 100)) {
                $bid_time = date('Y-m-d H:i:s');
                $insert_query = "INSERT INTO bids (item_id, user_id, bid_amount, bid_time) 
                                 VALUES ('$item_id', '$user_id', '$bid_amount', '$bid_time')";

                if (mysqli_query($conn, $insert_query)) {
                    // Update the current price in items table
                    $update_query = "UPDATE items SET current_price = '$bid_amount' WHERE item_id = '$item_id'";
                    mysqli_query($conn, $update_query);
                    $_SESSION['message'] = 'Bid placed successfully!';
                } else {
                    $_SESSION['message'] = 'Error placing bid.';
                }
            } else {
                $_SESSION['message'] = 'Invalid bid amount. Minimum increment is 100.';
            }
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_POST['comment'])) {
        $item_type = mysqli_real_escape_string($conn, $_POST['item_type']);
        $item_id = mysqli_real_escape_string($conn, $_POST['item_id']);
        $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);

        $comment_time = date('Y-m-d H:i:s');
        $comment_query = "INSERT INTO comments (user_id, item_type, item_id, comment_text, comment_time) 
                          VALUES ('$user_id', '$item_type', '$item_id', '$comment_text', '$comment_time')";

        if (mysqli_query($conn, $comment_query)) {
            $_SESSION['message'] = 'Comment added successfully!';
        } else {
            $_SESSION['message'] = 'Error adding comment.';
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Fetch upcoming and live events
$events_query = "SELECT * FROM events WHERE status IN ('upcoming', 'live')";
$events_result = mysqli_query($conn, $events_query);

// Fetch items for live events
$items_query = "SELECT items.*, events.title AS event_title 
                FROM items 
                JOIN events ON items.event_id = events.event_id 
                WHERE events.status = 'live'";
$items_result = mysqli_query($conn, $items_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BidRush</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #343a40;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Header */
        header {
            background: linear-gradient(90deg,rgb(117, 10, 10),rgb(14, 5, 46));
            color: #fff;
            padding: 1.5rem;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }

        /* Navigation */
        nav {
            background: #343a40;
            padding: 0.5rem 2rem;
        }

        nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
            text-align: right;
        }

        nav ul li {
            display: inline;
            margin-right: 1.5rem;
        }

        nav ul li a {
            color: #f8f9fa;
            font-weight: 500;
        }

        nav ul li a:hover {
            color: #ffc107;
        }

        /* Main Content */
        main {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Section Titles */
        h2 {
            font-size: 1.8rem;
            color: #4e9f3d;
            margin-bottom: 1rem;
            border-bottom: 2px solid #ddd;
            padding-bottom: 0.5rem;
        }

        /* Cards */
        .event, .product {
            background: #fff;
            margin: 1rem 0;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .event:hover, .product:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .event h3, .product h3 {
            margin-top: 0;
            color: #495057;
        }

        .event p, .product p {
            margin: 0.5rem 0;
            color: #6c757d;
        }

        /* Forms */
        form {
            margin: 1.5rem 0;
        }

        textarea, input[type="number"], input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            background: linear-gradient(90deg,rgb(112, 6, 6),rgb(0, 0, 0));
            color: #fff;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background 0.3s;
        }

        button:hover {
            background: #4e9f3d;
        }

        /* Images */
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 0.5rem 0;
        }

        /* Comment Section */
        h4 {
            margin-top: 1.5rem;
            font-size: 1.4rem;
            color: #495057;
        }

        p strong {
            color: #007bff;
        }

        /* Footer */
        footer {
            background: #343a40;
            color: #f8f9fa;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2rem;
        }

        footer a {
            color: #ffc107;
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="bid.php">My Bids</a></li>
        </ul>
    </nav>
    <main>
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?= htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <section>
            <h2>Upcoming and Live Events</h2>
            <?php while ($event = mysqli_fetch_assoc($events_result)) { ?>
                <div class="event">
                    <h3><?= htmlspecialchars($event['title']); ?></h3>
                    <p>Status: <?= htmlspecialchars($event['status']); ?></p>

                    <!-- Comment Form -->
                    <form method="POST">
                        <textarea name="comment_text" placeholder="Add a comment..." required></textarea>
                        <input type="hidden" name="item_type" value="event">
                        <input type="hidden" name="item_id" value="<?= $event['event_id']; ?>">
                        <button type="submit" name="comment">Comment</button>
                    </form>

                    <!-- Display Comments -->
                    <h4>Comments:</h4>
                    <?php
                    $event_id = $event['event_id'];
                    $comments_query = "SELECT comments.comment_text, comments.comment_time, users.name 
                                       FROM comments 
                                       JOIN users ON comments.user_id = users.id 
                                       WHERE comments.item_type = 'event' AND comments.item_id = '$event_id' 
                                       ORDER BY comments.comment_time DESC";
                    $comments_result = mysqli_query($conn, $comments_query);
                    while ($comment = mysqli_fetch_assoc($comments_result)) { ?>
                        <p><strong><?= htmlspecialchars($comment['name']); ?>:</strong> <?= htmlspecialchars($comment['comment_text']); ?> (<?= htmlspecialchars($comment['comment_time']); ?>)</p>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

        <section>
            <h2>Live Products</h2>
            <?php while ($item = mysqli_fetch_assoc($items_result)) { ?>
                <div class="product">
                    <h3><?= htmlspecialchars($item['title']); ?></h3>
                    <p>Description: <?= htmlspecialchars($item['description']); ?></p>
                    <img src="images/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['title']); ?>" width="150">
                    <p>Current Price: <?= htmlspecialchars($item['current_price']); ?></p>

                    <!-- Bid Form -->
                    <form method="POST">
                        <input type="number" name="bid_amount" placeholder="Enter your bid" required>
                        <input type="hidden" name="item_id" value="<?= $item['item_id']; ?>">
                        <button type="submit" name="bid">Place Bid</button>
                    </form>

                    <!-- Comment Form -->
                    <form method="POST">
                        <textarea name="comment_text" placeholder="Add a comment..." required></textarea>
                        <input type="hidden" name="item_type" value="item">
                        <input type="hidden" name="item_id" value="<?= $item['item_id']; ?>">
                        <button type="submit" name="comment">Comment</button>
                    </form>

                    <!-- Display Comments -->
                    <h4>Comments:</h4>
                    <?php
                    $item_id = $item['item_id'];
                    $comments_query = "SELECT comments.comment_text, comments.comment_time, users.name 
                                       FROM comments 
                                       JOIN users ON comments.user_id = users.id 
                                       WHERE comments.item_type = 'item' AND comments.item_id = '$item_id' 
                                       ORDER BY comments.comment_time DESC";
                    $comments_result = mysqli_query($conn, $comments_query);
                    while ($comment = mysqli_fetch_assoc($comments_result)) { ?>
                        <p><strong><?= htmlspecialchars($comment['name']); ?>:</strong> <?= htmlspecialchars($comment['comment_text']); ?> (<?= htmlspecialchars($comment['comment_time']); ?>)</p>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>
    </main>
</body>
</html>
