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
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit;
}

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Review System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand active" href="index.php">Book Review System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link"  href="create.php">Write a review</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="read.php">Read reviews</a>
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
                    echo '<a class="nav-link" href="index.php?logout=true">Logout</a>';
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
        <div class="col-md-12">
            <h2>Books</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch books from the database
                        $sql = "SELECT title, author FROM books";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            // Output data of each row
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["title"]. "</td>";
                                echo "<td>" . $row["author"]. "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No books found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>  

</body>

</html>

<?php
$conn->close();
?>  