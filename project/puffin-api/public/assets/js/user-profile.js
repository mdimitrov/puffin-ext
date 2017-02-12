function onPageLoad() {
    setTimeout(function() {
        const username = document.getElementById('hidden-username').innerHTML;
        const fullName = document.getElementById('hidden-fullName').innerHTML;
        const email = document.getElementById('hidden-email').innerHTML;
        const role = document.getElementById('hidden-role').innerHTML;

        document.getElementById('username-wrapper').innerHTML = '<b>Потребител:</b> ' + username;
        document.getElementById('fullName-wrapper').innerHTML = '<b>Име:</b> ' + fullName;
        document.getElementById('email-wrapper').innerHTML = '<b>Email:</b> ' + email;

        if (role === 'admin') {
            const adminButton = document.createElement('A');
            adminButton.href = '/admin/projects';
            adminButton.className = 'button admin-button';
            adminButton.innerHTML = 'Go to admin panel';
            document.getElementById('admin-button-wrapper').appendChild(adminButton);
        }
    }, 50);
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
    const username = document.getElementById('hidden-username').innerHTML;
    const id = document.getElementById('hidden-id').innerHTML;

    const currentFullNameDOM = document.getElementById('hidden-fullName');
    const currentFullName = currentFullNameDOM.innerHTML;
    const currentEmailDOM = document.getElementById('hidden-email');
    const currentEmail = currentEmailDOM.innerHTML;
    const fullNameInput = document.getElementById('form-fullName');
    const fullName = fullNameInput.value || currentFullName;
    const emailInput = document.getElementById('form-email');
    const email = emailInput.value || currentEmail;
    const errorField = document.getElementById('edit-profile-error');

    if (!fullName && !email) {
        errorField.innerHTML = 'At least one field should be filled';
        return;
    }

    errorField.innerHTML = '';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            fullNameInput.value = '';
            emailInput.value = '';

            try {
                response = JSON.parse(xhttp.responseText);
            } catch(e) {
                console.log(e);
            }

            if (this.status === 200) {
                const newfullName = response.data.fullName;
                const newEmail = response.data.email;

                fullNameInput.innerHTML = newfullName;
                currentEmailDOM.innderHTML = newEmail;
                document.getElementById('username-wrapper').innerHTML = '<b>Потребител:</b> ' + username;
                document.getElementById('fullName-wrapper').innerHTML = '<b>Име:</b> ' + newfullName;
                document.getElementById('email-wrapper').innerHTML = '<b>Email:</b> ' + newEmail;
                errorField.innerHTML = 'Success!';
                errorField.className += ' success';
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('PUT', '/api/users/' + id , true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ action: 'updateInfo', data: { fullName, email } }));
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

    const id = document.getElementById('hidden-id').innerHTML;

    xhttp.open('PUT', '/api/users/' + id, true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ action: 'updatePassword', data: { oldPassword, newPassword } }));
}

window.onload = onPageLoad();