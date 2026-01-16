<?php
session_start();
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: event.php");
    exit;
}

// Extracting data from POST
$title = trim($_POST['title']);
$description = trim($_POST['description']);
$event_date = $_POST['event_date'];
$event_time = $_POST['event_time'];
$location = trim($_POST['location']);
$event_type = $_POST['event_type'] ?? 'Other'; // Default to Other if not selected
$created_by = $_SESSION['user_id'];

$errors = [];

// Validation
if (empty($title)) $errors[] = "Title is required";
if (empty($description)) $errors[] = "Description is required";
if (empty($event_date)) $errors[] = "Event date is required";
if (empty($event_time)) $errors[] = "Event time is required";
if (empty($location)) $errors[] = "Location is required";

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: add_event.php");
    exit;
}

try {
    $db = (new Database())->getConnection();

    // Updated Query to match your full schema
    $query = "INSERT INTO events (title, description, event_date, event_time, location, event_type, created_by)
              VALUES (:title, :description, :event_date, :event_time, :location, :event_type, :created_by)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":event_date", $event_date);
    $stmt->bindParam(":event_time", $event_time);
    $stmt->bindParam(":location", $location);
    $stmt->bindParam(":event_type", $event_type);
    $stmt->bindParam(":created_by", $created_by);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Event created successfully!";
        header("Location: event.php");
        exit;
    }

} catch (PDOException $e) {
    // Log error and redirect with message
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "Could not create event. Please try again.";
    header("Location: add_event.php");
    exit;
}