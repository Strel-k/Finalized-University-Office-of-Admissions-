<?php
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: create.php");
    exit();
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studentDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$row = [];
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM student s INNER JOIN login l ON s.id = l.id WHERE s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['passWord']; 
    $age = $_POST['age'];
    $gpa = $_POST['gpa'];
    
    $isAdmin = isset($_POST['makeAdmin']) ? 1 : 0;

    $sqlStudent = "UPDATE student SET 
    fullName=?,
    email=?,
    age=?,
    gpa=?
    WHERE id =?";
    $stmtStudent = $conn->prepare($sqlStudent);
    $stmtStudent->bind_param("ssddi", $fullName, $email, $age, $gpa, $id);
    $stmtStudent->execute();

    $sqlLogin = "UPDATE login SET 
            email=?,
            password=?,
            isAdmin=?
            WHERE id =?";
    $stmtLogin = $conn->prepare($sqlLogin);
    $stmtLogin->bind_param("ssii", $email, $password, $isAdmin, $id);
    $stmtLogin->execute();

    header("Location:view.php"); 
    exit(); 
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/register.css">
    <title>Update | Login Form Activity</title>
</head>
<style>
.button-container input[type="submit"] {
    width:54%;
}
</style>
<body>
<div class="background">
    <form method="POST">
        <div class="form-container">
            <br>
            <img src="img/CLSU.png"class="logo">
            <h1>Update Form</h1>
            <div class="input-container">
                <input type="hidden" name="id" value="<?php echo isset($row['id']) ? $row['id'] : ''; ?>">

            </div>
            <div class="input-container">
                 <input type="text" name="fullName" placeholder="Full name" value="<?php echo isset($row['fullName']) ? $row['fullName'] : ''; ?>" required >
            </div>
            <div class="input-container">
                <input type="email" name="email" placeholder="example@gmail.com" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>" required >
            </div>
            <div class="input-container">
                <input type="password" name="passWord" placeholder="Enter Password" value="<?php echo isset($row['password']) ? $row['password'] : ''; ?>" required >

            </div>
            <div class="input-container">
                <input type="number" name="age" placeholder="Enter Age" value="<?php echo isset($row['age']) ? $row['age'] : ''; ?>" required >  
            </div>
            <div class="input-container">
                     <input type="number" name="gpa" step="0.01" min="0" max="5.0" placeholder="Enter GPA" value="<?php echo isset($row['gpa']) ? $row['gpa'] : ''; ?>" required >

            </div>
            <h3 style="text-align:center;">Make Admin</h3>
            <input type="checkbox" id="makeAdmin" name="makeAdmin" onclick="isAdminchecked()" <?php if(isset($row['isAdmin']) && $row['isAdmin'] == 1) echo 'checked'; ?> >
<label for="makeAdmin"></label><br>
            <div class="button-container">
            <input type="submit" name="submit" style="margin-right:10vh;" onclick="confirmSubmission()">

            </div>
        </div>
    </form>
</div>
<script>
    function isAdminchecked() {
        alert("NOTICE: Please logout of your account to ensure change.");
    }
    function confirmSubmission() {
            var confirmSubmit = confirm("Are you sure you want to finalize this submission?");
            
            if (confirmSubmit) {
                document.getElementById("updateForm").submit(); 
            } else {
                event.preventDefault();
            }
        }
    </script>
</body>
</html>
