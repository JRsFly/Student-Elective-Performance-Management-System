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
$name = mysqli_fetch_array(mysqli_query($db, "SELECT name FROM student WHERE username='$username'"))['name'];
if ($name == "") {
    $name = "学生";
}

if (!isset($_POST['search']) or $_POST['search'] == "") {
    $sql = "SELECT stu_cor.cno, stu_cor.grade, course.cname, teacher.name 
            FROM stu_cor, course, teacher 
            WHERE stu_cor.username='$username'
            AND course.cno=stu_cor.cno AND course.username=teacher.username";
} else {
    $search = $_POST['search'];
    $vague = "(stu_cor.cno LIKE '%$search%' 
            or course.cname LIKE '%$search%' 
            or teacher.name LIKE '%$search%' 
            or stu_cor.grade='$search')";
    $sql = "SELECT stu_cor.cno, stu_cor.grade, course.cname, teacher.name 
            FROM stu_cor, course, teacher 
            WHERE $vague 
            AND stu_cor.username='$username'
            AND course.cno=stu_cor.cno AND course.username=teacher.username";
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
    <link rel="stylesheet" href="./style/student/student_grade.css">
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
                            <th>教 师</th>
                            <th>成 绩</th>
                        </tr>
                    </thread>
                    <tbody>
                        <?php
                        while ($row = mysqli_fetch_array($course_info)) {
                            echo"<tr><td>";
                            echo $row['cno'], "</td><td>";
                            echo $row['cname'], "</td><td>";
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