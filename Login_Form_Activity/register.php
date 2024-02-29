<?php 
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "studentdb";

$conn = new mysqli($servername, $username, $password, $dbname);
if($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$sql_reset_auto_increment = "ALTER TABLE login AUTO_INCREMENT = 1";
$sql_reset_auto_increment = "ALTER TABLE student AUTO_INCREMENT = 1";

if(isset($_POST['submit'])) {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $passWord = $_POST['passWord'];
    $gpa = $_POST['gpa'];

    $check_sql = "SELECT * FROM student WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if($result->num_rows > 0) {
        echo "<script>alert('User already Exists!');</script>";
    } else {
        $sql_student = "INSERT INTO student (fullName, email, age, gpa) VALUES (?, ?, ?, ?)";
        $stmt_student = $conn->prepare($sql_student);
        $stmt_student->bind_param("ssid", $fullName, $email, $age, $gpa);
    
        if ($stmt_student->execute()) {
            
            $last_insert_id = $stmt_student->insert_id;

            $sql_login = "INSERT INTO login (id, email, passWord, isAdmin) VALUES (?, ?, ?, ?)";
            $stmt_login = $conn->prepare($sql_login);
            $role = 0; 
            $stmt_login->bind_param("issi", $last_insert_id, $email, $passWord, $role); 

            if ($stmt_login->execute()) {
                if ($_SESSION['isAdmin'] == 1) {
                    header("Location: view.php");
                } else {
                    header("Location: create.php");
                }
                exit();
            } else {
                echo "Error inserting login data: " . $stmt_login->error;
            }
        }
    }
}

$conn->close();
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/register.css">

    <title>Create | Login Form Activity</title>
</head>
<body>

<div class="background">
        <form method="POST">
            <div class="form-container">
                <img src="img/CLSU.png" class="logo">
                <h1>Registration Form</h1>
                <div class="input-container">
                    <input type="text" name="fullName" placeholder="Full name" required>
                </div>
                <div class="input-container">
                    <input type="email" name="email" placeholder="example@gmail.com" required> 
                </div>
                <div class="input-container">
                    <input type="password" name="passWord" placeholder="Enter Password" required>
                </div>
                <div class="input-container">
                    <input type="number" name="age" placeholder="Enter Age" required>
                </div>
                <div class="input-container">
                    <input type="number" name="gpa" step="0.01" min="0" max="4.0" placeholder="Enter GPA" required>
                </div>
                <div class="button-container">
                    <input type="submit" name="submit" value="Submit" style="margin-right:10vh;">
                </div>
            </div>
        </form>
    </div>
   <script>
        

    </script>
</body>
</html>
