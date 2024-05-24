<?php
session_start();
include 'config.php';

if($_SERVER["REQUEST_METHOD"]=='POST')
{
    $User_id = $_POST["User_id"];
    $Password = $_POST["Password"];

    $sql = "SELECT * FROM user WHERE User_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $User_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify password
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($Password, $user['Password'])) {
            // Set the session variable
            $_SESSION['user_id'] = $user['User_id'];
            header('Location: index.php');
            exit(); // Ensure script execution stops after redirection
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <form method="post" class="mt-5">
            <h1 class="text-center">Login</h1>
            <?php if(isset($error)) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php } ?>
            <div class="mb-3 col-3 mx-auto">
                <label for="User_id" class="form-label">User_id:</label>
                <input type="text" class="form-control form-control-sm" id="User_id" name="User_id" required>
            </div>
            <div class="mb-3 col-3 mx-auto">
                <label for="Password" class="form-label">Password:</label>
                <input type="password" class="form-control form-control-sm" id="Password" name="Password" required>
            </div>
            <div class="d-grid gap-2 col-2 mx-auto">
                <button type="submit" class="btn btn-primary btn-sm">Login</button>
            </div>
            <div class="text-center mt-3">
                New user? <br>
                <a href="register.php" class="btn btn-link">Register here</a>
            </div>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
