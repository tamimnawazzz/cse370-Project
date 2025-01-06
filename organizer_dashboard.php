<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];


$update_status_query = "
    UPDATE events
    SET status = CASE 
                    WHEN CURDATE() < start_date THEN 'Upcoming'
                    WHEN CURDATE() BETWEEN start_date AND end_date THEN 'Live'
                    ELSE 'Closed'
                END
    WHERE user_id = $user_id";
mysqli_query($conn, $update_status_query);


$user_query = "SELECT name, email FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);


$events_query = "
    SELECT e.event_id, e.title, e.start_date, e.end_date, e.status,
           (SELECT COUNT(*) FROM items WHERE event_id = e.event_id) AS item_count
    FROM events e
    WHERE e.user_id = $user_id";
$events_result = mysqli_query($conn, $events_query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }
        nav {
            background-color: #333;
            overflow: hidden;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        nav ul li {
            float: left;
        }
        nav ul li a {
            display: block;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
        }
        nav ul li a:hover {
            background-color: #575757;
        }
        main {
            padding: 20px;
        }
        .profile-summary, .events-section {
            margin-bottom: 20px;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .profile-summary h2, .events-section h2 {
            margin-top: 0;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .actions {
            text-align: center;
        }
        button {
            padding: 5px 10px;
            color: white;
            border: none;
            background-color: #007bff;
            border-radius: 3px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1><strong></strong> <?php echo htmlspecialchars($user_data['name']); ?> Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="post_item.php">Post Items</a></li>
            <li><a href="host_event.php">Host New Events</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <main>
       
        <div class="profile-summary">
            <h2>Profile Summary</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
        </div>

        
        <div class="events-section">
            <h2>Your Events</h2>
            <?php if (mysqli_num_rows($events_result) > 0) { ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Items Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = mysqli_fetch_assoc($events_result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo htmlspecialchars($event['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($event['end_date']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($event['status'])); ?></td>
                                <td class="actions"><?php echo $event['item_count']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>No events hosted yet. Start by <a href="host_event.php">hosting an event</a>.</p>
            <?php } ?>
        </div>
    </main>
</body>
</html>
