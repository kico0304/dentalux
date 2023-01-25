<?php 
    session_start();
    include_once('../connection.php'); 
    date_default_timezone_set("Europe/Belgrade");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin panel</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link href="./css/styles.css" rel="stylesheet">
</head>
<body>

<!-- LOADER -->
<div id="loader_">
   <div class="loader">Loading...</div> 
</div>