let verifiedUser = null;

document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('student-login-form');
    if (loginForm) loginForm.addEventListener('submit', handleStudentLogin);

    // Reset verification if ID or password changes
    const idInput = document.getElementById('student-id');
    const pwInput = document.getElementById('student-password');
    const resetFn = () => {
        verifiedUser = null;
        document.getElementById('id-info-group').classList.add('hidden');
        document.getElementById('start-ordering-btn').classList.add('hidden');
        const loginBtn = document.getElementById('login-btn');
        loginBtn.classList.remove('hidden');
        loginBtn.disabled = false;
        loginBtn.textContent = 'AUTHENTICATE';
    };
    if (idInput) idInput.addEventListener('input', resetFn);
    if (pwInput) pwInput.addEventListener('input', resetFn);
});

function toggleHelpdeskBox() {
    const box = document.getElementById('helpdesk-box');
    if (box) box.classList.toggle('hidden');
}

function togglePassword() {
    const pw = document.getElementById('student-password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    if (pw.type === 'password') {
        pw.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        pw.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

async function handleStudentLogin(event) {
    event.preventDefault();

    const studentId = document.getElementById('student-id').value.trim();
    const password = document.getElementById('student-password').value;
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
    if (!password) {
        alertUser('Please enter your Password', 'error');
        return;
    }

    loginBtn.disabled = true;
    loginBtn.textContent = 'VERIFYING...';

    try {
        // Unified Login - Checks both Student and Employee in one server-side request
        const response = await fetch('../server/api/unified_customer_login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: studentId, password: password })
        });

        const data = await response.json();
        console.log('Unified login response:', data);

        if (data && data.success && data.user) {
            const user = data.user;
            const name = user.full_name || user.name || 'User';
            const dept = user.department || user.department_name || user.program || 'No Department';
            const type = data.type || 'USER';

            verifiedUser = { id: studentId, name, department: dept, role: type };

            const isStudent = type === 'STUDENT';
            const themeClass = isStudent ? 'blue' : 'orange';

            infoGroup.className = `bg-${themeClass}-50 border-2 border-${themeClass}-300 rounded-lg p-4 text-center`;
            nameDisplay.className = `text-lg font-bold text-${themeClass}-700 mb-1`;
            deptDisplay.className = `text-${themeClass}-700 font-semibold`;
            typeBadge.className = `mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block bg-${themeClass}-200 text-${themeClass}-800`;

            typeBadge.textContent = type;
            nameDisplay.textContent = name;
            deptDisplay.textContent = dept;

            loginBtn.classList.add('hidden');
            startBtn.classList.remove('hidden');
            infoGroup.classList.remove('hidden');
            alertUser(`${type} Verified: ${name}`, 'success');
            return;
        }

        // Failed — show error
        const errorMsg = (data && data.message) ? data.message : 'Invalid ID or Password. Please try again.';
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
