<?php
$conn = new mysqli("localhost", "root", "", "gestion_notes_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
