<?php
include "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="register.php" method="post" class="mt-5">
            <h1 class="text-center">Register</h1>
            <div class="mb-3 col-3 mx-auto">
                <label for="User_id" class="form-label">User_id:</label>
                <input type="text" class="form-control" name="User_id" maxlength="50" required>
            </div>
            <div class="mb-3 col-3 mx-auto">
                <label for="Name" class="form-label">Name:</label>
                <input type="text" class="form-control" name="Name" maxlength="100" required>
            </div>
            <div class="mb-3 col-3 mx-auto">
                <label for="Password" class="form-label">Password:</label>
                <input type="password" class="form-control" name="Password" maxlength="100" required>
            </div>
            <div class="mb-3 col-3 mx-auto">
                <label for="Email" class="form-label">Email Id:</label>
                <input type="email" class="form-control" name="Email" maxlength="200" required>
            </div>
            <div class="d-grid gap-2 col-2 mx-auto">
            <button type="submit" class="btn btn-primary">Register</button>
            </div>
            <p class="mt-3 text-center">
                Already Registered? <br>
                <a href="login.php" class="btn btn-link">Login here</a>
            </p>
        </form>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $User_id = $_POST["User_id"];
    $Name = $_POST["Name"];
    $Password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $Email = $_POST["Email"];

    // Check if user_id or email already exists
    $sql = "SELECT * FROM user WHERE User_id = ? OR Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $User_id, $Email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $existing_user = $result->fetch_assoc();
        if ($existing_user['User_id'] == $User_id) {
            echo '<script>alert("This user ID already exists. Please try with another.");</script>';
        }
        if ($existing_user['Email'] == $Email) {
            echo '<script>alert("This email ID already exists. Please try with another.");</script>';
        }
    } else {

        $sql = "INSERT INTO user (User_id, Name, Password, Email) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssss', $User_id, $Name, $Password, $Email);

        if ($stmt->execute()) {
            // Registration successful, redirect to login page
            header('Location: login.php');
            exit();
        } else {
            // If execution fails, display an error message
            echo "Registration failed!!";
            echo "Error: " . $stmt->error;
        }
    }
}
$conn->close();
?>
