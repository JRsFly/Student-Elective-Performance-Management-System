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

if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = check($_POST['username']);
    $password = MD5($_POST['password']);

    $db = @mysqli_connect("localhost", "root", "123456", "eas");
    if (!$db) {
        die("Fail to connect the database！！" . mysqli_connect_error());
    }
    mysqli_query($db, "begin");
    $check_user = mysqli_query($db, "SELECT * FROM user WHERE username='$username' limit 1 for update");

    if ($user = mysqli_fetch_array($check_user)) {
        if ($password == $user['password']) {
            $_SESSION['username'] = $username;
            $_SESSION['identity'] = $user['identity'];
            mysqli_query($db, "commit");
            mysqli_close($db);
            header('Location:loginSuccess.php');
            exit();
        } else {
            mysqli_query($db, "commit");
            mysqli_close($db);
            echo "<script>alert('密码错误！！')</script>";
        }        
    } else {
        mysqli_query($db, "commit");
        mysqli_close($db);
        echo "<script>alert('该用户不存在！！')</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/login.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
    <title>武汉大学教务系统</title>
</head>
<body>
    <div class="container">
        <div class="login_box">
            <h1>Login</h1>
            <form action="" method="POST">
                <div class="form">
                    <div class="item">
                        <i class="far fa-user-circle"></i>
                        <input type="text" name="username" placeholder="username">
                    </div>
                    <div class="item">
                        <i class="fas fa-unlock-alt"></i>
                        <input type="password" name="password" placeholder="password">
                    </div>
                    <button>Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>