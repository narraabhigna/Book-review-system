<?php
session_start();
include 'config.php';

// Function to log out the user
function logout() {
    session_start();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Check if logout is requested
if (isset($_GET['logout'])) {
    logout();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit;
}

// Initialize variables
$selected_book = "";
$selected_author = "";
$warning_message = "";
$success_message = "";
$review = ""; // Initialize review variable
$rating = ""; // Initialize rating variable

// Fetch books from the database
$sql_books = "SELECT title FROM books";
$result_books = $conn->query($sql_books);

// Array to store book titles
$books = array();
if ($result_books->num_rows > 0) {
    while($row = $result_books->fetch_assoc()) {
        // Store each book title in the $books array
        $books[] = $row["title"];
    }
}

// Array to store book authors
$book_authors = array();
foreach ($books as $book) {
    // Fetch authors for each book
    $sql_authors = "SELECT author FROM books WHERE title = '$book'";
    $result_authors = $conn->query($sql_authors);
    if ($result_authors->num_rows > 0) {
        // Store authors for each book in the $book_authors array
        $authors = array();
        while($row = $result_authors->fetch_assoc()) {
            $authors[] = $row["author"];
        }
        $book_authors[$book] = $authors;
    }
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_book = $_POST['book'];
    $selected_author = $_POST['author'];
    $review = $_POST['review'];
    $rating = $_POST['rating'];

    // Get the book_id from the books table based on the selected book title and author
    $sql_book_id = "SELECT book_id FROM books WHERE title = '$selected_book' AND author = '$selected_author'";
    $result_book_id = $conn->query($sql_book_id);
    if ($result_book_id->num_rows > 0) {
        $row = $result_book_id->fetch_assoc();
        $book_id = $row["book_id"];

        // Get the user_id from the users table based on who is logged in
        $user_id = $_SESSION['user_id'];

        // Check if a review exists for this book and user
        $sql_check_review = "SELECT * FROM review WHERE user_id = '$user_id' AND book_id = '$book_id'";
        $result_check_review = $conn->query($sql_check_review);
        if ($result_check_review->num_rows > 0) {
            // Update the review in the review table
            $sql_update_review = "UPDATE review SET rating = '$rating', review = '$review' WHERE user_id = '$user_id' AND book_id = '$book_id'";
            if ($conn->query($sql_update_review) === TRUE) {
                $success_message = "Review updated successfully!";
                // Clear form values after successful update
                $selected_book = "";
                $selected_author = "";
                $review = "";
                $rating = "";
            } else {
                $warning_message = "Error: " . $conn->error;
            }
        } else {
            $warning_message = "No existing review found to update. Please create a review first!!";
        }
    } else {
        // Set warning message if book not found
        $warning_message = "Book not found. Please select a valid book and author.";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update a Review</title>
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
                        <a class="nav-link " href="create.php">Write a review</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="read.php">Read reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="update.php">Update a review</a>
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
                <h2>Update a Review</h2>
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
                    <div class="mb-3">
                        <label for="review" class="form-label">Review</label>
                        <textarea class="form-control" id="review" name="review" rows="3" required><?php echo $review; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1-5)</label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required value="<?php echo $rating; ?>">
                    </div>
                    <?php if(!empty($warning_message)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $warning_message; ?>
                        </div>
                    <?php } ?>
                    <?php if(!empty($success_message)) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php } ?>
                    <button type="submit" class="btn btn-primary">Update Review</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>