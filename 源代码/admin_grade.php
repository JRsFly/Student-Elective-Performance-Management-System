<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != -1) {
    header("Location:login.php");
    exit();
}

$db = @mysqli_connect("localhost", "root", "123456", "eas");
if (!$db) {
    die("Fail to connect the database！！" . mysqli_connect_error());
}
mysqli_query($db, "begin");
mysqli_query($db, "set names utf8");

if (!isset($_POST['search']) or $_POST['search'] == "") {
    $sql = "SELECT stu_cor.cno, course.cname, stu_cor.username, student.name, stu_cor.grade 
            FROM stu_cor, student, course 
            WHERE stu_cor.cno=course.cno 
            AND stu_cor.username=student.username;";
} else {
    $search = $_POST['search'];
    $vague = "(stu_cor.cno LIKE '%$search%' 
            or stu_cor.username LIKE '%$search%' 
            or student.name LIKE '%$search%' 
            or course.cname LIKE '%$search%'
            or stu_cor.grade='$search')";
    $sql = "SELECT stu_cor.cno, course.cname, stu_cor.username, student.name, stu_cor.grade 
            FROM stu_cor, student, course 
            WHERE $vague
            AND stu_cor.cno=course.cno 
            AND stu_cor.username=student.username;";
}

$grade_info = mysqli_query($db, $sql);
mysqli_query($db, "commit");
mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/admin/admin_grade.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
    <script type="text/javascript" src="./style/admin/admin.js"></script>
    <title>武汉大学教务系统</title>
</head>
<body>
    <div class="container" >
        <table class="identity">
            <tr>
                <td rowspan="2">
                    <img class="icon" src="./style/admin/admin.png">
                </td>
                <td class="identity_text">管理员</td>
            </tr>
        </table>

        <div onclick="choose('logout')">
            <i id="logout" class="fas fa-sign-out-alt"></i>
        </div>

        <div>
            <button class="backup" onclick="javascript:window.location.href='backup.php'">备份</button>
        </div>
        <div>
            <button class="restore" onclick="javascript:window.location.href='restore.php'">恢复</button>
        </div>

        <div class="admin">
            <div class="option" onclick="choose('user')">
                <i id="user" class="fas fa-users"></i>
                <div class="option_text">用户</div>
            </div>

            <div class="option" onclick="choose('course')">
                <i id="course" class="fas fa-book-open"></i>
                <div class="option_text">课程</div>
            </div>

            <div class="option" onclick="choose('grade')">
                <i id="grade" class="fas fa-award"></i>
                <div class="option_text">成绩</div>
            </div>
        </div>

        <div class="grade">
            <h1>Grade</h1>
            <form action="grade.php" method="POST">
                <div class="form">
                    <div class="item">
                        <i class="fas fa-user-graduate"></i>
                        <input type="text" name="username" placeholder="student_id">
                    </div>
                    <div class="item">
                        <i class="fab fa-cuttlefish"></i>
                        <input type="text" name="cno" placeholder="course_id">
                    </div>
                    <div class="item">
                        <i class="fas fa-trophy"></i>
                        <input type="text" name="grade" placeholder="grade">
                    </div>

                    <div class="operation">
                        <i class="fas fa-exchange-alt"></i>
                        <li><input id="modify" type="radio" name="operation_choice" value="1" checked></li>
                        <li><input id="wipe" type="radio" name="operation_choice" value="-1"></li>
                    </div>
                    <button>Submit</button>
                </div>
            </form>
        </div>     
        
        <form action="" method="POST">
            <div class="search_box">
                <input class="search_content" type="text" name="search" placeholder="search">
                <button class="search_btn"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <div class="grade_info">
            <div class="info_box">
                <table>
                    <thread>
                        <tr>
                            <th>课程号</th>
                            <th>课程名称</th>
                            <th>学 号</th>
                            <th>姓 名</th>
                            <th>成 绩</th>
                        </tr>
                    </thread>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($grade_info)) {
                            echo"<tr><td>";
                            echo $row['cno'], "</td><td>";
                            echo $row['cname'], "</td><td>";
                            echo $row['username'], "</td><td>";
                            echo $row['name'], "</td><td>";
                            echo $row['grade'],"</td></tr>";	
                        }	
                        ?>
                    </tbody>
                </table>
            </div>
        </div>       
    </div>
</body>
</html>