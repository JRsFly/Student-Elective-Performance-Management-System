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

$username  = $_POST['username'];
$password  = $_POST['password'];
$identity  = $_POST['identity'];
$operation = $_POST['operation_choice'];

if ($username == "" || $password == "") {
    echo "
	    <script>
	        alert('用户名和密码不能为空！！');
	        window.location.href = 'admin_user.php';
	    </script>
	";
    exit();
} else {
    $username = check($username);
    $password = MD5($password);

    $db = @mysqli_connect("localhost", "root", "123456", "eas");
    if (!$db) {
        die("Fail to connect the database！！" . mysqli_connect_error());
    }
    mysqli_query($db, "begin");
    mysqli_query($db, "set names utf8");

    $get_admin = mysqli_query($db, "SELECT * FROM user WHERE identity=-1 for update");
    $admin_passsword = mysqli_fetch_array($get_admin)['password'];

    $check_username = mysqli_query($db, "SELECT * FROM user WHERE username='$username' for update"); // avoid the change of username by other users
    if (mysqli_fetch_array($check_username)) {
        if ($operation == 1) {
            mysqli_query($db, "commit");
			mysqli_close($db);
            echo "
                <script>
                    alert('该用户名已存在！！');
                    window.location.href = 'admin_user.php';
                </script>
            ";
            exit();
        } elseif ($operation == -1) {
            if ($username == "admin") {
                mysqli_query($db, "commit");
			    mysqli_close($db);
                echo "
                    <script>
                        alert('无法删除管理员！！');
                        window.location.href = 'admin_user.php';
                    </script>
                ";
                exit();
            } else {
                if ($password == $admin_passsword) {
                    $result = mysqli_query($db, "DELETE FROM user WHERE username='$username'");
                    if ($result) {
                        if ($identity == 0) {
                            $result = mysqli_query($db, "DELETE FROM student WHERE username='$username'");
                        } elseif ($identity == 1) {
                            $result = mysqli_query($db, "DELETE FROM teacher WHERE username='$username'");
                        }

                        if ($result) {
                            mysqli_query($db, "commit");
                            mysqli_close($db);
                            echo "
                                <script>
                                    alert('删除用户成功！！');
                                    window.location.href = 'admin_user.php';
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
                            alert('密码错误，删除失败！！');
                            window.location.href = 'admin_user.php';
                        </script>
                    ";
                    exit();
                }
            }
        }
    } else {
        if ($operation == -1) {
            mysqli_query($db, "commit");
			mysqli_close($db);
            echo "
                <script>
                    alert('该用户名不存在！！');
                    window.location.href = 'admin_user.php';
                </script>
            ";
            exit();
        } elseif ($operation == 1) {
            $result = mysqli_query($db, "INSERT INTO user(username, password, identity) VALUES ('$username', '$password', '$identity')");
            if ($result) {
                if ($identity == 0) {
                    $result = mysqli_query($db, "INSERT INTO student(username) VALUES ('$username')");
                } elseif ($identity == 1) {
                    $result = mysqli_query($db, "INSERT INTO teacher(username) VALUES ('$username')");
                }

                if ($result) {
                    mysqli_query($db, "commit");
                    mysqli_close($db);
                    echo "
                        <script>
                            alert('添加用户成功！！');
                            window.location.href = 'admin_user.php';
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
            } else {
                echo '添加失败！！', mysqli_error($db), '<br />';
                echo 'Click here to <a href="javascript:history.back(-1);">go back</a> and retry..';
                mysqli_query($db, "rollback");
                mysqli_close($db);
                exit();
            }
        }
    }
}
?>