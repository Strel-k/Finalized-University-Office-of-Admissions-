<?php
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: create.php");
    exit();
}

$username = "root";
$password = "";
$database = "studentDB";
$servername = "localhost";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$recordsPerPage=10;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

$offset=($page-1)*$recordsPerPage;

$offset = ($page - 1) * $recordsPerPage;

$userId = $_SESSION['userId'];
$sql = "SELECT email FROM login WHERE id = $userId";
$result = $conn->query($sql);
$sql = "SELECT s.id, s.fullName, s.email, s.age, s.gpa, l.isAdmin 
        FROM student s
        INNER JOIN login l ON s.id = l.id";

if ($_SESSION['isAdmin'] != 1) {
    $sql .= " WHERE s.id = $userId";
}


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_email = $row["email"];
} else {
    $user_email = "Email not found";
}
$countSql = "SELECT COUNT(*) AS totalRecords FROM student";
$countResult = $conn->query($countSql);
$totalRecords = $countResult->fetch_assoc()['totalRecords'];

$sql .=" LIMIT $offset, $recordsPerPage";

$result = $conn->query($sql);

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/view_style.css">

    <title>Read | <?php echo $_SESSION['isAdmin'] == 1 ? "ADMIN" : "STUDENT"; ?></title>
</head>
<body>
    <div class="container">
        <div class="background user-background" style="padding-bottom:70px;">
            <img src="img/CLSU.png" width="110" height="100">
            <br><br>
            <p style='text-align:center;'> <?php echo $user_email; ?></p>
            <h2 style="margin-right:65px;"><?php echo $_SESSION['isAdmin'] == 1 ? "ADMIN" : "STUDENT"; ?></h2>
            <?php if ($_SESSION['isAdmin'] == 1) { ?>
            <a href="register.php" style="text-decoration:none;">
                <div class='background' style='margin-left:5px; background-color:green; color:white; text-align:center;'>
                    <b>Create</b>
                </div>
            </a>
            <a href="search.php" style="text-decoration:none;">
                <div class='background' style='margin-left:5px; background-color:rgb(107, 154, 13); color:white; text-align:center; margin-top:10px;'>
                    <b>Search</b>
                </div>
            </a>
            <?php } else { ?>
                <?php if($_SESSION['isAdmin']==1) {

               
           echo" <a href='search.php' style='text-decoration:none;'>
                <div class='background' style='margin-left:5px; background-color:green; color:white; text-align:center; margin-top:10px;'>
                    <b>Search</b>
                </div>
            </a> ";
        } ?>
            <?php } ?>
            <a href="logout.php" style="text-decoration:none;">
                <div class='background' style='margin-left:5px; background-color:red; color:white; margin-top:10px;text-align:center;'>
                    <b>Logout</b>
                </div>
            </a>
            <br><hr><br>
            <div class="image-container">
            <img src="img/Calendar.png" class="logo">

            </div>

            <p style='text-align:center;'>Date: <?php echo date("M, d, Y"); ?></p>
            <p style='text-align:center;'>Time: <?php echo date("H:i:s"); ?></p>

        </div>

        <div class="header">
            <h2 style="color:white;">OFFICE OF ADMISIONS</h2>
        </div>
        <br><br>
    </div>

    <div class="background">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Age</th>
                    <th>GPA</th>
                    <th>Role</th>
                    <?php if($_SESSION['isAdmin']==1) {
                        echo "<th>Options</th>";
                    }?>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["fullName"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["age"] . "</td>";
                        echo "<td>" . $row["gpa"] . "</td>";
                        echo "<td>" . ($row["isAdmin"] == 1 ? "Admin" : "Student") . "</td>";
                        if($_SESSION['isAdmin'] == 1) {
                            echo "<td>";
                            echo "<button onclick='updateRow(" . $row["id"] . ")' style='margin-left:10px; background-color:green; color:white; padding:5px;'>Update</button><br>";
                            echo "<button onclick='deleteRow(" . $row["id"] . ")'style='margin-left:12px;margin-top:5px;background-color:red; color:white; padding:5px;'>Delete</button>";
                            echo "</td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No Record Found!</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <?php if ($_SESSION['isAdmin'] == 1) : ?>
    <div class="pagination" style="text-align:center; margin-left:35vh;">
        <div class="pagination-prev">
            <?php if ($page > 1) : ?>
                <a href="?page=<?php echo ($page - 1); ?>" class="button" style="background-color:green;">Previous</a>
            <?php endif; ?>
        </div>

        <div class="pagination-numbers">
            <?php if ($result->num_rows > 0) : ?>
                <table style="display: inline-block;">
                    <tr>
                        <?php
                        $totalPages = ceil($totalRecords / $recordsPerPage);
                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i == $page) {
                                echo "<td><span class='current' style='font-weight:800; margin-right:10px;'>$i</span></td>";
                            } else {
                                echo "<td><a href='?page=$i' style='text-decoration:none; color:black;'>$i</a></td>";
                            }
                        }
                        ?>
                    </tr>
                </table>
            <?php endif; ?>
        </div>

        <div class="pagination-next">
            <?php if ($page < $totalPages) : ?>
                <a href="?page=<?php echo ($page + 1); ?>" class="button">Next</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

    <br>
               
    <script>
        function updateRow(id) {
            var confirmUpdate = confirm("Are you sure you want to update this record?");
            if (confirmUpdate) {
                window.location.href = 'update.php?id=' + id;
            }
        }

        function deleteRow(id) {
            var confirmDelete = confirm("Are you sure you want to delete this record?");
            if (confirmDelete) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>
