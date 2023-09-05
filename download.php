<?php
include_once 'includes/patcher.php';
// Connect to your database (replace with your database credentials)
include_once 'includes/db_connect.php';

// Get the random filename from the URL
$randomFilename = $_GET['file'];

// Fetch the original filename, uploaded date, and expiration date from the database
$sql = "SELECT file_size, original_filename, uploaded_date, expiration_date FROM uploaded_files WHERE random_filename = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $randomFilename);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: index.php");
        exit;
    }

    if ($result->num_rows > 0) {
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            $originalFilename = $row["original_filename"];
            $uploadedDate = $row["uploaded_date"];
            $expirationDate = $row["expiration_date"];
            $fileSize = $row["file_size"];
        }
    }

    $stmt->close();
} else {
    // Handle database query error
    echo "Database query error: " . $conn->error;
    exit;
}

function fileSizeToBytes($fileSize) {
    $sizeParts = explode(' ', $fileSize);
    $sizeValue = (float) $sizeParts[0];
    $sizeUnit = strtoupper($sizeParts[1]);

    switch ($sizeUnit) {
        case 'B':
            return $sizeValue;
        case 'KB':
            return $sizeValue * 1024;
        case 'MB':
            return $sizeValue * 1024 * 1024;
        case 'GB':
            return $sizeValue * 1024 * 1024 * 1024;
        case 'TB':
            return $sizeValue * 1024 * 1024 * 1024 * 1024;
        default:
            return false; // Invalid unit
    }
}

$fileSizeInBytes = fileSizeToBytes($fileSize);

$currentDate = new DateTime();
$expirationDateObj = new DateTime($expirationDate);
$interval = $currentDate->diff($expirationDateObj);
$daysRemaining = $interval->format('%a');

$conn->close();

$filename = "uploads/" . $randomFilename . "_" . $originalFilename;

if (isset($_GET['confirm'])) {
        if (file_exists($filename)) {
            // Get the 'Content-Type' for the image
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
           
            // Check if the file is an APK file
if (pathinfo($filename, PATHINFO_EXTENSION) == 'apk') {
    $mime = 'application/vnd.android.package-archive';
}else{
     $mime = finfo_file($finfo, $filename);
}
            finfo_close($finfo);
    
            // Get the 'Content-Length'
            $size = filesize($filename);
    
            // Send headers
            header("Content-Type: $mime");
            header("Content-Length: " . $size);
            header('Content-Disposition: attachment; filename="' . $originalFilename . '"');
            header('Accept-Ranges: bytes');
    
            // Check if download range is set
            if(isset($_SERVER['HTTP_RANGE'])) {
                list($a, $range) = explode("=", $_SERVER['HTTP_RANGE']);
                str_replace($range, "-", $range);
                list($range) = explode(",", $range);
                list($range, $end) = explode("-", $range);
                $range=intval($range);
                if(!$end) {
                    $end=$size-1;
                } else {
                    $end=intval($end);
                }
                header("HTTP/1.1 206 Partial Content");
                header("Content-Length: " . ($end-$range+1));
                header("Content-Range: bytes $range-$end/$size");
            } else {
                $end=$size-1;
            }
    
            // Open and output file contents
            $fp = fopen("$filename", "r");
            fseek($fp, $range);
            while(!feof($fp) && ($p=ftell($fp))<=$end) {
                if ($p + 1024 < $end) {
                    echo fread($fp, 1024);
                    flush();
                } else {
                    echo fread($fp, $end - $p + 1);
                    flush();
                }
            }
            fclose($fp);
        } else {
            echo 'File not found.';
        }
} else {
    // Display interface
    echo "
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Download File</title>
    <style>
    img[src*='https://cdn.000webhost.com/000webhost/logo/footer-powered-by-000webhost-white2.png'] {display: none;}
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
    font-weight:bold; }
    
    small {
    font-size :80%; }
    
    .download-button, .report-button {
    padding :8px 16px; background-color :#4CAF50; /* Green color */ color :white; border :none; cursor :pointer; border-radius :4px; font-size :14px; font-weight :bold; display :inline-flex; align-items :center; margin-right :10px; text-decoration: none;}
    
    .report-button {background-color:#ff4d4d; /* Red color */ position:absolute; right :0px; padding :5px 10px; border:none; color:white; cursor:pointer; border-radius :4px; font-size :80%; }
    </style>";

    echo "<div id='upload-container'>
    <h2>Download File</h2>
    <button id='upload-button' onclick=\"window.location.href='index.php'\">Upload File</button>
    <p><strong>{$originalFilename}</strong> ({$fileSize})</p>
    <p>Uploaded on:<strong>" . date('M j, Y g:i A', strtotime($uploadedDate)) . "</strong></p>
    <p>Accessible until:<strong>" . date('M j, Y g:i A', strtotime($expirationDate)) . "</strong> ({$daysRemaining} days remaining)</p>
    <a class='download-button' href='" . basename($_SERVER['PHP_SELF']) . "?" . $_SERVER['QUERY_STRING'] . "&confirm=true'><span class='icon'><i class='bi bi-download'></i></span> Download</a>
    <a class='report-button' href='uploads/" . $randomFilename . "_" . $originalFilename . "'><span class='icon'><i class='bi bi-exclamation-triangle'></i></span> Report</a>
    </div>";
}

?>
