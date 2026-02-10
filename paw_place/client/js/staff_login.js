let selectedRole = '';

// Initialize event listener
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    if (form) form.addEventListener('submit', handleLoginSubmit);

    // Refresh external API token on load
    refreshGrubhoundToken();
});

function handleRoleSelect(role) {
    selectedRole = role;

    // UI Updates
    document.getElementById('selected-role').textContent = role.replace('_', ' ');
    document.getElementById('role-selection').classList.add('hidden');
    document.getElementById('login-form-container').classList.remove('hidden');

    const usernameInput = document.getElementById('username');
    const loginBtn = document.getElementById('login-btn');

    loginBtn.textContent = role === 'ADMIN' ? 'ADMIN LOGIN' : 'CASHIER LOGIN';
    usernameInput.focus();
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
    fd.append('username', username);
    fd.append('password', password);

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
                loginButton.textContent = selectedRole === 'ADMIN' ? 'ADMIN LOGIN' : 'CASHIER LOGIN';
            }
        })
        .catch(err => {
            console.error(err);
            alertUser('Network or server error', 'error');
            loginButton.disabled = false;
            loginButton.textContent = selectedRole === 'ADMIN' ? 'ADMIN LOGIN' : 'CASHIER LOGIN';
        });
}

function redirectDefault(role) {
    if (!role) return '3_index.php';
    switch (role.toLowerCase()) {
        case 'cashier': return '3_index.php';
        case 'admin': return '5_adminDashboard.php';
        default: return '3_index.php';
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


// --- GRUBHOUND INTEGRATION ---
function refreshGrubhoundToken() {
    console.log('Refreshing Grubhound Token...');
    fetch('../server/refresh_grubhound_token.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Grubhound Token Refreshed:', data.message);
                // Optional property: show a small toast if critical
            } else {
                console.warn('Grubhound Token Refresh Failed:', data.error);
            }
        })
        .catch(error => console.error('Error refreshing token:', error));
}
