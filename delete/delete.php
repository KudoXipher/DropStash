<?php
include_once '../includes/patcher.php';
// Connect to your database (replace with your database credentials)
include_once '../includes/db_connect.php';

// Get the delete_string from the URL
$deleteString = $_GET['delete'];


// Initialize variables
$originalName = "";
$uploadedDate = "";
$expirationDate = "";
$randomFileName = "";
$fileSize = "";

// Use prepared statement to fetch data from the database
$sql = "SELECT file_size, original_filename, uploaded_date, expiration_date, random_filename FROM uploaded_files WHERE delete_filename = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $deleteString);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $originalName = $row["original_filename"];
        $uploadedDate = $row["uploaded_date"];
        $expirationDate = $row["expiration_date"];
        $randomFileName = $row["random_filename"];
        $fileSize = $row["file_size"];
    }
} else {
    // If the randomFileName doesn't exist in the database, redirect to index.php
    header("Location: ../index.php");
    exit;
}

// If the request method is POST, delete the file
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Use prepared statement to delete data from the database
    $deleteSql = "DELETE FROM uploaded_files WHERE delete_filename = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("s", $deleteString);
    
    if ($deleteStmt->execute()) {
        // Check if the file exists in the uploads directory
        $filePath = "../uploads/".$randomFileName."_".$originalName;
        if (file_exists($filePath)) {
            // Attempt to delete the file from the server
            if (!unlink($filePath)) {
                echo "Error deleting the file.";
            }
        }
        // Redirect to index.php
        header("Location: ../index.php");
        exit;
    } else {
        echo "Error deleting file: " . $conn->error;
        exit;
    }
}

$conn->close();

// Calculate the number of days remaining until the expiration date
$currentDate = new DateTime();
$expirationDateObj = new DateTime($expirationDate);
$interval = $currentDate->diff($expirationDateObj);
$daysRemaining = $interval->format('%a');

echo "

    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Delete File</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        #upload-container {
            max-width: 400px;
            padding: 20px;
            border: 1px solid #333333;
            border-radius: 5px;
            background-color: #333333;
            position: relative;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        #upload-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background-color: #4CAF50; /* Green color */
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
            font-size: 80%;
        }

        h2 {
            color: #ffffff;
        }

        p {
        
                color: #ffffff;
                margin-bottom: 10px;
                font-size: 14px;
                line-height: 1.5;
           
        }

        strong {
            font-weight: bold;
        }

        small {
            font-size: 80%;
        }

        #deleteButton {
            padding: 10px 20px;
            background-color: #ff4d4d; /* Red color */
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        #deleteButton:hover {
            background-color: #ff3333;
        }
        form {
            display: block;
            margin-top: 0;
            margin-block-end: 0;
        }
    </style>

    <div id='upload-container'>
        <h2>Delete File</h2>
        <button id='upload-button' onclick=\"window.location.href='../index.php'\">Upload File</button>
        <p><strong>{$originalName}</strong> ({$fileSize})</p>
        <p>Uploaded on: <strong>" . date('M j, Y g:i A', strtotime($uploadedDate)) . "</strong></p>
        <p>Accessible until: <strong>" . date('M j, Y g:i A', strtotime($expirationDate)) . "</strong> ({$daysRemaining} days remaining)</p>
        <form method='post'>
            <input type='submit' id='deleteButton' value='Delete'>
        </form>
    </div>";
?>


