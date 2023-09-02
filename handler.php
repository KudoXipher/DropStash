<?php
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
        $file = "download.php?file=" . $randomFileName;

        if (file_exists($filename)) {
            echo "<!DOCTYPE html>";
            echo "<html>";
            echo "<head>";
            echo "<title>Download File</title>";
            echo "<link rel='stylesheet' type='text/css' href='styles/handler.css'>";
            echo "</head>";
            echo "<body>";
            echo "<div class='container'>";
            echo "<h1>Download File: $originalFileName</h1>";

            // Display file information
            echo "<p><strong>File Information:</strong></p>";
            echo "<p><span>File Name:</span> $originalFileName</p>";
            echo "<p><span>File Type:</span> " . mime_content_type($filename) . "</p>";
            echo "<p><span>File Size:</span> " . formatFileSize(filesize($filename)) . "</p>";
            echo "<p><span>Download Link:</span> <a href='$file' download class='file-button'>Click here to download</a></p>";

            echo "</div>";
            echo "</body>";
            echo "</html>";
        } else {
            echo "File not found.";
        }
    } else {
        echo "File not found.";
    }
}

function formatFileSize($size) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = floor(log($size, 1024));
    return @round($size / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>
