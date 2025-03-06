<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <style>
        #maincontainer {
            min-height: 100vh;
        }
    </style>
    <title>Welcome to iDiscuss - Coding Forums</title>
</head>

<body>
    <?php include 'partials/_dbconnect.php'; ?>
    <?php include 'partials/_header.php'; ?>

    <!-- Search Results -->
    <div class="container my-3" id="maincontainer">
        <h1 class="py-3">Search results for <em>"<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"</em></h1>

        <?php
        $noresults = true;
        $query = $_GET["search"] ?? ''; // Use null coalescing operator

        // Check if search query is empty
        if (empty(trim($query))) {
            echo '<div class="alert alert-danger">Please enter a search term.</div>';
        } else {
            // Remove special characters that can break MySQL full-text search
            $query = preg_replace('/[+\-><\(\)~*\"@]+/', ' ', $query);
            
            // Escape user input to prevent SQL injection
            $query = mysqli_real_escape_string($conn, $query);

            // Attempt Full-Text Search
            $sql = "SELECT * FROM threads WHERE MATCH (thread_title, thread_desc) AGAINST ('$query' IN BOOLEAN MODE)";
            $result = mysqli_query($conn, $sql);

            // If Full-Text Search Fails or No Results, Use LIKE
            if (!$result || mysqli_num_rows($result) == 0) {
                $sql = "SELECT * FROM threads WHERE thread_title LIKE '%$query%' OR thread_desc LIKE '%$query%'";
                $result = mysqli_query($conn, $sql);
            }

            // Display Results
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $title = htmlspecialchars($row['thread_title']); // Prevent XSS
                    $desc = htmlspecialchars($row['thread_desc']);
                    $thread_id = $row['thread_id'];
                    $url = "thread.php?threadid=" . $thread_id;

                    echo "<div class='result my-3 p-3 border rounded'>
                            <h3><a href='$url' class='text-dark'>$title</a></h3>
                            <p>$desc</p>
                          </div>";
                    $noresults = false;
                }
            }

            // If No Results Found
            if ($noresults) {
                echo '<div class="jumbotron jumbotron-fluid">
                        <div class="container">
                            <p class="display-4">No Results Found</p>
                            <p class="lead">Suggestions:
                                <ul>
                                    <li>Make sure that all words are spelled correctly.</li>
                                    <li>Try different keywords.</li>
                                    <li>Try more general keywords.</li>
                                </ul>
                            </p>
                        </div>
                      </div>';
            }
        }
        ?>
    </div>

    <?php include 'partials/_footer.php'; ?>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuFzy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>
</body>

</html>
