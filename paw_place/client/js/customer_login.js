let verifiedUser = null;

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('student-login-form');
    if (loginForm) loginForm.addEventListener('submit', handleStudentLogin);

    // Reset verification if ID changes
    const idInput = document.getElementById('student-id');
    if (idInput) {
        idInput.addEventListener('input', () => {
            verifiedUser = null;
            document.getElementById('id-info-group').classList.add('hidden');
            document.getElementById('start-ordering-btn').classList.add('hidden');
            const loginBtn = document.getElementById('login-btn');
            loginBtn.classList.remove('hidden');
            loginBtn.disabled = false;
            loginBtn.textContent = 'AUTHENTICATE';
        });
    }
});

function toggleHelpdeskBox() {
    const box = document.getElementById('helpdesk-box');
    if (box) box.classList.toggle('hidden');
}

async function handleStudentLogin(event) {
    event.preventDefault();

    const studentId = document.getElementById('student-id').value.trim();
    const loginBtn = document.getElementById('login-btn');
    const infoGroup = document.getElementById('id-info-group');
    const nameDisplay = document.getElementById('id-name-display');
    const deptDisplay = document.getElementById('id-department-display');
    const typeBadge = document.getElementById('id-type-badge');
    const startBtn = document.getElementById('start-ordering-btn');

    if (!studentId) {
        alertUser('Please enter your ID Number', 'error');
        return;
    }

    loginBtn.disabled = true;
    loginBtn.textContent = 'VERIFYING...';

    try {
        // 1. Try Student Lookup First
        let response = await fetch('../server/api/get_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId })
        });

        let data = null;
        try { data = await response.json(); } catch (e) { console.error('Student API parse error:', e); }
        console.log('Student API response:', data);

        if (data && data.success && data.student) {
            const student = data.student;
            const name = student.full_name || student.name || 'Student';
            const dept = student.department || student.department_name || student.program || 'No Department';

            verifiedUser = { id: studentId, name, department: dept, role: 'STUDENT' };

            infoGroup.className = 'bg-blue-50 border-2 border-blue-300 rounded-lg p-4 text-center';
            nameDisplay.className = 'text-lg font-bold text-blue-700 mb-1';
            deptDisplay.className = 'text-blue-700 font-semibold';
            typeBadge.className = 'mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block bg-blue-200 text-blue-800';
            typeBadge.textContent = 'STUDENT';
            nameDisplay.textContent = name;
            deptDisplay.textContent = dept;

            loginBtn.classList.add('hidden');
            startBtn.classList.remove('hidden');
            alertUser(`Student Verified: ${name}`, 'success');
            return;
        }

        // 2. Try Employee Lookup Second
        response = await fetch('../server/api/get_employee.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ employee_id: studentId })
        });

        data = null;
        try { data = await response.json(); } catch (e) { console.error('Employee API parse error:', e); }
        console.log('Employee API response:', data);

        if (data && data.success && data.employee) {
            const employee = data.employee;
            const name = employee.full_name || employee.name || 'Employee';
            const dept = employee.department || employee.department_name || 'No Department';

            verifiedUser = { id: studentId, name, department: dept, role: 'EMPLOYEE' };

            infoGroup.className = 'bg-orange-50 border-2 border-orange-300 rounded-lg p-4 text-center';
            nameDisplay.className = 'text-lg font-bold text-orange-700 mb-1';
            deptDisplay.className = 'text-orange-700 font-semibold';
            typeBadge.className = 'mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block bg-orange-200 text-orange-800';
            typeBadge.textContent = 'EMPLOYEE';
            nameDisplay.textContent = name;
            deptDisplay.textContent = dept;

            loginBtn.classList.add('hidden');
            startBtn.classList.remove('hidden');
            alertUser(`Employee Verified: ${name}`, 'success');
            return;
        }

        // Both failed
        const errorMsg = (data && data.message) ? data.message : 'ID not found. Please check your ID Number.';
        infoGroup.classList.add('hidden');
        startBtn.classList.add('hidden');
        alertUser(errorMsg, 'error');
        loginBtn.disabled = false;
        loginBtn.textContent = 'AUTHENTICATE';
    } catch (error) {
        console.error('Login error:', error);
        alertUser('Failed to connect to server. Please try again.', 'error');
        loginBtn.disabled = false;
        loginBtn.textContent = 'AUTHENTICATE';
    }
}

function proceedToKiosk() {
    if (!verifiedUser) {
        alertUser('Please verify your ID first', 'error');
        return;
    }

    const startBtn = document.getElementById('start-ordering-btn');
    startBtn.disabled = true;
    startBtn.textContent = 'LOADING...';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../server/api/set_kiosk_session.php';

    const inputs = {
        'full_name': verifiedUser.name,
        'user_id': verifiedUser.id,
        'department': verifiedUser.department
    };

    for (const [key, value] of Object.entries(inputs)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }

    document.body.appendChild(form);
    alertUser(`Welcome, ${verifiedUser.name}!`, 'success');
    setTimeout(() => { form.submit(); }, 500);
}

function alertUser(message, type = 'info') {
    const container = document.getElementById('alert-container');
    if (!container) return;
    let color = { info: 'bg-blue-500', success: 'bg-green-600', error: 'bg-red-600' }[type];
    const alert = document.createElement('div');
    alert.className = `${color} text-white px-6 py-3 rounded-lg shadow-lg mb-2 animate-pulse`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => alert.remove(), 3000);
}

