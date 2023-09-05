<?php
// Combine the keys of all three superglobals into one array
foreach(array_merge(array_keys($_GET), array_keys($_POST), array_keys($_REQUEST)) as $value){
        // Check if the key exists in $_GET
        if(isset($_GET[$value])){
            // Apply htmlspecialchars function to sanitize the input and prevent XSS and HTML INJECTION attacks
            $_GET[$value] = htmlspecialchars($_GET[$value], ENT_QUOTES);
        }
        // Check if the key exists in $_POST
        if(isset($_POST[$value])){
            // Apply htmlspecialchars function to sanitize the input and prevent XSS and HTML INJECTION attacks
            $_POST[$value] = htmlspecialchars($_POST[$value], ENT_QUOTES);
        }
        // Check if the key exists in $_REQUEST
        if(isset($_REQUEST[$value])){
            // Apply htmlspecialchars function to sanitize the input and prevent XSS and HTML INJECTION attacks
            $_REQUEST[$value] = htmlspecialchars($_REQUEST[$value], ENT_QUOTES);
        }
}
?>