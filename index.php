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
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["file"])) {
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
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "file_upload_db";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

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
    $downloadLink = "localhost/" . $randomString;
    $deleteLink = "localhost/delete/" . $deleteString;

    // Display the success message and the links using echo
    echo "<h3>File uploaded successfully</h3>";
    echo "<style>
            body {
                font-family: Arial, sans-serif;
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
                padding: 10px 20px; /* Increase padding */
                border: 1px solid #ccc;
                border-radius: 5px 0 0 5px; /* Adjust border radius */
                outline: none;
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
            .delete-button { background-color: #ff0000; } /* Light red color */
          </style>";
          echo "<h5 class='tite'>" . $originalName. " (".$fileSizeFormatted.")</h5>";
          echo "<p class='tite'>Download</p>";
    echo "<div class='container'>
            <input type='text' value='$downloadLink' readonly>
            <button onclick=\"window.location.href='$downloadLink'\" class='download-button'>Go</button>
          </div>";
          echo "<p class='tite'>Delete</p>";
    echo "<div class='container'>
            <input type='text' value='$deleteLink' readonly>
            <button onclick=\"window.location.href='$deleteLink'\" class='delete-button'>Go</button>
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload with Progress Bar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Roboto', sans-serif;
        }
        body {
            text-align: center;
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
        #progress {
            width: 100%;
            height: 30px;
            background-color: #f1f1f1;
            position: relative;
            margin-top: 10px;
        }
        #bar {
            width: 0;
            height: 100%;
            background-color: #4caf50;
            position: relative;
        }
        #percent {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
        }
        #fileInput {
            display: none;
        }
        #chooseFileBtn {
            padding: 8px 16px;
            background-color: #4caf50;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        #chooseFileBtn:hover {
            background-color: #45a049;
        }
        #upload-container.dragover {
            border: 2px dashed #4caf50;
            background-color: #f9f9f9;
        }
        #cancelBtn {
            padding: 8px 16px;
            background-color: #ff4d4d; /* Red color */
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div id="upload-container" class="dragover">
        <h2>Simple File Hosting</h2>
        <label id="chooseFileBtn" for="fileInput">Choose File</label>
        <input type="file" id="fileInput">
        <p id="or-area">or</p>
        <div id="drag-drop-area">
            <p>Drag and drop any files here</p>
        </div>
        <button id="cancelBtn" style="display: none;">Cancel</button>
        <div id="progress">
            <div id="bar"></div>
            <div id="percent">0%</div>
        </div>
    </div>
    <script>
        const fileInput = document.getElementById("fileInput");
        const bar = document.getElementById("bar");
        const percent = document.getElementById("percent");
        const chooseFileBtn = document.getElementById("chooseFileBtn");
        const cancelBtn = document.getElementById("cancelBtn");
        const uploadContainer = document.getElementById("upload-container");
        const dragDropArea = document.getElementById("drag-drop-area");
        const OrArea = document.getElementById("or-area");
        const fileNameElement = document.getElementById("file-name");
        const fileSizeElement = document.getElementById("file-size");

        let xhr;

        fileInput.addEventListener("change", () => {
            const file = fileInput.files[0];
            if (file) {
                uploadFile(file);
            }
        });

        uploadContainer.addEventListener("dragenter", (e) => {
            e.preventDefault();
            uploadContainer.classList.add("dragover");
        });

        uploadContainer.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        uploadContainer.addEventListener("dragleave", () => {
            uploadContainer.classList.remove("dragover");
        });

        uploadContainer.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadContainer.classList.remove("dragover");
            const file = e.dataTransfer.files[0];
            if (file) {
                uploadFile(file);
            }
        });

        cancelBtn.addEventListener("click", () => {
            if (xhr && xhr.readyState !== XMLHttpRequest.DONE) {
                xhr.abort();
            }
            location.reload(); // Reload the page to start over
        });

        function uploadFile(file) {
            OrArea.style.display = "none";
            dragDropArea.style.display = "none";
            chooseFileBtn.style.display = "none";
            cancelBtn.style.display = "inline-block";
            
            xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);

    xhr.onload = function () {
    if (xhr.status === 200) {
        // Insert the response HTML into the page
        document.getElementById('upload-container').innerHTML = xhr.responseText;
    } else {
        alert('File upload failed');
    }
};

            xhr.upload.addEventListener("progress", (event) => {
        const progress = (event.loaded / event.total) * 100;
        bar.style.width = `${progress}%`;
        percent.innerText = `${Math.round(progress)}%`;

        if (progress === 100) {

            fileNameElement.textContent = file.name;
            fileSizeElement.textContent = formatFileSize(file.size);

            resetProgress();
            setTimeout(() => {
                percent.innerText = "";
            }, 1000); // Reset progress after 1 second
        }
    });

    const formData = new FormData();
    formData.append("file", file);
    xhr.send(formData);
}


        function resetProgress() {
            chooseFileBtn.style.display = "inline-block";
            cancelBtn.style.display = "none";
            bar.style.width = "0";
            percent.innerText = "0%";
        }
        
    </script>
</body>
</html>
