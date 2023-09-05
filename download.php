<?php
$host = 'localhost';
$dbname = 'file_upload_db';
$username = 'root';
$password = '';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the random filename from the URL
$randomFilename = $_GET['file'];

// Fetch the original filename from the database
$sql = "SELECT original_filename FROM uploaded_files WHERE random_filename = '$randomFilename'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $originalFilename = $row["original_filename"];
    }
} else {
    echo "No results";
}
$conn->close();

// Display the download button
echo "<style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
      </style>";
echo "<div class='container'>
        <button onclick=\"window.location.href='uploads/$randomFilename'\">Download $originalFilename</button>
      </div>";
?>
