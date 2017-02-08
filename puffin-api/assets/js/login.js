function onSend() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    document.getElementById('error').innerHTML = '';

    httpRequest({
        url: '/api/login',
        method: 'POST',
        body: { username, password }
    })
        .then(function(response) {
            if (!response.ok) {
                if (response.message) {
                    document.getElementById('error').innerHTML = response.message;
                }
            } else {
                console.log(response.user, ' logged successfully')
            }
        })
        .catch(function(error) {
            console.log(error);
        })
}