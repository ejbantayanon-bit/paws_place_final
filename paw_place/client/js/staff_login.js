document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('login-form');
    if (form) form.addEventListener('submit', handleLoginSubmit);

    // Focus on username
    const usernameInput = document.getElementById('username');
    if (usernameInput) usernameInput.focus();

    // Refresh external API token on load
    refreshGrubhoundToken();
});

function handleLoginSubmit(event) {
    event.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const loginButton = document.getElementById('login-btn');

    loginButton.disabled = true;
    loginButton.textContent = 'Verifying...';

    // Build form data
    const fd = new FormData();
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
                loginButton.textContent = 'AUTHENTICATE';
            }
        })
        .catch(err => {
            console.error(err);
            alertUser('Network or server error', 'error');
            loginButton.disabled = false;
            loginButton.textContent = 'AUTHENTICATE';
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

function refreshGrubhoundToken() {
    console.log('Refreshing Grubhound Token...');
    fetch('../server/refresh_grubhound_token.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Grubhound Token Refreshed:', data.message);
            } else {
                console.warn('Grubhound Token Refresh Failed:', data.error);
            }
        })
        .catch(error => console.error('Error refreshing token:', error));
}
