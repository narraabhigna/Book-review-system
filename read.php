<?php
session_start();
include 'config.php';
function logout() {
    // Destroy session
    session_destroy();
    // Redirect to login page
    header("Location: login.php");
    exit;
}

// Check if logout is requested
if (isset($_GET['logout'])) {
    logout();
}
// Fetch books from the database
$sql_books = "SELECT Title FROM books";
$result_books = $conn->query($sql_books);

// Array to store book titles
$books = array();
if ($result_books->num_rows > 0) {
    while($row = $result_books->fetch_assoc()) {
        // Store each book title in the $books array
        $books[] = $row["Title"];
    }
}

// Array to store book authors
$book_authors = array();
foreach ($books as $book) {
    // Fetch authors for each book
    $sql_authors = "SELECT Author FROM books WHERE Title = '$book'";
    $result_authors = $conn->query($sql_authors);
    if ($result_authors->num_rows > 0) {
        // Store authors for each book in the $book_authors array
        $authors = array();
        while($row = $result_authors->fetch_assoc()) {
            $authors[] = $row["Author"];
        }
        $book_authors[$book] = $authors;
    }
}

// Initialize variables
$selected_book = "";
$selected_author = "";
$reviews = array();
$warning_message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_book = $_POST['book'];
    $selected_author = $_POST['author'];

    // Check if the selected book and author combination exists
    if (!array_key_exists($selected_book, $book_authors) || !in_array($selected_author, $book_authors[$selected_book])) {
        // Set warning message for invalid combination
        $warning_message = "Invalid book and author combination. Please select a valid combination.";
    } else {
        // Fetch reviews for the selected book and author combination
        $sql_reviews = "SELECT review_id, user_id, rating, review FROM review WHERE book_id = (SELECT Book_id FROM books WHERE Title = '$selected_book')";

        $result_reviews = $conn->query($sql_reviews);
        if ($result_reviews->num_rows > 0) {
            // Store reviews in the $reviews array
            while($row = $result_reviews->fetch_assoc()) {
                $reviews[] = $row;
            }
        } else {
            // Set warning message if no reviews exist
            $warning_message = "This book hasn't been reviewed yet.";
        }
    }
    
    // Clear form values after submission
    $selected_book = "";
    $selected_author = "";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Read Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Book Review System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="create.php">Write a review</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="read.php">Read reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="update.php">Update a review</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="delete.php">Delete a review</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                <?php
                if (isset($_SESSION['user_id'])) {
                    echo '<li class="nav-item">';
                    echo '<a class="nav-link" href="create.php?logout=true">Logout</a>';
                    echo '</li>';
                } else {
                    // If the user is not logged in, display the Register and Logout links as it is
                    echo '
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    ';
                }
                ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Read Reviews</h2>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label for="book" class="form-label">Select a Book</label>
                        <select class="form-select" id="book" name="book" required>
                            <option value="" <?php if($selected_book == "") echo "selected"; ?> disabled>Select a Book</option>
                            <?php
                            // Iterate over each book and add it as an option in the dropdown
                            foreach ($books as $book) {
                                echo "<option value='" . $book . "'";
                                if($selected_book == $book) echo " selected";
                                echo ">" . $book . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Select an Author</label>
                        <select class="form-select" id="author" name="author" required>
                            <option value="" <?php if($selected_author == "") echo "selected"; ?> disabled>Select an Author</option>
                            <?php
                            // Iterate over all possible authors and add them as options in the dropdown
                            foreach ($book_authors as $book => $authors) {
                                foreach ($authors as $author) {
                                    echo "<option value='" . $author . "'";
                                    if($selected_author == $author) echo " selected";
                                    echo ">" . $author . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <?php if(!empty($warning_message)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $warning_message; ?>
                        </div>
                    <?php } ?>
                    <button type="submit" class="btn btn-primary">View Reviews</button>
                </form>
                
                <?php if(!empty($reviews)) { ?>
                    <h3>Reviews</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Review ID</th>
                                    <th>Review</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review) { ?>
                                    <tr>
                                        <td><?php echo $review['user_id']; ?></td>
                                        <td><?php echo $review['review_id']; ?></td>
                                        <td><?php echo $review['review']; ?></td>
                                        <td><?php echo $review['rating']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
