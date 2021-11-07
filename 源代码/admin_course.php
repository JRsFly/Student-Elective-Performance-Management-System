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
    $sql = "SELECT course.cno, course.cname, course.username, course.number, teacher.name 
            FROM course, teacher 
            WHERE course.username=teacher.username";
} else {
    $search = $_POST['search'];
    $vague = "(course.cno LIKE '%$search%' 
            or course.cname LIKE '%$search%' 
            or course.username LIKE '%$search%' 
            or teacher.name LIKE '%$search%')";
    $sql = "SELECT course.cno, course.cname, course.username, course.number, teacher.name 
            FROM course, teacher
            WHERE $vague
            AND course.username=teacher.username";
}

$course_info = mysqli_query($db, $sql);
mysqli_query($db, "commit");
mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/admin/admin_course.css">
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

        <div class="course"> 
            <h1>Course</h1>
            <form action="course.php" method="POST">
                <div class="form">
                    <div class="item">
                        <i class="fab fa-cuttlefish"></i>
                        <input type="text" name="cno" placeholder="course_id">
                    </div>
                    <div class="item">
                        <i class="fab fa-cuttlefish"></i>
                        <input type="text" name="cname" placeholder="course_name">
                    </div>
                    <div class="item">
                        <i class="fas fa-user-tie"></i>
                        <input type="text" name="username" placeholder="teacher_id">
                    </div>

                    <div class="operation">
                        <i class="fas fa-exchange-alt"></i>
                        <li><input id="insert" type="radio" name="operation_choice" value="1" checked></li>
                        <li><input id="delete" type="radio" name="operation_choice" value="-1"></li>
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

        <div class="course_info">
            <div class="info_box">
                <table>
                    <thread>
                        <tr>
                            <th>课程编号</th>
                            <th>课程名称</th>
                            <th>任课教师</th>
                            <th>教师编号</th>
                            <th>人 数</th>
                        </tr>
                    </thread>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($course_info)) {
                            echo"<tr><td>";
                            echo $row['cno'], "</td><td>";
                            echo $row['cname'], "</td><td>";
                            echo $row['name'], "</td><td>";
                            echo $row['username'], "</td><td>";
                            echo $row['number'],"</td></tr>";	
                        }	
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>