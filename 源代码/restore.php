<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != -1) {
    header("Location:login.php");
    exit();
}

$path1 = dirname(__FILE__)."/backup/eas_backup1.sql";
$path2 = dirname(__FILE__)."/backup/eas_backup2.sql";

$db = @mysqli_connect("localhost", "root", "123456");
if (!$db) {
    die("Fail to connect the database！！" . mysqli_connect_error());
}
mysqli_query($db, "begin");
mysqli_query($db, "set names utf8");

if (file_exists($path1)) {
    $source = "mysql -u root -p123456 -t eas < ".$path1;
} elseif (file_exists($path2)) {
    $source = "mysql -u root -p123456 -t eas < ".$path2;
} else {
    mysqli_query($db, "commit");
    mysqli_close($db);
    echo "
        <script>
            alert('无备份文件！！');
            window.location.href = 'admin_user.php';
        </script>
    ";
    exit();
}

$result1 = mysqli_query($db, "DROP DATABASE eas");
$result2 = mysqli_query($db, "CREATE DATABASE eas");
exec($source, $r, $result3);
if ($result2 && ($result3 == 0)) {
    mysqli_query($db, "commit");
    mysqli_close($db);
    echo "
        <script>
            alert('已恢复至上次备份！！');
            window.location.href = 'admin_user.php';
        </script>
    ";
    exit();
} else {
    echo "
        <script>
            alert('恢复至上次备份失败！！');
            window.location.href = 'admin_user.php';
        </script>
    ";
    mysqli_query($db, "rollback");
    mysqli_close($db);
    exit();
}
?>