function onEditProfileSend() {
    const username = document.getElementById('form-username').value;
    const email = document.getElementById('form-email').value;
    const errorField = document.getElementById('edit-profile-error');

    if (!username && !email) {
        errorField.innerHTML = 'At least one field should be filled';
        return;
    }

    errorField.innerHTML = '';

    httpRequest({
        url: '/api/edit-profile',
        method: 'POST',
        body: { username, email }
    })
        .then(function(response) {
            if (!response.ok) {
                 errorField.innerHTML = response.message || 'Error';
            } else {
                console.log(response.username, response.email, ' edit successfully')
            }
        })
        .catch(function(error) {
            console.log(error);
        })
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

    httpRequest({
        url: '/api/change-password',
        method: 'POST',
        body: { oldPassword, newPassword }
    })
        .then(function(response) {
            if (!response.ok) {
                 errorField.innerHTML = response.message || 'Error';
            } else {
                console.log('change password successfully')
            }
        })
        .catch(function(error) {
            console.log(error);
        })
}