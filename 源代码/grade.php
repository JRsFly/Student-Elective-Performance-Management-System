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

$username = check($_POST['username']);
$cno = check($_POST['cno']);
$grade = check($_POST['grade']);
$operation = $_POST['operation_choice'];

if ($operation == 1) {  // modify
    if ($username == "" || $cno == "" || $grade == "") {
        echo "
            <script>
                alert('成绩信息不能为空！！');
                window.location.href = 'admin_grade.php';
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

        $check_stu_cor = mysqli_query($db, "SELECT * FROM stu_cor WHERE username='$username' AND cno='$cno' for update");
        if (mysqli_fetch_array($check_stu_cor)) {
            if (!($grade >= 0 && $grade <= 100)) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('分数需在0～100！！');
                        window.location.href = 'admin_grade.php';
                    </script>
                ";
                exit();
            }

            $result = mysqli_query($db, "UPDATE stu_cor SET grade='$grade' WHERE username='$username' AND cno='$cno'");
            if ($result) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('成绩修改成功！！');
                        window.location.href = 'admin_grade.php';
                    </script>
                ";
                exit();
            } else {
                echo '修改失败！！', mysqli_error($db), '<br />';
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
                    alert('该学生并未选修相应课程！！');
                    window.location.href = 'admin_grade.php';
                </script>
            ";
            exit();
        }
    }
} elseif ($operation == -1) {   // wipe
    if ($cno == "" || $username == "") {
        echo "
            <script>
                alert('信息不能为空！！');
                window.location.href = 'admin_grade.php';
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

        $check_stu_cor = mysqli_query($db, "SELECT * FROM stu_cor WHERE username='$username' AND cno='$cno' for update");
        if (mysqli_fetch_array($check_stu_cor)) {
            $result = mysqli_query($db, "UPDATE stu_cor SET grade=NULL WHERE username='$username' AND cno='$cno'");
            if ($result) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('成绩信息成功清除！！');
                        window.location.href = 'admin_grade.php';
                    </script>
                ";
                exit();
            } else {
                echo '清除失败！！', mysqli_error($db), '<br />';
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
                    alert('该条成绩信息不存在！！');
                    window.location.href = 'admin_grade.php';
                </script>
            ";
            exit();
        }
    }
}
?>