<?php
function formatFileSize($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $index = 0;
    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }
    return round($bytes, 2) . ' ' . $units[$index];
}
// Check if the request method is POST and a file is uploaded
// Check if the request method is POST and a file is uploaded
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
    // Check if the file size exceeds 10 MB
    if ($_FILES["file"]["size"] > 10 * 1024 * 1024) {
        echo "File size exceeded 10 MB";
        exit;
    }

    $uploadsDir = "uploads/";
    $targetFileName = basename($_FILES["file"]["name"]);

    // Generate a random string of 5 uppercase letters
    $randomString = substr(str_shuffle("AB1CDE2FG3HI4JKL5MNO6PQ7RST8UV9WXYZ"), 0, 5);
    $deleteString = substr(str_shuffle("AB1CDE2FG3HI4JKL5MNO6PQ7RST8UV9WXYZ"), 0, 5);

    // Create a new filename using the random string
    $fileExtension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
    $newFileName = $randomString . "_" . $targetFileName;
    $targetFile = $uploadsDir . $newFileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        // Connect to your database (replace with your database credentials)
        include_once 'db_connect.php';

        // Get file information
        $originalName = $_FILES["file"]["name"];
        $fileType = $_FILES["file"]["type"];
        $fileSize = $_FILES["file"]["size"];
        $fileSizeFormatted = formatFileSize($fileSize);
        $uploadedDate = date("Y-m-d H:i:s");

        // Calculate expiration date (e.g., 7 days from the uploaded date)
        // Calculate expiration date (1 year from the uploaded date)
$expirationDate = date("Y-m-d H:i:s", strtotime("+1 year", strtotime($uploadedDate)));

// Insert file data into the database
$sql = "INSERT INTO uploaded_files (new_filename, random_filename, original_filename, delete_filename, file_extension,file_type, file_size, uploaded_date, expiration_date)
        VALUES ('$newFileName', '$randomString', '$originalName', '$deleteString', '$fileExtension', '$fileType', '$fileSizeFormatted', '$uploadedDate', '$expirationDate')";


if ($conn->query($sql) === TRUE) {
    // Generate the download and delete links
    $downloadLink = "" . $randomString;
    $deleteLink = "delete/" . $deleteString;

    // Display the success message and the links using echo
    echo "<h3>File uploaded successfully</h3>";
    echo "<style>
            body {
                font-family: 'Fira Code', monospace;
                margin: 0;
                padding: 0;
            }
            .container {
                display: flex;
                justify-content: flex-start; /* Align items to the left */
                align-items: center;
            }
            .container p {
                flex-basis: 100%; /* Make titles take full width */
            }
            .container input[type='text'] {
                flex-grow: 1; /* Allow the input to grow and shrink */
                padding: 9px 20px; /* Increase padding */
                border: 1px solid #ccc;
                border-radius: 5px 0 0 5px; /* Adjust border radius */
                outline: none;
                width: 100%;
            }
            .container button {
                padding: 10px 20px;
                color: white;
                border: none;
                cursor: pointer;
                border-radius: 0 5px 5px 0; /* Adjust border radius */
            }
            .tite {
            
                    display: flex;
                    align-items: center;
                    margin: 0px;
                    margin-top: 20px;
             
            }
            .download-button { background-color: #4CAF50; } /* Green color */
            .delete-button { background-color: #ff4d4d; } /* Light red color */
          </style>";
          echo "<h5 class='tite'>" . $originalName. " (".$fileSizeFormatted.")</h5>";
          echo "<p class='tite'>Download</p>";
    echo "<div class='container'>
    <input type='text' value='http://domain.com/$downloadLink' readonly onclick='copyToClipboard(this);'>
    <button onclick=\"window.location.href='$downloadLink'\" class='download-button'>Navigate</button>
</div>";
          echo "<p class='tite'>Delete</p>";
    echo "<div class='container'>
    <input type='text' value='http://domain.com/$deleteLink' readonly onclick='copyToClipboard(this);'>
    <button onclick=\"window.location.href='$deleteLink'\" class='delete-button'>Navigate</button>
</div>";
    exit;
} else {
    echo "File upload failed";
    exit;
}



        $conn->close();
    } else {
        // Return an error response
        http_response_code(400);
        echo "<script>alert('File upload failed');</script>";
    }
}
?>