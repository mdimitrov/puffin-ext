function onEditProfileSend() {
    const username = document.getElementById('form-username').value;
    const email = document.getElementById('form-email').value;
    const errorField = document.getElementById('edit-profile-error');

    if (!username && !email) {
        errorField.innerHTML = 'At least one field should be filled';
        return;
    }

    errorField.innerHTML = '';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            const response = JSON.parse(xhttp.responseText);

            if (this.status === 200) {
                
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('POST', '/api/edit-profile', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ username, email }));
}

function onChangePasswordSend() {
    const oldPassword = document.getElementById('old-password').value;
    const newPassword = document.getElementById('new-password').value;
    const repeatNewPassword = document.getElementById('repeat-new-password').value;
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

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            const response = JSON.parse(xhttp.responseText);

            if (this.status === 200) {
                
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('POST', '/api/change-password', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ oldPassword, newPassword }));
}