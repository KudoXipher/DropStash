
<!DOCTYPE html>
<html>
<head>
    <title>Simple File Hosting</title>
    <link rel="stylesheet" type="text/css" href="styles/index.css">
    <!-- Add a link to the Font Awesome library for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <div class="header-content">
                    <div class="logo">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>AirMB</span>
                    </div>
                    <ul class="nav-links">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="container">
        <h1>Simple File Hosting Platform</h1>
        <form class="file-form" action="" method="post" enctype="multipart/form-data" onsubmit="uploadFile()">
    <label class="file-label" for="fileToUpload">Choose a file:</label>
    <input class="file-input" type="file" name="fileToUpload" id="fileToUpload" onchange="updateFileInfo()">
    <input class="file-button" type="submit" value="Upload File" name="submit">
    <div id="progress">
        <progress class="upload-progress" value="0" max="100" id="bar"></progress>
        <span class="progress-number" id="percent">0%</span>
    </div>
</form>

        <div class="file-info" id="fileInfo">
            <p><strong>File Information:</strong></p>
            <p><span id="fileNameLabel">File Name:</span> <span id="fileName"></span></p>
            <p><span id="fileTypeLabel">File Type:</span> <span id="fileType"></span></p>
            <p><span id="fileSizeLabel">File Size:</span> <span id="fileSize"></span></p>
            <p><span id="fileLastModifiedLabel">Last Modified:</span> <span id="fileLastModified"></span></p>
        </div>
        
    </div>
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
    </div>
    <div class="features-section">
        <div class="container">
            <h2>Why Choose AirMB?</h2>
            <div class="feature">
                <i class="fas fa-cloud-upload-alt"></i>
                <h3>Free File Hosting</h3>
                <p>Enjoy the convenience of hosting your files without any cost. Our platform offers reliable hosting solutions for your files.</p>
            </div>
            <div class="feature">
                <i class="fas fa-file"></i>
                <h3>File Size Limit</h3>
                <p>Upload files with a maximum size of 100MB. Whether it's documents, images, or more, we've got you covered.</p>
            </div>
            <div class="feature">
                <i class="fas fa-lock"></i>
                <h3>File Security</h3>
                <p>Your files are safe with us. We prioritize security and take measures to ensure the protection of your uploaded content.</p>
            </div>
        </div>
    </div>
    <div class="about-section">
        <div class="container">
            <h2>About AirMB</h2>
            <p>AirMB is a minimalist file hosting platform that enables you to share files quickly and securely. Our focus is on simplicity, privacy, and ease of use.</p>
        </div>
    </div>
    <div class="how-to-section">
        <div class="container">
            <h2>How It Works</h2>
            <div class="step">
                <h3>1. Choose a File</h3>
                <p>Select the file you want to upload from your device.</p>
            </div>
            <div class="step">
                <h3>2. Click Upload</h3>
                <p>Click the "Upload File" button to upload your selected file.</p>
            </div>
            <div class="step">
                <h3>3. Get Your Link</h3>
                <p>Once uploaded, you'll receive a link to share your file.</p>
            </div>
        </div>
    </div>
    </div>
    <footer>
        <div class="container">
            <p>&copy; 2023 AirMB. All rights reserved.</p>
        </div>
    </footer>
    <script>

    function copyToClipboard(text) {
        const tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        alert("Link copied to clipboard!");
    }
    const fileInput = document.getElementById("fileToUpload");
    const fileInfo = document.getElementById("fileInfo");
    const fileNameLabel = document.getElementById("fileNameLabel");
    const fileTypeLabel = document.getElementById("fileTypeLabel");
    const fileSizeLabel = document.getElementById("fileSizeLabel");
    const fileLastModifiedLabel = document.getElementById("fileLastModifiedLabel");
    const fileName = document.getElementById("fileName");
    const fileType = document.getElementById("fileType");
    const fileSize = document.getElementById("fileSize");
    const fileLastModified = document.getElementById("fileLastModified");

    fileInput.addEventListener("change", function() {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            fileName.textContent = file.name;
            fileType.textContent = file.type || "Unknown";
            fileSize.textContent = formatFileSize(file.size);
            fileLastModified.textContent = new Date(file.lastModified).toLocaleString();

            // Show the spans
            fileName.style.display = "inline";
            fileType.style.display = "inline";
            fileSize.style.display = "inline";
            fileLastModified.style.display = "inline";

            fileInfo.style.display = "block"; // Show the file information
        } else {
            fileInfo.style.display = "none"; // Hide the file information if no file selected
        }
    });

        function formatFileSize(size) {
            if (size === 0) return "0 Bytes";
            const units = ["Bytes", "KB", "MB", "GB", "TB"];
            const i = parseInt(Math.floor(Math.log(size) / Math.log(1024)));
            return Math.round(size / Math.pow(1024, i), 2) + " " + units[i];
        }
        function uploadFile() {
    const fileInput = document.getElementById("fileToUpload");
    const uploadProgress = document.getElementById("bar");
    const progressNumber = document.getElementById("percent");

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const xhr = new XMLHttpRequest();

        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const percent = (event.loaded / event.total) * 100;
                uploadProgress.value = percent;           // Update the progress bar value
                progressNumber.textContent = percent.toFixed(2) + "%"; // Update the progress text
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200) {
                // Handle successful upload
                console.log("File uploaded successfully");
            } else {
                // Handle upload error
                console.error("File upload failed");
            }
        };

        xhr.open("POST", "upload.php", true);
        const formData = new FormData();
        formData.append("fileToUpload", file);
        xhr.send(formData);
    }
    return false; // Prevent default form submission
}


    </script>
</body>
</html>
