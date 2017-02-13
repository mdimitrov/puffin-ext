function onSend() {
    const emailInput = document.getElementById('email');
    const email = emailInput.value;
    const errorField = document.getElementById('error');

    if (!email) {
        errorField.innerHTML = 'You must fill the field!';
        return;
    }

    errorField.innerHTML = '';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            emailInput.value = '';

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                return;
            }

            if (this.status === 200) {
                errorField.innerHTML = 'An email has been sent!';
                errorField.className += ' success';
            } else {
                errorField.innerHTML = response.message || 'Error';
            }
        }
    };

    xhttp.open('POST', '/api/users/password-reset', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ email }));
}