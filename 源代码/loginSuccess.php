<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity'])) {
    header("Location:login.php");
    exit();
} else {
    $username = $_SESSION['username'];
    $identity = $_SESSION['identity'];

    if ($identity == -1) {  // admin
        header("Location:admin_user.php");
        exit();
    } elseif ($identity == 0) { // student
        header("Location:student_student.php");
        exit();
    } elseif ($identity == 1) { // teacher
        header("Location:teacher_teacher.php");
        exit();
    } else {
        echo "
        <script>
            alert('无法识别的身份信息！！');
            window.location.href = 'login.php' ;
        </script>
        ";
        exit();
    }
}
?>