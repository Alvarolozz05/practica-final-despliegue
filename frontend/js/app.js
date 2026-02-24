const API_URL = 'https://practica-final-despliegue.onrender.com';

document.addEventListener('DOMContentLoaded', () => {
    loadStudents();
    loadCourses();
    loadEnrollments();

    // Event Listeners
    document.getElementById('student-form').addEventListener('submit', createStudent);
    document.getElementById('course-form').addEventListener('submit', createCourse);
    document.getElementById('enroll-form').addEventListener('submit', enrollStudent);
});

async function loadStudents() {
    const res = await fetch(`${API_URL}/students.php`);
    const students = await res.json();
    
    // Lista
    const list = document.getElementById('student-list');
    list.innerHTML = '';
    
    // Select para inscripción
    const select = document.getElementById('enroll-student');
    select.innerHTML = '<option value="">Seleccionar Estudiante</option>';

    students.forEach(student => {
        // En lista
        const li = document.createElement('li');
        li.innerHTML = `
            ${student.name} (${student.email})
            <button class="delete-btn" onclick="deleteStudent(${student.id})">x</button>
        `;
        list.appendChild(li);

        // En select
        const option = document.createElement('option');
        option.value = student.id;
        option.textContent = student.name;
        select.appendChild(option);
    });
}

async function loadCourses() {
    const res = await fetch(`${API_URL}/courses.php`);
    const courses = await res.json();
    
    // Lista
    const list = document.getElementById('course-list');
    list.innerHTML = '';
    
    // Select para inscripción
    const select = document.getElementById('enroll-course');
    select.innerHTML = '<option value="">Seleccionar Curso</option>';

    courses.forEach(course => {
        const li = document.createElement('li');
        li.innerHTML = `
            <strong>${course.name}</strong>: ${course.description}
            <button class="delete-btn" onclick="deleteCourse(${course.id})">x</button>
        `;
        list.appendChild(li);

        const option = document.createElement('option');
        option.value = course.id;
        option.textContent = course.name;
        select.appendChild(option);
    });
}

async function loadEnrollments() {
    const res = await fetch(`${API_URL}/enrollments.php`);
    const enrollments = await res.json();
    
    const tbody = document.querySelector('#enrollment-table tbody');
    tbody.innerHTML = '';

    enrollments.forEach(enroll => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${enroll.student_name}</td>
            <td>${enroll.course_name}</td>
            <td><button class="delete-btn" onclick="deleteEnrollment(${enroll.id})">Eliminar</button></td>
        `;
        tbody.appendChild(tr);
    });
}

// Crear
async function createStudent(e) {
    e.preventDefault();
    const name = document.getElementById('student-name').value;
    const email = document.getElementById('student-email').value;

    await fetch(`${API_URL}/students.php`, {
        method: 'POST',
        body: JSON.stringify({ name, email })
    });

    document.getElementById('student-form').reset();
    loadStudents();
}

async function createCourse(e) {
    e.preventDefault();
    const name = document.getElementById('course-name').value;
    const desc = document.getElementById('course-desc').value;

    await fetch(`${API_URL}/courses.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, description: desc })
    });

    document.getElementById('course-form').reset();
    loadCourses();
}

async function enrollStudent(e) {
    e.preventDefault();
    const studentId = document.getElementById('enroll-student').value;
    const courseId = document.getElementById('enroll-course').value;

    if (!studentId || !courseId) return;

    await fetch(`${API_URL}/enrollments.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ student_id: studentId, course_id: courseId })
    });

    loadEnrollments();
}

// Eliminar (funciones globales para onclick)
window.deleteStudent = async (id) => {
    if(!confirm('¿Seguro?')) return;
    await fetch(`${API_URL}/students.php?id=${id}`, { method: 'DELETE' });
    loadStudents();
    loadEnrollments(); // Recargar por si se borraron inscripciones en cascada
}

window.deleteCourse = async (id) => {
    if(!confirm('¿Seguro?')) return;
    await fetch(`${API_URL}/courses.php?id=${id}`, { method: 'DELETE' });
    loadCourses();
    loadEnrollments();
}

window.deleteEnrollment = async (id) => {
    await fetch(`${API_URL}/enrollments.php?id=${id}`, { method: 'DELETE' });
    loadEnrollments();
}
