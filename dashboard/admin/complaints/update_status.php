<?php
include("../../../config/db.php");

$database = new Database();
$conn = $database->getConnection();

$id = $_POST['id'];
$status = $_POST['status'];

// Prepare and execute the update
$sql = "UPDATE complaints SET status = :status WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();


header("Location: complaints.php");
exit;
?>
