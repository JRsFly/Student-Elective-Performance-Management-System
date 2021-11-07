function choose(id)
{
    if (id == "student") {
        window.location.href = 'student_student.php';
    } else if (id == "course") {
        window.location.href = 'student_course.php';
    } else if (id == "grade") {
        window.location.href = 'student_grade.php';
    } else if (id == "logout") {
        sessionStorage.clear();
        window.location.href = 'login.php';
    }
}