<?php
include_once 'includes/patcher.php';
include_once 'includes/upload_function.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Drop Stash</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="Drop Stash">
<meta name="keywords" content="Simple File Hosting, Fast File Upload, Free File Storage, Quick File Sharing, Easy File Access, Secure File Hosting, Reliable File Hosting, User-Friendly File Hosting, High-Speed File Download, Unlimited File Hosting">
    <meta name="description" content="Experience the simplicity of our user-friendly interface, designed to make file hosting a breeze. Enjoy our free service that eliminates the need for unnecessary expenses. Experience lightning-fast upload and download speeds that ensure your files are transferred in no time. Join us for a simple, free, and fast file hosting experience.">
    <meta name="author" content="Neil Aisley">

    <!-- Google / Search Engine Tags -->
    <meta itemprop="name" content="Drop Stash">
    <meta itemprop="description" content="Experience the simplicity of our user-friendly interface, designed to make file hosting a breeze. Enjoy our free service that eliminates the need for unnecessary expenses. Experience lightning-fast upload and download speeds that ensure your files are transferred in no time. Join us for a simple, free, and fast file hosting experience.">
    <meta itemprop="image" content="URL_TO_YOUR_IMAGE">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/misans@3.1.1/lib/misans-400-regular.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/firacode@6.2.0/distr/fira_code.css">
    <link rel="stylesheet" type="text/css" href="styles/main.css">
    <!-- Add a favicon for your website -->
    <link rel="icon" href="URL_TO_YOUR_STORAGE_LOGO" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<nav class="top-nav">
        <h2 class="title">Drop Stash</h2>
        <div class="menu-icon" id="menuIcon">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
        <ul class="nav-links" id="navLinks">
            <li><a href="#">Home</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </nav>
    <div class="center">
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
</div>
    <div class="row">
        <div class="feature">
            <i class="fa fa-upload"></i>
            <p>Upload files with a maximum size of 10 MB per file.</p>
        </div>
        <div class="feature">
            <i class="fa fa-send"></i>
            <p>Share your files with others anytime, anywhere around the world. </p>
        </div>
        <div class="feature">
            <i class="fa fa-download"></i>
            <p>Download your files that have been shared with you. </p>
        </div>
    </div>

    <nav class="bottom-nav">

        <a class="faqs" href="#faq-section">FAQs</a>

</nav>
<script src="javascript/main.js"></script>
</body>
</html>

