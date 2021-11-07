<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != 1) {
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
$name = mysqli_fetch_array(mysqli_query($db, "SELECT name FROM teacher WHERE username='$username'"))['name'];
if ($name == "") {
    $name = "教师";
}

if (!isset($_POST['search']) or $_POST['search'] == "") {
    $sql = "SELECT stu_cor.username, stu_cor.cno, stu_cor.grade, student.name, course.cname 
            FROM stu_cor, student, course 
            WHERE course.username='$username'
            AND student.username=stu_cor.username AND course.cno=stu_cor.cno";
} else {
    $search = $_POST['search'];
    $vague = "(stu_cor.username LIKE '%$search%' 
            or stu_cor.cno LIKE '%$search%' 
            or student.name LIKE '%$search%' 
            or course.cname LIKE '%$search%'
            or stu_cor.grade='$search')";
    $sql = "SELECT stu_cor.username, stu_cor.cno, stu_cor.grade, student.name, course.cname 
            FROM stu_cor, student, course 
            WHERE $vague
            AND course.username='$username'
            AND student.username=stu_cor.username AND course.cno=stu_cor.cno";
}

$_SESSION['sql'] = $sql;
$course_info = mysqli_query($db, $sql);

mysqli_query($db, "commit");
mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/teacher/teacher_course.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
    <script type="text/javascript" src="./style/teacher/teacher.js"></script>    
    <title>武汉大学教务系统</title>
</head>
<body>
    <div class="container" >
        <table class="identity">
            <tr>
                <td rowspan="2">
                    <img class="icon" src="./style/teacher/teacher.png">
                </td>
                <td class="identity_text"><?php echo $name; ?></td>
            </tr>
        </table>

        <div onclick="choose('logout')">
            <i id="logout" class="fas fa-sign-out-alt"></i>
        </div>

        <div class="teacher">
            <div class="option" onclick="choose('teacher')">
                <i id="teacher" class="fas fa-user"></i>
                <div class="option_text">个人信息</div>
            </div>

            <div class="option" onclick="choose('course')">
                <i id="course" class="fas fa-book-open"></i>
                <div class="option_text">课程</div>
            </div>
        </div>

        <form action="" method="POST">
            <div class="search_box">
                <input class="search_content" type="text" name="search" placeholder="search">
                <button class="search_btn"><i class="fas fa-search"></i></button>
            </div>
        </form>

        <div class="course_info">
            <div class="info_box">
                <table id="course_info">
                    <thread>
                        <tr>
                            <th>课程编号</th>
                            <th>课程名称</th>
                            <th>学 号</th>
                            <th>姓 名</th>
                            <th>成 绩</th>
                        </tr>
                    </thread>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($course_info)) {
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

        <button class="output"  onclick="javascript:window.location.href='output.php'">
            <p>EXPORT</p>
            <div class="loading ">
                <div></div>
                <div></div>
                <div></div>
            </div>

            <svg class='checkmark' width='15px' height='15px' stroke='white' stroke-width="2px" fill='none'>
                <polyline points='1,5 6,9 14,1'></polyline>
            </svg>
        </button>
    </div>
</body>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $($('.output').click(
        function() {
            $(this).toggleClass('active');
            setTimeout(
                () => {
                    $(this).toggleClass('verity');
                }, 2000
            )
        }
    ))
</script>
</html>