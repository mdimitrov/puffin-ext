function onSend() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    document.getElementById('error').innerHTML = '';

    fetch('/api/login', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    }).then(function (response) {
        return response.json();
    })
    .then(function(response) {
        if (response.ok) {
            console.log(response.user, ' logged successfully')
        } else {
            document.getElementById('error').innerHTML = response.message;
        }
    })
    .catch(function(error) {
        console.error(error);
    })
}