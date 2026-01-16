<?php 
session_start(); 

$loggedIn = isset($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth"> <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Campus Utility Portal</title>
    <!-- Favicon -->
  
   

</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

<?php 
  include_once "includes/header.php"; 
?>

<main>
    <?php include_once "includes/hero.php"; ?>
    <?php include_once "includes/feature.php"; ?>
     <?php include_once "includes/about.php"; ?>
</main>

<?php include_once "includes/footer.php"; ?>

</body>
</html>