<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or ($_SESSION['identity'] != 1 && $_SESSION['identity'] != 0)) {
    header("Location:login.php");
    exit();
}

$filename = "course.xls";
header("Content-Type: application/vnd.ms-execl");
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

$db = @mysqli_connect("localhost", "root", "123456", "eas");
if (!$db) {
    die("Fail to connect the database！！" . mysqli_connect_error());
}
mysqli_query($db, "begin");
mysqli_query($db, "set names utf8");
$info = mysqli_query($db, $_SESSION['sql']);

if ($_SESSION['identity'] == 1) {   // teacher
    echo "课程编号"."\t";
    echo "课程名称"."\t";
    echo "学 号"."\t";
    echo "姓 名"."\t";
    echo "成 绩"."\n";

    while ($row = mysqli_fetch_array($info)) {
        echo $row['cno']."\t";
        echo $row['cname']."\t";
        echo $row['username']."\t";
        echo $row['name']."\t";
        echo $row['grade']."\n";
    }
} elseif ($_SESSION['identity'] == 0) { // student
    echo "课程编号"."\t";
    echo "课程名称"."\t";
    echo "教 师"."\t";
    echo "成 绩"."\n";

    while ($row = mysqli_fetch_array($info)) {
        echo $row['cno']."\t";
        echo $row['cname']."\t";
        echo $row['name']."\t";
        echo $row['grade']."\n";
    }
}

mysqli_query($db, "commit");
mysqli_close($db);
?>