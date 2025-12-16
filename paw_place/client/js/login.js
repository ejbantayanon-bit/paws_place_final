let selectedRole = '';

// Initialize event listener
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    if (form) form.addEventListener('submit', handleLoginSubmit);
});

function handleRoleSelect(role) {
    selectedRole = role;
    
    // UI Updates
    document.getElementById('selected-role').textContent = role.replace('_', ' ');
    document.getElementById('role-selection').classList.add('hidden');
    document.getElementById('login-form-container').classList.remove('hidden');
    
    const usernameGroup = document.getElementById('username-group');
    const usernameInput = document.getElementById('username');
    const loginBtn = document.getElementById('login-btn');

    if (role === 'KIOSK') {
        // Hide Username for Kiosk, make it not required
        usernameGroup.classList.add('hidden');
        usernameInput.removeAttribute('required');
        loginBtn.textContent = 'LAUNCH KIOSK';
        document.getElementById('password').focus();
    } else {
        // Show Username for Staff/Admin
        usernameGroup.classList.remove('hidden');
        usernameInput.setAttribute('required', 'true');
        loginBtn.textContent = 'AUTHENTICATE';
        usernameInput.focus();
    }
}

function resetSelection() {
    selectedRole = '';
    document.getElementById('login-form-container').classList.add('hidden');
    document.getElementById('role-selection').classList.remove('hidden');
    document.getElementById('login-form').reset();
}

function handleLoginSubmit(event) {
    event.preventDefault();
    
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
    if (!role) return '3_index.html';
    switch (role.toLowerCase()) {
        case 'cashier': return '3_index.php';
        case 'admin': return '5_adminDashboard.php';
        case 'barista': return '4_baristaKDS.html';
        default: return '3_index.php';
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