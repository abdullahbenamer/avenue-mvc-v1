
// Fetch and populate courses
function fetchCourses() {
    var custId = document.getElementById('cust_id').value;
    if (custId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_courses.php?cust_id=' + custId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('course_id').innerHTML = xhr.responseText;
            } else {
                console.error('Failed to fetch courses.');
            }
        };
        xhr.send();
    }
}

// Fetch and populate course dates
function fetchCourseDates() {
    var courseId = document.getElementById('course_id').value;
    var custId = document.getElementById('cust_id').value;
    if (courseId && custId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_course_dates.php?course_id=' + courseId + '&cust_id=' + custId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('course_date').innerHTML = xhr.responseText;
            } else {
                console.error('Failed to fetch course dates.');
            }
        };
        xhr.send();
    }
}

// Fetch and populate trainees
function fetchTrainees() {
    var courseId = document.getElementById('course_id').value;
    var courseDate = document.getElementById('course_date').value;
    var custId = document.getElementById('cust_id').value;
    if (courseId && courseDate && custId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_trainees.php?course_id=' + courseId + '&course_date=' + courseDate + '&cust_id=' + custId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementsByName('full_name')[0].value = xhr.responseText;
            } else {
                console.error('Failed to fetch trainees.');
            }
        };
        xhr.send();
    }
}

// Event listeners
document.getElementById('cust_id').addEventListener('change', fetchCourses);
document.getElementById('course_id').addEventListener('change', fetchCourseDates);
document.getElementById('course_date').addEventListener('change', fetchTrainees);
