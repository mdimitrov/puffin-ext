function onSend() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorField = document.getElementById('error');

    if (!username || !password) {
        errorField.innerHTML = 'All fields must be filled!';
        return;
    }

    errorField.innerHTML = '';

    httpRequest({
        url: '/api/login',
        method: 'POST',
        body: { username, password }
    })
        .then(function(response) {
            if (!response.ok) {
                errorField.innerHTML = response.message || 'Error';
            } else {
                window.location.replace('/user/profile');
            }
        })
        .catch(function(error) {
            console.log(error);
        })
}