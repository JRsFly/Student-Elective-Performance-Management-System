<?php
// check the input
function check($value)
{
    if (get_magic_quotes_gpc()) {
        $value = htmlspecialchars(trim($value));
    } else {
        $value = addslashes(htmlspecialchars(trim($value)));
    }
    return $value;
}

session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or ($_SESSION['identity'] != 0 && $_SESSION['identity'] != 1)) {
    header("Location:login.php");
    exit();
}

$username = $_SESSION['username'];
$name = check($_POST['name']);
$college = check($_POST['college']);
if (isset($_POST['sex'])) {
    $sex = check($_POST['sex']);
}
if (isset($_POST['age'])) {
    $age = check($_POST['age']);
}
if (isset($_POST['major'])) {
    $major = check($_POST['major']);
}

if ($_SESSION['identity'] == 0) {   // student
    if ($sex != "" && $sex != "男" && $sex != "女") {
        echo "
            <script>
                alert('性别有误！！');
                window.location.href = 'student_student.php';
            </script>
	    ";
        exit();
    }

    if (!($age > 0 && $age <=100)) {
        echo "
            <script>
                alert('年龄需在1~100！！');
                window.location.href = 'student_student.php';
            </script>
	    ";
        exit();
    }

    $db = @mysqli_connect("localhost", "root", "123456", "eas");
    if (!$db) {
        die("Fail to connect the database！！" . mysqli_connect_error());
    }
    mysqli_query($db, "begin");
    mysqli_query($db, "set names utf8");

    $result1 = mysqli_query($db, "UPDATE student SET name='$name', sex='$sex', age='$age', 
               college='$college', major='$major' WHERE username='$username'");
    $result2 = mysqli_query($db, "UPDATE user SET name='$name' WHERE username='$username'");
    if ($result1 && $result2) {
        mysqli_query($db, "commit");
        mysqli_close($db);
        echo "
            <script>
                alert('个人信息保存成功！！');
                window.location.href = 'student_student.php';
            </script>
        ";
        exit();
    } else {
        echo '保存失败！！', mysqli_error($db), '<br />';
        echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
        mysqli_query($db, "rollback");
        mysqli_close($db);
        exit();
    }
} elseif ($_SESSION['identity'] == 1) { // teacher
    $db = @mysqli_connect("localhost", "root", "123456", "eas");
    if (!$db) {
        die("Fail to connect the database！！" . mysqli_connect_error());
    }
    mysqli_query($db, "begin");
    mysqli_query($db, "set names utf8");

    $result1 = mysqli_query($db, "UPDATE teacher SET name='$name', college='$college' WHERE username='$username'");
    $result2 = mysqli_query($db, "UPDATE user SET name='$name' WHERE username='$username'");
    if ($result1 && $result2) {
        mysqli_query($db, "commit");
        mysqli_close($db);
        echo "
            <script>
                alert('个人信息保存成功！！');
                window.location.href = 'teacher_teacher.php';
            </script>
        ";
        exit();
    } else {
        echo '保存失败！！', mysqli_error($db), '<br />';
        echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
        mysqli_query($db, "rollback");
        mysqli_close($db);
        exit();
    }
}
?>