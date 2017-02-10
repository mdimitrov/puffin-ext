function onSend() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorField = document.getElementById('error');

    if (!username || !password) {
        errorField.innerHTML = 'All fields must be filled!';
        return;
    }

    errorField.innerHTML = '';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            const response = JSON.parse(xhttp.responseText);

            if (this.status === 200) {
                window.location.replace('/user/' + response.username);
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('POST', '/login', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ username, password }));
}