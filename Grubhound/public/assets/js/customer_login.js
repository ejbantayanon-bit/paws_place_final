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
            // Only reset if we actually had a verified user
            if (verifiedUser) {
                verifiedUser = null;
                document.getElementById('id-info-group').classList.add('hidden');
                document.getElementById('id-login-btn').style.display = 'none';
                document.getElementById('verify-id-btn').style.display = 'block';
            }
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
        const response = await fetch(BASE_URL + 'kiosk/lookup', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_number: idNumber })
        });

        const data = await response.json();

        if (data.success && data.data) {
            const user = data.data;
            verifiedUser = {
                id: user.id,
                name: user.name,
                department: user.department,
                role: data.type, // STUDENT or EMPLOYEE
                data: user
            };

            const isStudent = data.type === 'STUDENT';
            const colorClass = isStudent ? 'blue' : 'orange';

            // Apply Styling
            infoGroup.className = `bg-${colorClass}-50 border-2 border-${colorClass}-300 rounded-lg p-4 text-center`;
            nameDisplay.className = `text-lg font-bold text-${colorClass}-700 mb-2`;
            deptDisplay.className = `text-${colorClass}-700 font-semibold`;
            typeBadge.className = `mt-2 text-xs font-bold uppercase tracking-widest px-2 py-1 rounded inline-block bg-${colorClass}-200 text-${colorClass}-800`;
            typeBadge.textContent = data.type;

            nameDisplay.textContent = user.name;
            deptDisplay.textContent = user.department;
            infoGroup.classList.remove('hidden');

            verifyBtn.style.display = 'none';
            loginBtn.style.display = 'block';
            loginBtn.focus();

            alertUser(`${data.type} Verified: ${user.name}`, 'success');
            return;
        }

        // Not found
        verifiedUser = null;
        infoGroup.classList.add('hidden');
        loginBtn.style.display = 'none';
        verifyBtn.style.display = 'block';
        verifyBtn.disabled = false;
        verifyBtn.textContent = 'VERIFY ID';
        alertUser(data.message || 'ID Number not found', 'error');

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

async function submitIdLogin(event) {
    event.preventDefault();

    // If not verified yet, try to verify first
    if (!verifiedUser) {
        await handleKioskIdLookup();
        return;
    }

    const loginBtn = document.getElementById('id-login-btn');
    loginBtn.disabled = true;
    loginBtn.textContent = 'Starting...';

    initiateSession({
        user_id: verifiedUser.id,
        full_name: verifiedUser.name,
        department: verifiedUser.department,
        user_type: verifiedUser.role
    });
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

    initiateSession({
        user_id: 'GUEST',
        full_name: guestName,
        department: 'Guest',
        user_type: 'GUEST'
    });
}

async function initiateSession(sessionData) {
    try {
        const response = await fetch(BASE_URL + 'kiosk/set-session', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(sessionData)
        });
        const data = await response.json();

        if (data.success && data.redirect) {
            alertUser(`Welcome, ${sessionData.full_name}!`, 'success');
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 400);
        } else {
            alertUser('Failed to start session', 'error');
        }
    } catch (e) {
        console.error(e);
        alertUser('Network error', 'error');
    }
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
