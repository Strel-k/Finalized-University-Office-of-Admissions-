<?php  
session_start();

if (!isset($_SESSION['isAdmin'])) {
    header("Location: create.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "studentDB";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $conn->begin_transaction();

    try {
        $sql_student = "DELETE FROM student WHERE id = ?";
        $stmt_student = $conn->prepare($sql_student);
        $stmt_student->bind_param("i", $id);
        $stmt_student->execute();

        $sql_login = "DELETE FROM login WHERE id = ?";
        $stmt_login = $conn->prepare($sql_login);
        $stmt_login->bind_param("i", $id);
        $stmt_login->execute();

        $conn->commit();

        $sql_count_deleted = "SELECT COUNT(*) AS deleted_count FROM student WHERE id < ?";
        $stmt_count_deleted = $conn->prepare($sql_count_deleted);
        $stmt_count_deleted->bind_param("i", $id);
        $stmt_count_deleted->execute();
        $result_count_deleted = $stmt_count_deleted->get_result();
        $row_count_deleted = $result_count_deleted->fetch_assoc();
        $deleted_count = $row_count_deleted['deleted_count'];
        
        if ($deleted_count >= $reset_threshold) {
            $sql_reset_student = "ALTER TABLE student AUTO_INCREMENT = 1";
            $sql_reset_login = "ALTER TABLE login AUTO_INCREMENT = 1";
            $conn->query($sql_reset_student);
            $conn->query($sql_reset_login);
        }
        
        // Redirect to view.php
        header("Location: view.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error deleting record: " . $e->getMessage();
    }
}

$conn->close();
?>
