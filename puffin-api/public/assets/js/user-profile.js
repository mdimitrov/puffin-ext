function onPageLoad() {
    setTimeout(function() {
        const username = document.getElementById('initial-username').innerHTML;
        const email = document.getElementById('initial-email').innerHTML;
        const role = document.getElementById('initial-role').innerHTML;

        document.getElementById('username-wrapper').innerHTML = '<b>Потребител:</b> ' + username;
        document.getElementById('email-wrapper').innerHTML = '<b>Email:</b> ' + email;

        if (role === 'admin') {
            const adminButton = document.createElement('A');
            adminButton.href = '/admin';
            adminButton.className = 'button admin-button';
            adminButton.innerHTML = 'Go to admin panel';
            document.getElementById('admin-button-wrapper').appendChild(adminButton);
        }
    }, 200);
}

function onLogoutClick() {
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            try {
                response = JSON.parse(xhttp.responseText);
            } catch(e) {
                console.log(e);
            }

            if (this.status === 200) {
                window.location.replace('/login');
            } else {
                console.log(response);
            }
        }
    };

    xhttp.open('GET', '/logout', true);
    xhttp.send();
}

function onEditProfileSend() {
    const usernameInput = document.getElementById('form-username')
    const username = usernameInput.value;
    const emailInput = document.getElementById('form-email');
    const email = emailInput.value;
    const errorField = document.getElementById('edit-profile-error');

    if (!username && !email) {
        errorField.innerHTML = 'At least one field should be filled';
        return;
    }

    errorField.innerHTML = '';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            usernameInput.value = '';
            emailInput.value = '';

            try {
                response = JSON.parse(xhttp.responseText);
            } catch(e) {
                console.log(e);
            }

            if (this.status === 200) {
                errorField.innerHTML = 'Success!';
                errorField.className += ' success';
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('PUT', '/user/edit', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ username, email }));
}

function onChangePasswordSend() {
    const oldPasswordInput = document.getElementById('old-password');
    const oldPassword = oldPasswordInput.value;
    const newPasswordInput = document.getElementById('new-password');
    const newPassword = newPasswordInput.value;
    const repeatNewPasswordInput = document.getElementById('repeat-new-password');
    const repeatNewPassword = repeatNewPasswordInput.value;
    const errorField = document.getElementById('change-password-error');

    if (!oldPassword || !newPassword || !repeatNewPassword) {
        errorField.innerHTML = 'You must fill all fields!';
        return;
    }

    if (newPassword !== repeatNewPassword) {
        errorField.innerHTML = 'Passwords do not match!';
        return;
    }

    errorField.innerHTML = '';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            oldPasswordInput.value = '';
            newPasswordInput.value = '';
            repeatNewPasswordInput.value = '';

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                return;
            }

            if (this.status === 200) {
                errorField.innerHTML = 'Success!';
                errorField.className += ' success';
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('PUT', '/user/password', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ oldPassword, newPassword }));
}

window.onload = onPageLoad();