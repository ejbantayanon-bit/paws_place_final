let selectedMode = '';
let verifiedUser = null;
let kioskLookupTimeout = null;

// Initialize event listener
document.addEventListener('DOMContentLoaded', () => {
    const idLoginForm = document.getElementById('id-login-form');
    if (idLoginForm) idLoginForm.addEventListener('submit', submitIdLogin);

    const guestForm = document.getElementById('guest-form');
    if (guestForm) guestForm.addEventListener('submit', handleGuestSubmit);

    // Kiosk ID input listener - reset verification if ID changes
    const kioskIdInput = document.getElementById('kiosk-id-number');
    if (kioskIdInput) {
        kioskIdInput.addEventListener('input', () => {
            verifiedUser = null;
            document.getElementById('id-info-group').classList.add('hidden');
            document.getElementById('id-login-btn').style.display = 'none';
            document.getElementById('verify-id-btn').style.display = 'block';
        });
    }
});

function handleModeSelect(mode) {
    selectedMode = mode;
    verifiedUser = null;

    document.getElementById('mode-selection').classList.add('hidden');

    if (mode === 'ID_LOGIN') {
        document.getElementById('id-login-container').classList.remove('hidden');
        document.getElementById('guest-form-container').classList.add('hidden');
        document.getElementById('kiosk-id-number').focus();
    } else {
        document.getElementById('id-login-container').classList.add('hidden');
        document.getElementById('guest-form-container').classList.remove('hidden');
        document.getElementById('guest-name').focus();
    }
}

function resetModeSelection() {
    selectedMode = '';
    verifiedUser = null;
    document.getElementById('mode-selection').classList.remove('hidden');
    document.getElementById('id-login-container').classList.add('hidden');
    document.getElementById('guest-form-container').classList.add('hidden');

    document.getElementById('id-login-form').reset();
    document.getElementById('guest-form').reset();

    document.getElementById('id-info-group').classList.add('hidden');
    document.getElementById('id-login-btn').style.display = 'none';
}

async function handleKioskIdLookup() {
    const idNumber = document.getElementById('kiosk-id-number').value.trim();
    const infoGroup = document.getElementById('id-info-group');
    const nameDisplay = document.getElementById('id-name-display');
    const deptDisplay = document.getElementById('id-department-display');
    const typeBadge = document.getElementById('id-type-badge');
    const loginBtn = document.getElementById('id-login-btn');
    const verifyBtn = document.getElementById('verify-id-btn');

    if (!idNumber) {
        alertUser('Please enter an ID number', 'error');
        return;
    }

    verifyBtn.disabled = true;
    verifyBtn.textContent = 'Verifying...';

    // Reset styling
    infoGroup.className = 'hidden border-2 rounded-lg p-4 text-center';
    nameDisplay.className = 'text-lg font-bold mb-2';
    deptDisplay.className = 'font-semibold';

    try {
        // 1. Try Student Lookup First
        let response = await fetch('../server/api/get_student.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: idNumber })
        });

        let data = await response.json();

        if (data.success && data.student) {
            // Student Found
            const name = data.student.name || data.student.full_name || 'Unknown Student';
            const dept = data.student.department || 'No Department';

            verifiedUser = {
                id: idNumber,
                name: name,
                department: dept,
                role: 'STUDENT',
                data: data.student
            };

            // Apply Student Styling (Blue)
            infoGroup.className = 'bg-blue-50 border-2 border-blue-300 rounded-lg p-4 text-center';
            nameDisplay.className = 'text-lg font-bold text-blue-700 mb-2';
            deptDisplay.className = 'text-blue-700 font-semibold';
            typeBadge.className = 'mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block bg-blue-200 text-blue-800';
            typeBadge.textContent = 'STUDENT';

            nameDisplay.textContent = name;
            deptDisplay.textContent = dept;
            infoGroup.classList.remove('hidden');

            verifyBtn.style.display = 'none';
            loginBtn.style.display = 'block';
            loginBtn.focus();

            alertUser(`Student Verified: ${name}`, 'success');
            return;
        }

        // 2. Try Employee Lookup Second (if student failed)
        response = await fetch('../server/api/get_employee.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ employee_id: idNumber })
        });

        data = await response.json();

        if (data.success && data.employee) {
            // Employee Found
            const name = data.employee.name || data.employee.full_name || 'Unknown Employee';
            const dept = data.employee.department || 'No Department';

            verifiedUser = {
                id: idNumber,
                name: name,
                department: dept,
                role: 'EMPLOYEE',
                data: data.employee
            };

            // Apply Employee Styling (Orange)
            infoGroup.className = 'bg-orange-50 border-2 border-orange-300 rounded-lg p-4 text-center';
            nameDisplay.className = 'text-lg font-bold text-orange-700 mb-2';
            deptDisplay.className = 'text-orange-700 font-semibold';
            typeBadge.className = 'mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block bg-orange-200 text-orange-800';
            typeBadge.textContent = 'EMPLOYEE';

            nameDisplay.textContent = name;
            deptDisplay.textContent = dept;
            infoGroup.classList.remove('hidden');

            verifyBtn.style.display = 'none';
            loginBtn.style.display = 'block';
            loginBtn.focus();

            alertUser(`Employee Verified: ${name}`, 'success');
            return;
        }

        // If we get here, neither was found
        verifiedUser = null;
        infoGroup.classList.add('hidden');
        loginBtn.style.display = 'none';
        verifyBtn.style.display = 'block';
        verifyBtn.disabled = false;
        verifyBtn.textContent = 'VERIFY ID';
        alertUser('ID Number not found (Student or Employee)', 'error');

    } catch (error) {
        console.error('Error looking up ID:', error);
        verifiedUser = null;
        infoGroup.classList.add('hidden');
        loginBtn.style.display = 'none';
        verifyBtn.style.display = 'block';
        verifyBtn.disabled = false;
        verifyBtn.textContent = 'VERIFY ID';
        alertUser('Failed to verify ID', 'error');
    }
}

function submitIdLogin(event) {
    event.preventDefault();

    if (!verifiedUser) {
        alertUser('Please verify your ID first', 'error');
        return;
    }

    const loginBtn = document.getElementById('id-login-btn');
    loginBtn.disabled = true;
    loginBtn.textContent = 'Starting...';

    // Create hidden form to submit and set session
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../server/api/set_kiosk_session.php';

    const nameInput = document.createElement('input');
    nameInput.type = 'hidden';
    nameInput.name = 'full_name';
    nameInput.value = verifiedUser.name;

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'user_id';
    idInput.value = verifiedUser.id;

    const deptInput = document.createElement('input');
    deptInput.type = 'hidden';
    deptInput.name = 'department';
    deptInput.value = verifiedUser.department;

    // If it's an employee, we might want to track that role if specific logic is needed later
    // For now we treat both as generic KIOSK users in session, but we can pass the specific role if needed
    if (verifiedUser.role === 'EMPLOYEE') {
        const roleInput = document.createElement('input');
        roleInput.type = 'hidden';
        roleInput.name = 'user_specific_role';
        roleInput.value = 'EMPLOYEE';
        form.appendChild(roleInput);
    }

    form.appendChild(nameInput);
    form.appendChild(idInput);
    form.appendChild(deptInput);
    document.body.appendChild(form);

    alertUser(`Welcome, ${verifiedUser.name}!`, 'success');
    setTimeout(() => {
        form.submit();
    }, 400);
}

function handleGuestSubmit(event) {
    event.preventDefault();

    const guestName = document.getElementById('guest-name').value.trim();
    if (!guestName) {
        alertUser('Please enter your name', 'error');
        return;
    }

    const loginButton = document.getElementById('guest-login-btn');
    loginButton.disabled = true;
    loginButton.textContent = 'Starting...';

    // Create hidden form to submit and set session
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../server/api/set_kiosk_session.php';

    const nameInput = document.createElement('input');
    nameInput.type = 'hidden';
    nameInput.name = 'full_name';
    nameInput.value = guestName;

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'user_id';
    idInput.value = 'GUEST';

    const deptInput = document.createElement('input');
    deptInput.type = 'hidden';
    deptInput.name = 'department';
    deptInput.value = 'Guest';

    form.appendChild(nameInput);
    form.appendChild(idInput);
    form.appendChild(deptInput);
    document.body.appendChild(form);

    alertUser(`Welcome, ${guestName}!`, 'success');
    setTimeout(() => {
        form.submit();
    }, 400);
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
