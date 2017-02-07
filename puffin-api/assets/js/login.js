function onSend() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    fetch('/api/login', {
        method: 'POST',
        body: { username, password }
    })
    .then(function(response) {
        console.log('Response', response);
    })
    .catch(function(error) {
        if (error && error.message) {
            document.getElementById('error').value = error.message;
        }
    })
}