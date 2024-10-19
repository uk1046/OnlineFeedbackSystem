<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db_connect.php';

// Add Faculty and Subjects Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_faculty'])) {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $num_subjects = $_POST['num_subjects'];

    // Insert faculty into the faculty table
    $query = "INSERT INTO faculty (name, department, email, phone) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $name, $department, $email, $phone);
    $stmt->execute();
    $faculty_id = $stmt->insert_id; // Get the inserted faculty ID
    $stmt->close();

    // Insert subjects into the subjects table
    for ($i = 1; $i <= $num_subjects; $i++) {
        $subject_name = $_POST['subject_' . $i];
        $query = "INSERT INTO subjects (faculty_id, subject_name) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $faculty_id, $subject_name);
        $stmt->execute();
        $stmt->close();
    }

    $message = "Faculty and subjects added successfully!";
}

// Delete Faculty and related subjects logic
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    $message = "Faculty deleted successfully!";
}

// Retrieve all faculty members, their subjects, and feedback
$query = "SELECT f.*, 
                 GROUP_CONCAT(s.subject_name SEPARATOR ', ') AS subjects,
                 AVG(fe.average_rating) AS average_rating,
                 COUNT(fe.feedback_id) AS total_feedback
          FROM faculty f
          LEFT JOIN subjects s ON f.faculty_id = s.faculty_id
          LEFT JOIN feedback fe ON f.faculty_id = fe.faculty_id
          GROUP BY f.faculty_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Faculty Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .container {
            margin: 50px auto;
            width: 80%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .message {
            color: green;
            font-weight: bold;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        form label {
            margin: 10px 0 5px 0;
        }
        form input, form select {
            padding: 10px;
            width: 80%;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        form #subjects_container {
            width: 80%;
        }
        .subject-field {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            overflow-x: auto;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        table th {
            background-color: #333;
            color: white;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            color: red;
            text-decoration: none;
            font-size: 1.2rem;
        }
    </style>
    <script>
        function generateSubjectFields() {
            var numSubjects = document.getElementById('num_subjects').value;
            var subjectsContainer = document.getElementById('subjects_container');
            subjectsContainer.innerHTML = ''; // Clear previous subjects

            for (var i = 1; i <= numSubjects; i++) {
                var subjectDiv = document.createElement('div');
                subjectDiv.classList.add('subject-field');

                var subjectLabel = document.createElement('label');
                subjectLabel.innerHTML = 'Subject ' + i + ':';
                subjectDiv.appendChild(subjectLabel);

                var subjectInput = document.createElement('input');
                subjectInput.type = 'text';
                subjectInput.name = 'subject_' + i;
                subjectInput.required = true;
                subjectDiv.appendChild(subjectInput);

                subjectsContainer.appendChild(subjectDiv);
            }
        }
    </script>
</head>
<body>

<header>
    <h1>Admin Portal - Faculty Management</h1>
</header>

<div class="container">
    <?php if (isset($message)): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <h2>Add Faculty</h2>
    <form method="POST" action="">
        <label for="name">Faculty Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" required>

        <label for="num_subjects">Number of Subjects:</label>
        <input type="number" id="num_subjects" name="num_subjects" required oninput="generateSubjectFields()">

        <div id="subjects_container"></div>

        <button type="submit" name="add_faculty">Add Faculty</button>
    </form>

    <h2>Current Faculty List with Feedback</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Subjects</th>
            <th>Average Rating</th>
            <th>Total Feedback</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['faculty_id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['department']; ?></td>
            <td><?php echo $row['email']; ?></td>
            <td><?php echo $row['phone']; ?></td>
            <td><?php echo $row['subjects']; ?></td>
            <td>
        <?php 
            // Format the average rating for display
            $averageRating = !is_null($row['average_rating']) ? number_format($row['average_rating'], 2) : 0;
            echo "$averageRating/5"; // Display as "1.5/5" or "0/5" if no ratings are available
        ?>
    </td>
            <td><?php echo $row['total_feedback']; ?></td> <!-- Display total feedback count -->
            <td>
                <a href="edit_faculty.php?id=<?php echo $row['faculty_id']; ?>">Edit</a>
                <a href="?delete_id=<?php echo $row['faculty_id']; ?>" onclick="return confirm('Are you sure you want to delete this faculty?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
