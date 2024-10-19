<?php
// Connect to the database
require_once 'db_connect.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute query to find the student by email
    $stmt = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $student['password'])) {
            // Start a session and store student information
            session_start();
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['name'];

            // Redirect to a dashboard or another page after successful login
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "Invalid email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s;
        }

        .container {
            margin: 50px auto;
            width: 40%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        form label {
            margin: 10px 0 5px 0;
        }

        form input {
            padding: 10px;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        form button {
            padding: 10px 20px;
            background-color: #333; /* Dark gray color for the button */
            color: white; /* White text color */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #555; /* Lighter gray on hover */
        }

        .message {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        .signup-btn {
            margin-top: 10px; /* Space above the button */
            background-color: #333; /* Blue color for the button */
            border: none; /* Remove border */
            color: white; /* White text color */
            padding: 10px; /* Space for button */
            cursor: pointer; /* Pointer cursor on hover */
            border-radius: 4px; /* Rounded corners */
            transition: background-color 0.3s; /* Smooth transition */
        }

        .signup-btn:hover {
            background-color: #555; /* Darker blue on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Login</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Log In</button>
    </form>

    <form action="student_signup.php" method="get">
        <button type="submit" class="signup-btn">Create an Account</button> <!-- Button to go to signup page -->
    </form>
</div>

</body>
</html>
