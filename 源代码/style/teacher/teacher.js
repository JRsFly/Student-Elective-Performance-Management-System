function choose(id)
{
    if (id == "teacher") {
        window.location.href = 'teacher_teacher.php';
    } else if (id == "course") {
        window.location.href = 'teacher_course.php';
    } else if (id == "logout") {
        sessionStorage.clear();
        window.location.href = 'login.php';
    }
}