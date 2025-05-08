
<?php
// Include necessary files (e.g., database connection, header, footer)

// Initialize controllers
$dbController = new DBController();
$groupController = new GroupController($dbController);
$tagController = new TagController($dbController);

// Fetch all groups and tags
$groups = $groupController->getAllGroups();

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <!-- Add navigation links here -->
    <style>
        .nav-links {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .nav-links a {
            margin-right: 15px;
            text-decoration: none;
        }
    </style>
    <div class="nav-links">
        <a href="#all-groups">All Groups</a>
   
    </head>
</head>
<body>
    <h1>Groups</h1>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
            <div class="col-md-8" id="all-groups"> <!-- Added ID here -->
                <?php if (!empty($groups)): ?>
                    <ul class="list-group">
                        <?php foreach ($groups as $group): ?>
                            <li class="list-group-item">
                                <h3><?php echo htmlspecialchars($group['name']); ?></h3>
                                <p>Group ID: <?php echo htmlspecialchars($group['id']); ?></p>
                                <p>Created By: <?php echo htmlspecialchars($group['creator_id']); ?></p>
                                <p>Created At: <?php echo htmlspecialchars($group['created_at']); ?></p>
                                <a href="viewGroup.php?id=<?php echo $group['id']; ?>" class="btn btn-primary">View</a>
                                <form action="../controllers/GroupController.php" method="post" style="display: inline-block;">
                                    <input type="hidden" name="action" value="join">
                                   
                                    <input type="hidden" name="group_id" value="<?php echo $group['id']; ?>">
                                    <!-- Add logic here to check if user is already in the group and change button text -->
                                    <button type="submit" class="btn btn-success">Join Group</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No groups found.</p>
                <?php endif; ?>
          
        </div>
    </div>

 
</body>
</html>