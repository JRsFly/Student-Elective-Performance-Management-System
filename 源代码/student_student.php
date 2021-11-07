<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != 0) {
    header("Location:login.php");
    exit();
}

$db = @mysqli_connect("localhost", "root", "123456", "eas");
if (!$db) {
    die("Fail to connect the database！！" . mysqli_connect_error());
}
mysqli_query($db, "begin");
mysqli_query($db, "set names utf8");

$username = $_SESSION['username'];
$student_info = mysqli_fetch_array(mysqli_query($db, "SELECT username, name, sex, age, college, major FROM student WHERE username='$username'"));
if ($student_info['name'] == "") {
    $name = "学生";
} else {
    $name = $student_info['name'];
}

mysqli_query($db, "commit");
mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/student/student_student.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
    <script type="text/javascript" src="./style/student/student.js"></script>
    <title>武汉大学教务系统</title>
</head>
<body>
    <div class="container" >
        <table class="identity">
            <tr>
                <td rowspan="2">
                    <img class="icon" src="./style/student/student.png">
                </td>
                <td class="identity_text"><?php echo $name; ?></td>
            </tr>
        </table>

        <div onclick="choose('logout')">
            <i id="logout" class="fas fa-sign-out-alt"></i>
        </div>

        <div class="student">
            <div class="option" onclick="choose('student')">
                <i id="student" class="fas fa-user"></i>
                <div class="option_text">个人信息</div>
            </div>

            <div class="option" onclick="choose('course')">
                <i id="course" class="fas fa-book-open"></i>
                <div class="option_text">选课</div>
            </div>

            <div class="option" onclick="choose('grade')">
                <i id="grade" class="fas fa-award"></i>
                <div class="option_text">成绩</div>
            </div>
        </div>

        <form action="private.php" method="POST">
            <h1 class="title">个人信息</h1>
            <div class="info_box">
                <table>
                    <tbody>
                        <tr>
                            <td>学 号</td>
                            <td><?php echo $student_info['username']; ?></td>
                        </tr>
                        <tr>
                            <td>姓 名</td>
                            <td><input type="text" name="name" value="<?php echo $student_info['name']; ?>"></td>
                        </tr>
                        <tr>
                            <td>性 别</td>
                            <td><input type="text" name="sex" value="<?php echo $student_info['sex']; ?>"></td>
                        </tr>
                        <tr>
                            <td>年 龄</td>
                            <td><input type="text" name="age" value="<?php echo $student_info['age']; ?>"></td>
                        </tr>
                        <tr>
                            <td>学 院</td>
                            <td><input type="text" name="college" value="<?php echo $student_info['college']; ?>"></td>
                        </tr>
                        <tr>
                            <td>专 业</td>
                            <td><input type="text" name="major" value="<?php echo $student_info['major']; ?>"></td>
                        </tr>
                    </tbody>
                </table>
                <button>保存</button>
            </div>
        </form>

        <form action="password.php" method="POST">
            <div class="pwd_box">
                <table>
                    <tbody>
                        <tr>
                            <td>旧密码</td>
                            <td><input type="password" name="old_pwd"></td>
                        </tr>
                        <tr>
                            <td>新密码</td>
                            <td><input type="password" name="new_pwd"></td>
                        </tr>
                        <tr>
                            <td>密码确认</td>
                            <td><input type="password" name="pwd_confirm"></td>
                        </tr>
                    </tbody>
                </table>
                <button>修改</button>
            </div>
        </form>
    </div>
</body>
</html>