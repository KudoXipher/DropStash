<?php
// Connect to your database (replace with your database credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "file_upload_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the random filename from the URL
$randomFilename = $_GET['file'];

// Fetch the original filename, uploaded date, and expiration date from the database using the delete_string
$sql = "SELECT original_filename, uploaded_date, random_filename FROM uploaded_files WHERE random_filename = '$randomFilename'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $originalName = $row["original_filename"];
        $uploadedDate = $row["uploaded_date"];
        $randomFileName = $row["random_filename"];
    }
} else {
    echo "No results";
    exit;
}

$conn->close();

$filename = "uploads/".$randomFilename."_".$originalName;

if (isset($_GET['confirm'])) {
    if (file_exists($filename)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $originalName . '"');
        readfile($filename);
    } else {
        echo "File not found.";
    }
} else {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Download File</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400&display=swap');

            body {
                font-family: 'Roboto', sans-serif;
                text-align: left;
                margin: 0;
                padding: 0;
            }

            #upload-container {
                max-width: 400px;
                margin: 50px auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background-color: #f9f9f9;
            }

            #downloadButton {
                padding: 10px 20px;
                background-color: #007bff; /* Blue color */
                border: none;
                color: white;
                cursor: pointer;
                border-radius: 4px;
            }

            #uploadButton {
                position: absolute;
                top: 10px;
                right: 10px;
                padding: 10px 20px;
                background-color: #4CAF50; /* Green color */
                color: white;
                border: none;
                cursor: pointer;
                border-radius: 4px;
            }
        </style>
    </head>
    <body>
        <button id='uploadButton' onclick=\"window.location.href='index.php'\">Upload</button>
        <div id='upload-container'>
            <h2>Download File</h2>
            <p>File Name: {$originalName}</p>
            <p>Uploaded on: {$uploadedDate}</p>
            <a href='?file={$randomFilename}&confirm=true' id='downloadButton'>Download</a>
        </div>
    </body>
    </html>";
}
?>
