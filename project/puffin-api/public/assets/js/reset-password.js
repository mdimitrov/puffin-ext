function onSend() {
    const id = document.getElementById('hidden-username').innerHTML;
    const code = document.getElementById('hidden-code').innerHTML;
    const newPasswordInput = document.getElementById('new-password');
    const newPassword = newPasswordInput.value;
    const repeatNewPasswordInput = document.getElementById('repeat-new-password');
    const repeatNewPassword = repeatNewPasswordInput.value;
    const errorField = document.getElementById('error');

    if (!newPassword || !repeatNewPassword) {
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

            newPasswordInput.value = '';
            repeatNewPasswordInput.value = '';

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                return;
            }

            if (this.status === 200) {
                window.location.replace('/login');
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('POST', '/api/users/' + id + '/_reset_password', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ newPassword, code }));
}