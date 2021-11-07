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

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != -1) {
    header("Location:login.php");
    exit();
}

$cno = check($_POST['cno']);
$cname = check($_POST['cname']);
$username = check($_POST['username']);
$operation = $_POST['operation_choice'];

if ($operation == 1) {  // insert
    if ($cno == "" || $cname == "" || $username == "") {
        echo "
            <script>
                alert('课程信息不能为空！！');
                window.location.href = 'admin_course.php';
            </script>
	    ";
        exit();
    } else {
        $db = @mysqli_connect("localhost", "root", "123456", "eas");
        if (!$db) {
            die("Fail to connect the database！！" . mysqli_connect_error());
        }
        mysqli_query($db, "begin");
        mysqli_query($db, "set names utf8");

        $check_cno = mysqli_query($db, "SELECT * FROM course WHERE cno='$cno' for update");
        if (mysqli_fetch_array($check_cno)) {
            mysqli_query($db, "commit");
			mysqli_close($db);
            echo "
                <script>
                    alert('课程号已存在！！');
                    window.location.href = 'admin_course.php';
                </script>
            ";
            exit();
        } else {
            $check_username = mysqli_query($db, "SELECT * from teacher WHERE username='$username'");
            if (!$check_username) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('该教师不存在！！');
                        window.location.href = 'admin_course.php';
                    </script>
                ";
                exit();
            } else {
                $name = mysqli_fetch_array($check_username)['name'];
            }
            $result = mysqli_query($db, "INSERT INTO course(cno, cname, username, name) VALUES ('$cno', '$cname', '$username', '$name')");
            if ($result) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('添加课程成功！！');
                        window.location.href = 'admin_course.php';
                    </script>
                ";
                exit();
            } else {
                echo '添加失败！！', mysqli_error($db), '<br />';
                echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
                mysqli_query($db, "rollback");
                mysqli_close($db);
                exit();
            }
        }
    }
} elseif ($operation == -1) {   // delete
    if ($cno == "") {
        echo "
            <script>
                alert('课程号不能为空！！');
                window.location.href = 'admin_course.php';
            </script>
	    ";
        exit();
    } else {
        $db = @mysqli_connect("localhost", "root", "123456", "eas");
        if (!$db) {
            die("Fail to connect the database！！" . mysqli_connect_error());
        }
        mysqli_query($db, "begin");
        mysqli_query($db, "set names utf8");

        $check_cno = mysqli_query($db, "SELECT * FROM course WHERE cno='$cno' for update");
        if (mysqli_fetch_array($check_cno)) {
            $result = mysqli_query($db, "DELETE FROM course WHERE cno='$cno'");
            if ($result) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('删除课程成功！！');
                        window.location.href = 'admin_course.php';
                    </script>
                ";
                exit();
            } else {
                echo '删除失败！！', mysqli_error($db), '<br />';
                echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
                mysqli_query($db, "rollback");
                mysqli_close($db);
                exit();
            }
        } else {
            mysqli_query($db, "commit");
			mysqli_close($db);
            echo "
                <script>
                    alert('课程号不存在！！');
                    window.location.href = 'admin_course.php';
                </script>
            ";
            exit();
        }
    }
}
?>