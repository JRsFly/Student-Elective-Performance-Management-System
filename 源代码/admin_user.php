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
    $sql1 = "SELECT username, name FROM user WHERE identity = 0";
    $sql2 = "SELECT username, name FROM user WHERE identity = 1";
} else {
    $search = $_POST['search'];
    $vague = "(username LIKE '%$search%' 
            or name LIKE '%$search%')";
    $sql1 = "SELECT username, name 
            FROM user 
            WHERE identity = 0
            AND $vague";
    $sql2 = "SELECT username, name 
            FROM user 
            WHERE identity = 1
            AND $vague";
}

$student_info = mysqli_query($db, $sql1);
$teacher_info = mysqli_query($db, $sql2);
mysqli_query($db, "commit");
mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/admin/admin_user.css">
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

        <div>
            <button class="backup" onclick="javascript:window.location.href='backup.php'">备份</button>
        </div>
        <div>
            <button class="restore" onclick="javascript:window.location.href='restore.php'">恢复</button>
        </div>

        <div class="user">
            <h1>User</h1>
            <form action="register.php" method="POST">
                <div class="form">
                    <div class="item">
                        <i class="far fa-user-circle"></i>
                        <input type="text" name="username" placeholder="username">
                    </div>
                    <div class="item">
                        <i class="fas fa-unlock-alt"></i>
                        <input type="text" name="password" placeholder="password">
                    </div>
                    
                    <div class="user_identity">
                        <i class="far fa-address-card"></i>
                        <select name="identity" id="identity_box">
                            <option value="0">student</option>
                            <option value="1">teacher</option>
                        </select>
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
        
        <div class="user_info">
            <div class="info_box">
                <h2 class="teacher_title">教 师</h2>
                <table id="teacher_box">
                    <thread>
                        <tr>
                            <th>用户名</th>
                            <th>姓 名</th>
                        </tr>
                    </thread>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($teacher_info)) {
                            echo"<tr><td>";
                            echo $row['username'], "</td><td>";
                            echo $row['name'],"</td></tr>";	
                        }	
                        ?>
                    </tbody>
                </table>
                <h2 class="student_title">学 生</h2>
                <table id="student_box">
                    <thread>
                        <tr>
                            <th>用户名</th>
                            <th>姓 名</th>
                        </tr>
                    </thread>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($student_info)) {
                            echo"<tr><td>";
                            echo $row['username'], "</td><td>";
                            echo $row['name'],"</td></tr>";	
                        }	
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>