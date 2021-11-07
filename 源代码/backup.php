<?php
session_start();

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != -1) {
    header("Location:login.php");
    exit();
}

$path1 = dirname(__FILE__)."/backup/eas_backup1.sql";
$path2 = dirname(__FILE__)."/backup/eas_backup2.sql";

$delete1 = "rm ".$path1;
$delete2 = "rm ".$path2;

if (file_exists($path1)) {
    $backup = "mysqldump -u root -p123456 eas > ".$path2;
    exec($backup, $r, $result);
    if ($result == 0) {
        exec($delete1, $r, $result);
        echo "
            <script>
                alert('备份成功！！');
                window.location.href = 'admin_user.php';
            </script>
        ";
        exit();
    } else {
        exec($delete2, $r, $result);
        echo "
            <script>
                alert('备份失败！！');
                window.location.href = 'admin_user.php';
            </script>
        ";
        exit();
    }
} else {
    $backup = "mysqldump -u root -p123456 eas > ".$path1;
    exec($backup, $r, $result);
    if ($result == 0) {
        exec($delete2, $r, $result);
        echo "
            <script>
                alert('备份成功！！');
                window.location.href = 'admin_user.php';
            </script>
        ";
        exit();
    } else {
        exec($delete1, $r, $result);
        echo "
            <script>
                alert('备份失败！！');
                window.location.href = 'admin_user.php';
            </script>
        ";
        exit();
    }
}
?>