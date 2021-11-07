function choose(id)
{
    if (id == "user") {
        window.location.href = 'admin_user.php';
    } else if (id == "course") {
        window.location.href = 'admin_course.php';
    } else if (id == "grade") {
        window.location.href = 'admin_grade.php';
    } else if (id == "logout") {
        sessionStorage.clear();
        window.location.href = 'login.php';
    }
}