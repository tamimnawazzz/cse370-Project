<?php
include 'connect.php';

// Fetch events categorized by status
$upcoming_events = mysqli_query($conn, "SELECT * FROM events WHERE status = 'upcoming'");
$live_events = mysqli_query($conn, "SELECT * FROM events WHERE status = 'live'");
$closed_events = mysqli_query($conn, "SELECT * FROM events WHERE status = 'closed'");

// Fetch items grouped by event
$items_query = "SELECT items.title AS item_title, items.description, items.current_price, items.image, events.title AS event_title 
                FROM items 
                JOIN events ON items.event_id = events.event_id";
$items = mysqli_query($conn, $items_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BidRush</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color:rgb(16, 24, 23);
            padding: 10px 20px;
            color: #fff;
        }

        .navbar__links a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .navbar__logo-image {
            height: 50px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .section {
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            border-radius: 8px;
        }

        .event-list, .item-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .event, .item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            flex: 1 1 calc(30% - 20px);
            background-color: #f9f9f9;
            text-align: center;
            transition: box-shadow 0.3s ease;
        }

        .event:hover, .item:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .event img, .item img {
            width: 50%;
            height: auto;
            border-radius: 8px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: #fff;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="navbar__logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="BidRush" class="navbar__logo-image">
                </a>
            </div>
            <div class="navbar__links">
                <a href="login.php" class="navbar__link">Login</a>
                <a href="signup.php" class="navbar__link">Signup</a>
                <a href="live_auctions.php" class="navbar__link">Live Auctions</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="section">
            <h2>Upcoming Events</h2>
            <div class="event-list">
                <?php while ($event = mysqli_fetch_assoc($upcoming_events)) { ?>
                    <div class="event">
                        <h3><?php echo $event['title']; ?></h3>
                        <p>Start: <?php echo $event['start_date']; ?></p>
                        <p>End: <?php echo $event['end_date']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </section>

        <section class="section">
            <h2>Live Events</h2>
            <div class="event-list">
                <?php while ($event = mysqli_fetch_assoc($live_events)) { ?>
                    <div class="event">
                        <h3><?php echo $event['title']; ?></h3>
                        <p>Start: <?php echo $event['start_date']; ?></p>
                        <p>End: <?php echo $event['end_date']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </section>

        <section class="section">
            <h2>Closed Events</h2>
            <div class="event-list">
                <?php while ($event = mysqli_fetch_assoc($closed_events)) { ?>
                    <div class="event">
                        <h3><?php echo $event['title']; ?></h3>
                        <p>Start: <?php echo $event['start_date']; ?></p>
                        <p>End: <?php echo $event['end_date']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </section>

        <section class="section">
            <h2>Items in Events</h2>
            <div class="item-list">
                <?php while ($item = mysqli_fetch_assoc($items)) { ?>
                    <div class="item">
                        <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['item_title']; ?>">
                        <h3><?php echo $item['item_title']; ?></h3>
                        <p><?php echo $item['description']; ?></p>
                        <p>Current Price: $<?php echo $item['current_price']; ?></p>
                        <p>Event: <?php echo $item['event_title']; ?></p>
                    </div>
                <?php } ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 BidRush. All rights reserved by Tamim Nawaz.</p>
    </footer>
</body>
</html>
