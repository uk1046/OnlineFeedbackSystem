<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php"); // Redirect if not logged in
    exit();
}

// Connect to the database
require_once 'db_connect.php';

$message = '';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_feedback'])) {
    $faculty_id = $_POST['faculty_name'];
    $subject = $_POST['subject'];
    $student_name = $_SESSION['student_name']; // Assuming the student's name is stored in the session
    $extra_feedback = $_POST['extra_feedback'];

    // Get ratings for the questions
    $ratings = [];
    for ($i = 1; $i <= 10; $i++) {
        $ratings[$i] = intval($_POST["rating_$i"]);
    }

    // Calculate average rating
    $average_rating = array_sum($ratings) / count($ratings);

    // Insert feedback into the database with individual ratings
    $stmt = $conn->prepare("INSERT INTO feedback (
        faculty_id, student_name, subject, rating_1, rating_2, rating_3, rating_4, rating_5,
        rating_6, rating_7, rating_8, rating_9, rating_10, average_rating, feedback_text
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "issiiiiiiiiiiis", 
        $faculty_id, $student_name, $subject, $ratings[1], $ratings[2], $ratings[3], $ratings[4], 
        $ratings[5], $ratings[6], $ratings[7], $ratings[8], $ratings[9], $ratings[10], 
        $average_rating, $extra_feedback
    );

    if ($stmt->execute()) {
        $message = "Feedback submitted successfully!";
    } else {
        $message = "Failed to submit feedback, please try again.";
    }

    $stmt->close();
}

// Fetch faculty data for dropdown
$faculty_query = "SELECT DISTINCT faculty_id, name FROM faculty"; // Faculty names from the admin database
$faculty_result = $conn->query($faculty_query);

// Check if a faculty is selected to fetch corresponding subjects
$selected_faculty_id = isset($_POST['faculty_name']) ? $_POST['faculty_name'] : null;
$subjects = [];

if ($selected_faculty_id) {
    // Fetch subjects for the selected faculty
    $subject_query = "SELECT subject_id, subject_name FROM subjects WHERE faculty_id = ?";
    $stmt = $conn->prepare($subject_query);
    $stmt->bind_param("i", $selected_faculty_id); // Bind faculty ID
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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

        form input, form select, form textarea {
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
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #555;
        }

        .message {
            color: green;
            font-weight: bold;
            text-align: center;
        }

        .question {
            margin: 10px 0;
        }

        .rating {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Student Dashboard</h2>
    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="faculty_name">Faculty Name:</label>
        <select id="faculty_name" name="faculty_name" onchange="this.form.submit()" required>
            <option value="">Select Faculty</option>
            <?php while ($row = $faculty_result->fetch_assoc()): ?>
                <option value="<?php echo $row['faculty_id']; ?>" <?php if ($selected_faculty_id == $row['faculty_id']) echo 'selected'; ?>>
                    <?php echo $row['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <?php if (!empty($subjects)): ?>
            <label for="subject">Subject:</label>
            <select id="subject" name="subject" required>
                <option value="">Select Subject</option>
                <?php foreach ($subjects as $subject): ?>
                    <option value="<?php echo $subject['subject_name']; ?>"><?php echo $subject['subject_name']; ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>

        <h3>Rate the Faculty:</h3>
        <?php
        $questions = [
            "Description of course objective and assignments",
            "Communication of ideas and information",
            "Expression of expectations for performance",
            "Availability to assist students in or out of class",
            "Report or concern for students",
            "Stimulation of interest in course",
            "Facilitation of learning",
            "Enthusiasm for the students",
            "Encouragement for students to think independently, creatively, and critically",
            "Overall rating"
        ];
        ?>
        <?php for ($i = 1; $i <= 10; $i++): ?>
            <div class="question">
                <label>Question <?php echo $i; ?>: <?php echo $questions[$i - 1]; ?></label>
                <div class="rating">
                    <label><input type="radio" name="rating_<?php echo $i; ?>" value="1" required> 1 - Poor</label>
                    <label><input type="radio" name="rating_<?php echo $i; ?>" value="2"> 2 - Fair</label>
                    <label><input type="radio" name="rating_<?php echo $i; ?>" value="3"> 3 - Good</label>
                    <label><input type="radio" name="rating_<?php echo $i; ?>" value="4"> 4 - Very Good</label>
                    <label><input type="radio" name="rating_<?php echo $i; ?>" value="5"> 5 - Excellent</label>
                </div>
            </div>
        <?php endfor; ?>

        <label for="extra_feedback">Additional Feedback:</label>
        <textarea id="extra_feedback" name="extra_feedback" rows="4" placeholder="Your feedback here..."></textarea>

        <button type="submit" name="submit_feedback">Submit Feedback</button>
    </form>
</div>

</body>
</html>
