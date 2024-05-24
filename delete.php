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

$user_id = $_SESSION['user_id'];
$warning_message = "";
$success_message = "";


// Check if a review ID is provided for deletion and validate it
if (isset($_GET['delete_id'])) {
    $review_id = intval($_GET['delete_id']); // Ensure review_id is an integer

    // Prepare the SQL statement to delete the review
    $sql_delete = "DELETE FROM review WHERE review_id = ? ";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $review_id);

    // Execute the statement and check if the deletion was successful
    if ($stmt->execute()) {
        $success_message = "Review deleted successfully!";
    } else {
        $warning_message = "Error: " . $conn->error;
    }

    // Close the statement
    $stmt->close();
}

// Fetch reviews for the logged-in user
$sql_reviews = "SELECT review_id, book_id, rating, review FROM review WHERE user_id = '$user_id'";
$stmt = $conn->prepare($sql_reviews);
$stmt->execute();
$result_reviews = $stmt->get_result();
$reviews = $result_reviews->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch book titles
$book_titles = array();
foreach ($reviews as $review) {
    $book_id = $review['book_id'];
    $sql_book_title = "SELECT title FROM books WHERE book_id = ?";
    $stmt = $conn->prepare($sql_book_title);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result_title = $stmt->get_result();
    $book = $result_title->fetch_assoc();
    $book_titles[$book_id] = $book['title'];
    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delete Review</title>
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
                        <a class="nav-link" href="read.php">Read reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="update.php">Update a review</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="delete.php">Delete a review</a>
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
            <div class="col-md-8 offset-md-2">
                <h2>Delete a Review</h2>
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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book_titles[$review['book_id']]); ?></td>
                                <td><?php echo htmlspecialchars($review['rating']); ?></td>
                                <td><?php echo htmlspecialchars($review['review']); ?></td>
                                <td>
                                    <a href="?delete_id=<?php echo $review['review_id']; ?>" class="btn btn-danger">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
