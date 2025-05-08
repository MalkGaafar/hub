
<?php
// Include necessary controllers


// Initialize controllers
$dbController = new DBController();
$tagController = new TagController($dbController);

// Fetch all tags
$tags = $tagController->getAllTags();

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <style>
        .tag-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            height: 200px; /* Adjust height as needed */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .tag-card h3 {
            margin-top: 0;
        }
        .tag-stats {
            font-size: 0.9em;
            color: #555;
        }
        .search-bar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>All Tags</h1>

        <div class="search-bar">
            <input type="text" class="form-control" id="tagSearch" placeholder="<?php
                if (isset($_SESSION['language']) && $_SESSION['language'] == 'en') {
                    echo 'Search by tag name';
                } else {
                    echo 'البحث باسم الوسم';
                }?>">
        </div>

        <div class="row" id="tagList">
            <?php if (!empty($tags)): ?>
                <?php foreach ($tags as $tag): ?>
                    <?php
                    // Determine the correct description based on language
                    $description = '';
                    if (isset($_SESSION['language']) && $_SESSION['language'] == 'en') {
                        $description = $tag['english_description'] ?? ''; // Use ?? '' for safety
                    } else {
                        $description = $tag['arabic_description'] ?? ''; // Use ?? '' for safety
                    }
                    ?>
                    <div class="col-md-3">
                        <div class="tag-card">
                            <h3><?php
                                if (isset($_SESSION['language']) && $_SESSION['language'] == 'en') {
                                    echo htmlspecialchars($tag['name']);
                                } else {
                                    echo htmlspecialchars($tag['name']);
                                }?></h3>
                            <div class="tag-stats">
                                <p><?php echo htmlspecialchars($description); ?></p>
                                
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <p>No tags found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

  

    <script>
        // Basic client-side filtering (can be enhanced with AJAX)
        document.getElementById('tagSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let tagList = document.getElementById('tagList');
            let tagCards = tagList.getElementsByClassName('col-md-3');

            for (let i = 0; i < tagCards.length; i++) {
                let h3 = tagCards[i].getElementsByTagName('h3')[0];
                let textValue = h3.textContent || h3.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    tagCards[i].style.display = "";
                } else {
                    tagCards[i].style.display = "none";
                }
            }
        });
    </script>

</body>
</html>