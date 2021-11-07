<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or ($_SESSION['identity'] != 1 && $_SESSION['identity'] != 0)) {
    header("Location:login.php");
    exit();
}

$username = $_SESSION['username'];
$identity = $_SESSION['identity'];
$old_pwd = $_POST['old_pwd'];
$new_pwd = $_POST['new_pwd'];
$pwd_confirm = $_POST['pwd_confirm'];

if ($old_pwd == "" or $new_pwd == "" or $pwd_confirm == "") {
    if ($identity == 0) {
        echo "
            <script>
                alert('密码不能为空！！');
                window.location.href = 'student_student.php';
            </script>
        ";
        exit();
    } elseif ($identity == 1) {
        echo "
            <script>
                alert('密码不能为空！！');
                window.location.href = 'teacher_teacher.php';
            </script>
        ";
        exit();
    }
} else {
    $old_pwd = MD5($old_pwd);
    $new_pwd = MD5($new_pwd);
    $pwd_confirm = MD5($pwd_confirm);
}

if ($new_pwd != $pwd_confirm) {
    if ($identity == 0) {
        echo "
            <script>
                alert('新密码两次输入不一致！！');
                window.location.href = 'student_student.php';
            </script>
        ";
        exit();
    } elseif ($identity == 1) {
        echo "
            <script>
                alert('新密码两次输入不一致！！');
                window.location.href = 'teacher_teacher.php';
            </script>
        ";
        exit();
    }
} else {
    $db = @mysqli_connect("localhost", "root", "123456", "eas");
    if (!$db) {
        die("Fail to connect the database！！" . mysqli_connect_error());
    }
    mysqli_query($db, "begin");
    mysqli_query($db, "set names utf8");

    $user = mysqli_fetch_array(mysqli_query($db, "SELECT * from user WHERE username='$username' for update"));
    if ($old_pwd == $user['password']) {
        $result = mysqli_query($db, "UPDATE user SET password='$new_pwd' WHERE username='$username'");
        if ($result) {
            mysqli_query($db, "commit");
            mysqli_close($db);
            echo "
                <script>
                    alert('密码修改成功，请重新登陆！！');
                    sessionStorage.clear();
                    window.location.href = 'login.php';
                </script>
            ";
            exit();
        } else {
            echo '密码修改失败！！', mysqli_error($db), '<br />';
            echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
            mysqli_query($db, "rollback");
            mysqli_close($db);
            exit();
        }
    } else {
        if ($identity == 0) {
            mysqli_query($db, "commit");
            mysqli_close($db);
            echo "
                <script>
                    alert('密码错误，无法修改！！');
                    window.location.href = 'student_student.php';
                </script>
            ";
            exit();
        } elseif ($identity == 1) {
            mysqli_query($db, "commit");
            mysqli_close($db);
            echo "
                <script>
                    alert('密码错误，无法修改！！');
                    window.location.href = 'teacher_teacher.php';
                </script>
            ";
            exit();
        }
    }
}
?>