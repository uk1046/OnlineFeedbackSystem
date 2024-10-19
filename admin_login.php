<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<style>
    /* General Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

body {
    background-color: #fff;
    color: #000;
    display: flex;
    flex-direction: column;
    height: 100vh;
    justify-content: space-between;
}

/* Header */
header {
    background-color: #f1f1f1;
    padding: 20px;
    text-align: center;
    border-bottom: 2px solid #ccc;
}

header h1 {
    font-size: 2.5rem;
    letter-spacing: 2px;
    color: #000;
}

/* Container */
.container {
    text-align: center;
    margin: auto;
    width: 40%;
    padding: 40px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

form label {
    margin-top: 20px;
    font-size: 1.1rem;
}

form input {
    padding: 10px;
    width: 100%;
    max-width: 300px;
    margin-top: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.btn {
    background-color: #000;
    color: white;
    border: 2px solid #000;
    padding: 10px 20px;
    text-decoration: none;
    font-size: 1rem;
    margin-top: 20px;
    transition: all 0.3s ease;
    border-radius: 5px;
    cursor: pointer;
}

.btn:hover {
    background-color: white;
    color: black;
}

/* Error message */
.error {
    color: red;
    margin-bottom: 10px;
}

/* Footer */
footer {
    background-color: #f1f1f1;
    padding: 10px;
    text-align: center;
    border-top: 2px solid #ccc;
    font-size: 0.9rem;
    color: #555;
}

/* Styling for Back to Home Button */
.back-home {
    margin-top: 20px;
}

.back-home .btn {
    background-color: #000;
    color: white;
    border: 2px solid #000;
    padding: 10px 20px;
    text-decoration: none;
    font-size: 1rem;
    transition: all 0.3s ease;
    border-radius: 5px;
    cursor: pointer;
}

.back-home .btn:hover {
    background-color: white;
    color: black;
}


</style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    
</head>
<body>
    <!-- Header Section -->
    <header>
        <h1>Admin Login</h1>
    </header>

    <!-- Main Content Section -->
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form action="process_admin_login.php" method="POST">
            <label for="admin_id">Admin ID:</label>
            <input type="text" id="admin_id" name="admin_id" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" class="btn">Login</button>
        </form>

        <div class="back-home">
            <a href="index.php" class="btn">Back to Home</a>
        </div>

    </div>

    

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Online Feedback System. All Rights Reserved.</p>
    </footer>
</body>
</html>
