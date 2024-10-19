<?php
// Connect to the database
require_once 'db_connect.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $roll_number = $_POST['roll_number']; // Get roll number

    // Check if email already exists
    $check_email = $conn->prepare("SELECT * FROM students WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $message = "Email already exists!";
    } else {
        // Insert student into the database
        $query = "INSERT INTO students (name, email, password, roll_number) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $name, $email, $password, $roll_number);
        
        if ($stmt->execute()) {
            $message = "Signup successful! You can now log in.";
        } else {
            $message = "Signup failed, please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Signup</title>
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
            transition: transform 0.3s;
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
            color: green;
            font-weight: bold;
            text-align: center;
        }

        .login-btn {
            padding: 10px; /* Space for button */
            background-color: #333; /* Dark gray color for the button */
            color: white; /* White text color */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition */
            margin-top: 10px; /* Space above the button */
        }

        .login-btn:hover {
            background-color: #555; /* Lighter gray on hover */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Signup</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="roll_number">Roll Number:</label>
        <input type="text" id="roll_number" name="roll_number" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Sign Up</button>
    </form>

    <form action="student_login.php" method="get">
        <button type="submit" class="login-btn">Login</button> 
    </form>
</div>

</body>
</html>
