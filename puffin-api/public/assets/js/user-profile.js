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