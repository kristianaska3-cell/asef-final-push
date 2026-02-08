<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "timetable_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Connection failed");

$first = $_POST['first_name'] ?? '';
$last  = $_POST['last_name'] ?? '';
$timetable = $_POST['timetable'] ?? '';

if ($first === '' || $last === '' || $timetable === '') die("Missing data");

$stmt = $conn->prepare("INSERT INTO timetables (first_name, last_name, timetable) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $first, $last, $timetable);
$stmt->execute();

$stmt->close();
$conn->close();
?>



<div id="confirmationPopup" class="popup">
  <h2>✅ Timetable Confirmed</h2>
  <p>Your timetable has been confirmed.</p>
  <button onclick="window.location.href='index.php'">OK</button>

</div>


<script>
function showConfirmation() {
  document.getElementById('confirmationPopup').style.display = 'block';
}
function closeConfirmation() {
  document.getElementById('confirmationPopup').style.display = 'none';
}
window.onload = showConfirmation;
</script>
