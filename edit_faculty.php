<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once 'db_connect.php';

// Fetch faculty details to edit
if (isset($_GET['id'])) {
    $faculty_id = $_GET['id'];

    $query = "SELECT * FROM faculty WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();

    if (!$faculty) {
        die("Faculty not found.");
    }

    // Fetch subjects for this faculty
    $query = "SELECT subject_name FROM subjects WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $subjects_result = $stmt->get_result();

    $subjects = [];
    while ($row = $subjects_result->fetch_assoc()) {
        $subjects[] = $row['subject_name'];
    }
}

// Update faculty details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_faculty'])) {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $num_subjects = $_POST['num_subjects'];

    // Update faculty details
    $query = "UPDATE faculty SET name = ?, department = ?, email = ?, phone = ? WHERE faculty_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $department, $email, $phone, $faculty_id);
    $stmt->execute();

    // Update subjects
    $stmt = $conn->prepare("DELETE FROM subjects WHERE faculty_id = ?");
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();

    for ($i = 1; $i <= $num_subjects; $i++) {
        $subject_name = $_POST['subject_' . $i];
        $query = "INSERT INTO subjects (faculty_id, subject_name) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $faculty_id, $subject_name);
        $stmt->execute();
    }

    header("Location: admin_dashboard.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Faculty</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 50px auto;
            width: 50%;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Faculty</h2>
    <form method="POST" action="">
        <label for="name">Faculty Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $faculty['name']; ?>" required>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" value="<?php echo $faculty['department']; ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $faculty['email']; ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?php echo $faculty['phone']; ?>" required>

        <label for="num_subjects">Number of Subjects:</label>
        <input type="number" id="num_subjects" name="num_subjects" value="<?php echo count($subjects); ?>" required oninput="generateSubjectFields()">

        <div id="subjects_container">
            <?php foreach ($subjects as $index => $subject): ?>
                <div class="subject-field">
                    <label for="subject_<?php echo $index + 1; ?>">Subject <?php echo $index + 1; ?>:</label>
                    <input type="text" id="subject_<?php echo $index + 1; ?>" name="subject_<?php echo $index + 1; ?>" value="<?php echo $subject; ?>" required>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit" name="update_faculty">Update Faculty</button>
    </form>
</div>

</body>
</html>
