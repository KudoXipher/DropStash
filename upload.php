<?php
        function generateRandomString($length = 12) {
            $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

        // Database connection
        // Maximum file size allowed (in bytes)
$maxFileSize = 104857600; // 100 MB

// Destination directory to save uploaded files
$uploadDirectory = 'uploads/';

// Database connection
$host = 'localhost';
$dbname = 'file_links';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {
    $uploadedFile = $_FILES['fileToUpload'];

    // Check for errors during file upload
    if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
        // Check file size
        if ($uploadedFile['size'] <= $maxFileSize) {
            $originalFilename = basename($uploadedFile['name']);
            $randomFileName = generateRandomString();
            $destination = $uploadDirectory . $randomFileName . '-' . $originalFilename;

            if (move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
                echo "The file " . $originalFilename . " has been uploaded.";

                // Store the mapping in the database
                $stmt = $db->prepare("INSERT INTO files (random_string, original_filename) VALUES (?, ?)");
                $stmt->execute([$randomFileName, $originalFilename]);

                $shortDownloadLink = "http://localhost/$randomFileName";
                echo "<div class='download-link'>";
                if (isset($shortDownloadLink)) {
                    echo "Download link: <a href='$shortDownloadLink'>$originalFilename</a>";
                    echo "<button class='copy-link-button' onclick='copyToClipboard(\"$shortDownloadLink\")'>Copy Link</button>";
                }
                echo "</div>";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File size exceeds the maximum allowed size.";
        }
    } else {
        echo "Error during file upload: " . $uploadedFile['error'];
    }
}

        // Handle file download if file name is in query parameter
        if (isset($_GET["file"])) {
            $randomFileName = $_GET["file"];

            // Retrieve original filename from the database
            $stmt = $db->prepare("SELECT original_filename FROM files WHERE random_string = ?");
            $stmt->execute([$randomFileName]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $originalFileName = $row["original_filename"];
                $filename = "uploads/" . $randomFileName . '-' . $originalFileName;

                if (file_exists($filename)) {
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $originalFileName . '"');
                    readfile($filename);
                } else {
                    echo "File not found.";
                }
            } else {
                echo "File not found.";
            }
        }
        ?>