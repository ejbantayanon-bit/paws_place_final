let selectedRole = '';
let verifiedStudent = null;
let kioskLookupTimeout = null;

// Initialize event listener
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    if (form) form.addEventListener('submit', handleLoginSubmit);

    // Kiosk ID input listener for real-time lookup while typing
    const kioskIdInput = document.getElementById('kiosk-student-id');
    if (kioskIdInput) {
        kioskIdInput.addEventListener('input', debounceKioskLookup);
    }
});

function handleRoleSelect(role) {
    selectedRole = role;
    verifiedStudent = null;

    // UI Updates
    document.getElementById('selected-role').textContent = role.replace('_', ' ');
    document.getElementById('role-selection').classList.add('hidden');
    document.getElementById('login-form-container').classList.remove('hidden');

    const usernameGroup = document.getElementById('username-group');
    const kioskIdGroup = document.getElementById('kiosk-id-group');
    const studentInfoGroup = document.getElementById('student-info-group');
    const passwordGroup = document.getElementById('password-group');
    const usernameInput = document.getElementById('username');
    const kioskIdInput = document.getElementById('kiosk-student-id');
    const loginBtn = document.getElementById('login-btn');

    if (role === 'KIOSK') {
        // For Kiosk: Ask for Student ID instead of password
        usernameGroup.classList.add('hidden');
        usernameInput.removeAttribute('required');
        kioskIdGroup.classList.remove('hidden');
        passwordGroup.classList.add('hidden');
        document.getElementById('password').removeAttribute('required');
        loginBtn.textContent = 'PROCEED TO ORDERING';
        loginBtn.style.display = 'none'; // Hide initially, show when student is verified
        kioskIdInput.value = '';
        studentInfoGroup.classList.add('hidden');
        kioskIdInput.focus();
    } else {
        // For Staff/Admin: Username and Password required
        usernameGroup.classList.remove('hidden');
        usernameInput.setAttribute('required', 'true');
        kioskIdGroup.classList.add('hidden');
        passwordGroup.classList.remove('hidden');
        document.getElementById('password').setAttribute('required', 'true');
        studentInfoGroup.classList.add('hidden');
        loginBtn.textContent = 'AUTHENTICATE';
        loginBtn.style.display = 'block';
        usernameInput.focus();
    }
}

function resetSelection() {
    selectedRole = '';
    verifiedStudent = null;
    document.getElementById('login-form-container').classList.add('hidden');
    document.getElementById('role-selection').classList.remove('hidden');
    document.getElementById('login-form').reset();
    document.getElementById('student-info-group').classList.add('hidden');
    document.getElementById('login-btn').style.display = 'block';
}

function debounceKioskLookup() {
    // Clear previous timeout
    if (kioskLookupTimeout) clearTimeout(kioskLookupTimeout);

    // Wait 500ms after user stops typing before making API call
    kioskLookupTimeout = setTimeout(() => {
        handleKioskStudentIdLookup();
    }, 500);
}

async function handleKioskStudentIdLookup() {
    const studentId = document.getElementById('kiosk-student-id').value.trim();
    const studentInfoGroup = document.getElementById('student-info-group');
    const studentNameDisplay = document.getElementById('student-name-display');
    const loginBtn = document.getElementById('login-btn');

    if (!studentId) {
        studentInfoGroup.classList.add('hidden');
        loginBtn.style.display = 'none';
        verifiedStudent = null;
        return;
    }

    try {
        // Use server-backed Grubhound lookup (server holds token)
        const endpoint = '../server/api/get_student.php';

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: studentId })
        });

        const data = await response.json();

        if (data.success && data.student) {
            // Get student name and department from response
            const studentName = data.student.name || data.student.full_name || 'Unknown Student';
            const studentDept = data.student.department || 'No Department';
            verifiedStudent = {
                id: studentId,
                name: studentName,
                department: studentDept,
                data: data.student
            };

            studentNameDisplay.textContent = studentName;
            document.getElementById('student-department-display').textContent = studentDept;
            studentInfoGroup.classList.remove('hidden');
            loginBtn.style.display = 'block';
            loginBtn.focus(); // Focus button so user can easily press it
            alertUser(`✓ ${studentName} verified - Ready to proceed`, 'success');
        } else {
            verifiedStudent = null;
            studentInfoGroup.classList.add('hidden');
            loginBtn.style.display = 'none';
            alertUser(data.message || 'Student ID not found', 'error');
        }
    } catch (error) {
        console.error('Error looking up student:', error);
        verifiedStudent = null;
        studentInfoGroup.classList.add('hidden');
        loginBtn.style.display = 'none';
        alertUser('Failed to verify student ID', 'error');
    }
}

function handleLoginSubmit(event) {
    event.preventDefault();

    // For Kiosk: Check if student is verified
    if (selectedRole === 'KIOSK') {
        if (!verifiedStudent) {
            alertUser('Please enter and verify your student ID first', 'error');
            return;
        }

        const loginButton = document.getElementById('login-btn');
        loginButton.disabled = true;
        loginButton.textContent = 'Launching Kiosk...';

        // Store student info in localStorage and via form submission to set session
        localStorage.setItem('userRole', 'KIOSK');
        localStorage.setItem('userName', verifiedStudent.name);
        localStorage.setItem('userId', verifiedStudent.id);
        localStorage.setItem('studentData', JSON.stringify(verifiedStudent.data));

        // Create hidden form to submit to set PHP session
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../server/api/set_kiosk_session.php';

        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'full_name';
        nameInput.value = verifiedStudent.name;

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'user_id';
        idInput.value = verifiedStudent.id;

        const deptInput = document.createElement('input');
        deptInput.type = 'hidden';
        deptInput.name = 'department';
        deptInput.value = verifiedStudent.department;

        form.appendChild(nameInput);
        form.appendChild(idInput);
        form.appendChild(deptInput);
        document.body.appendChild(form);

        alertUser(`Welcome, ${verifiedStudent.name}!`, 'success');
        setTimeout(() => {
            form.submit();
        }, 400);
        return;
    }

    // For Staff/Admin: Regular authentication
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const loginButton = document.getElementById('login-btn');

    loginButton.disabled = true;
    loginButton.textContent = 'Verifying...';
    // Build form data
    const fd = new FormData();
    fd.append('role', selectedRole);
    fd.append('password', password);
    if (selectedRole !== 'KIOSK') fd.append('username', username);

    fetch('../server/auth_login.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data && data.success) {
                localStorage.setItem('userRole', data.role);
                localStorage.setItem('userName', data.full_name);
                localStorage.setItem('userId', data.user_id);
                alertUser(`Success! Welcome, ${data.full_name}.`, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect || redirectDefault(data.role);
                }, 400);
            } else {
                alertUser(data.message || 'Invalid credentials', 'error');
                loginButton.disabled = false;
                loginButton.textContent = (selectedRole === 'KIOSK') ? 'LAUNCH KIOSK' : 'AUTHENTICATE';
            }
        })
        .catch(err => {
            console.error(err);
            alertUser('Network or server error', 'error');
            loginButton.disabled = false;
            loginButton.textContent = (selectedRole === 'KIOSK') ? 'LAUNCH KIOSK' : 'AUTHENTICATE';
        });
}

function redirectToDashboard(role) {
    window.location.href = redirectDefault(role);
}

function redirectDefault(role) {
    if (!role) return 'index.php';
    switch (role.toLowerCase()) {
        case 'cashier': return 'index.php';
        case 'admin': return 'adminDashboard.php';
        case 'barista': return '4_baristaKDS.html';
        default: return 'index.php';
    }
}

function alertUser(message, type = 'info') {
    const container = document.getElementById('alert-container');
    if (!container) return;
    let color = { info: 'bg-blue-500', success: 'bg-green-600', error: 'bg-red-600' }[type];
    const alert = document.createElement('div');
    alert.className = `p-4 mb-2 rounded shadow-xl ${color} text-white font-bold text-sm tracking-wide transition-opacity duration-300`;
    alert.textContent = message;
    container.appendChild(alert);
    setTimeout(() => { alert.style.opacity = '0'; setTimeout(() => alert.remove(), 300); }, 3000);
}