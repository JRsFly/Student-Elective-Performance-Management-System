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

if (!isset($_SESSION['username']) or !isset($_SESSION['identity']) or $_SESSION['identity'] != 0) {
    header("Location:login.php");
    exit();
}

$username = $_SESSION['username'];
$cno = check($_POST['cno']);
$operation = $_POST['operation_choice'];

if ($operation == 1) {  // select
    if ($cno == "") {
        echo "
            <script>
                alert('未选择课程！！');
                window.location.href = 'student_course.php';
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

        $check_stu_cor = mysqli_query($db, "SELECT * FROM stu_cor WHERE cno='$cno' AND username='$username' for update");
        if (mysqli_fetch_array($check_stu_cor)) {
            mysqli_query($db, "commit");
			mysqli_close($db);
            echo "
                <script>
                    alert('该课程已在课表中！！');
                    window.location.href = 'student_course.php';
                </script>
            ";
            exit();
        }

        $check_cno = mysqli_query($db, "SELECT * FROM course WHERE cno='$cno' for update");
        $course_info = mysqli_fetch_array($check_cno);
        if ($course_info) {
            $number = $course_info['number'];
            if (120 - $number <= 0) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('该课程已无余量！！');
                        window.location.href = 'student_course.php';
                    </script>
                ";
                exit();
            } else {
                $result1 = mysqli_query($db, "UPDATE course SET number=number+1 WHERE cno='$cno'");
                $result2 = mysqli_query($db, "INSERT INTO stu_cor(username, cno) VALUES ('$username', '$cno')");
                if ($result1 && $result2) {
                    mysqli_query($db, "commit");
                    mysqli_close($db);
                    echo "
                        <script>
                            alert('选课成功！！');
                            window.location.href = 'student_course.php';
                        </script>
                    ";
                    exit();
                } else {
                    echo '选课失败！！', mysqli_error($db), '<br />';
                    echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
                    mysqli_query($db, "rollback");
                    mysqli_close($db);
                    exit();
                }
            }    
        } else {
            mysqli_query($db, "commit");
			mysqli_close($db);
            echo "
                <script>
                    alert('该课程不存在！！');
                    window.location.href = 'student_course.php';
                </script>
            ";
            exit();
        }
    }
} elseif ($operation == -1) {   // withdraw
    if ($cno == "") {
        echo "
            <script>
                alert('未选择课程！！');
                window.location.href = 'student_course.php';
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

        $check_stu_cor = mysqli_query($db, "SELECT * FROM stu_cor WHERE cno='$cno' AND username='$username' for update");
        $stu_cor = mysqli_fetch_array($check_stu_cor);
        if ($stu_cor) {
            if ($stu_cor['grade'] != "") {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('该课程已经无法撤销！！');
                        window.location.href = 'student_course.php';
                    </script>
                ";
                exit();
            }
            $result1 = mysqli_query($db, "UPDATE course SET number=number-1 WHERE cno='$cno'");
            $result2 = mysqli_query($db, "DELETE FROM stu_cor WHERE cno='$cno' AND username='$username'");
            if ($result1 && $result2) {
                mysqli_query($db, "commit");
                mysqli_close($db);
                echo "
                    <script>
                        alert('撤课成功！！');
                        window.location.href = 'student_course.php';
                    </script>
                ";
                exit();
            } else {
                echo '撤课失败！！', mysqli_error($db), '<br />';
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
                    alert('相应选课信息不存在！！');
                    window.location.href = 'student_course.php';
                </script>
            ";
            exit();
        }
    }
}
?>