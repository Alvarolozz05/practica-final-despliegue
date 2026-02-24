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
    try {
        const res = await fetch(`${API_URL}/students.php`);
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Error ${res.status}: ${errorText}`);
        }
        const students = await res.json();
        
        // Lista
        const list = document.getElementById('student-list');
        list.innerHTML = '';
        
        // Select para inscripción
        const select = document.getElementById('enroll-student');
        select.innerHTML = '<option value="">Seleccionar Estudiante</option>';

        if (Array.isArray(students)) {
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
        } else {
            console.error('La respuesta no es un array:', students);
        }
    } catch (error) {
        console.error('Error cargando estudiantes:', error);
        alert('Error al cargar estudiantes. Revisa la consola para más detalles.');
    }
}

async function loadCourses() {
    try {
        const res = await fetch(`${API_URL}/courses.php`);
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Error ${res.status}: ${errorText}`);
        }
        const courses = await res.json();
        
        // Lista
        const list = document.getElementById('course-list');
        list.innerHTML = '';
        
        // Select para inscripción
        const select = document.getElementById('enroll-course');
        select.innerHTML = '<option value="">Seleccionar Curso</option>';

        if (Array.isArray(courses)) {
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
    } catch (error) {
        console.error('Error cargando cursos:', error);
    }
}

async function loadEnrollments() {
    try {
        const res = await fetch(`${API_URL}/enrollments.php`);
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`Error ${res.status}: ${errorText}`);
        }
        const enrollments = await res.json();
        
        const tbody = document.querySelector('#enrollment-table tbody');
        tbody.innerHTML = '';

        if (Array.isArray(enrollments)) {
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
    } catch (error) {
        console.error('Error cargando inscripciones:', error);
    }
}

// Crear
async function createStudent(e) {
    e.preventDefault();
    const name = document.getElementById('student-name').value;
    const email = document.getElementById('student-email').value;

    try {
        const res = await fetch(`${API_URL}/students.php`, {
            method: 'POST',
            body: JSON.stringify({ name, email })
        });
        if (!res.ok) throw new Error(await res.text());
        document.getElementById('student-form').reset();
        loadStudents();
    } catch (error) {
        console.error("Error creating student:", error);
        alert("Error creating student: " + error.message);
    }
}

async function createCourse(e) {
    e.preventDefault();
    const name = document.getElementById('course-name').value;
    const desc = document.getElementById('course-desc').value;

    try {
        const res = await fetch(`${API_URL}/courses.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, description: desc })
        });
        if (!res.ok) throw new Error(await res.text());
        document.getElementById('course-form').reset();
        loadCourses();
    } catch (error) {
        console.error("Error creating course:", error);
        alert("Error creating course: " + error.message);
    }
}

async function enrollStudent(e) {
    e.preventDefault();
    const studentId = document.getElementById('enroll-student').value;
    const courseId = document.getElementById('enroll-course').value;

    if (!studentId || !courseId) return;

    try {
        const res = await fetch(`${API_URL}/enrollments.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId, course_id: courseId })
        });
        if (!res.ok) throw new Error(await res.text());
        loadEnrollments();
    } catch (error) {
        console.error("Error enrolling:", error);
        alert("Error enrolling: " + error.message);
    }
}

// Eliminar (funciones globales para onclick)
window.deleteStudent = async (id) => {
    if(!confirm('¿Seguro?')) return;
    try {
        const res = await fetch(`${API_URL}/students.php?id=${id}`, { method: 'DELETE' });
        if (!res.ok) throw new Error(await res.text());
        loadStudents();
        loadEnrollments(); 
    } catch (error) {
        console.error("Error deleting student:", error);
        alert("Error deleting student: " + error.message);
    }
}

window.deleteCourse = async (id) => {
    if(!confirm('¿Seguro?')) return;
    try {
        const res = await fetch(`${API_URL}/courses.php?id=${id}`, { method: 'DELETE' });
        if (!res.ok) throw new Error(await res.text());
        loadCourses();
        loadEnrollments();
    } catch (error) {
        console.error("Error deleting course:", error);
        alert("Error deleting course: " + error.message);
    }
}

window.deleteEnrollment = async (id) => {
    if(!confirm('¿Seguro?')) return;
    try {
        const res = await fetch(`${API_URL}/enrollments.php?id=${id}`, { method: 'DELETE' });
        if (!res.ok) throw new Error(await res.text());
        loadEnrollments();
    } catch (error) {
        console.error("Error deleting enrollment:", error);
        alert("Error deleting enrollment: " + error.message);
    }
}
