const roleToStringMapping = {
    'admin': 'Администратор',
    'user': 'Потребител'
};

const roleToButtonStringMapping = {
    'admin': 'Премахни админ',
    'user': 'Направи админ'
}

function constructTableRow(data, index) {
    const table = document.getElementById("table");
    const row = table.insertRow(index + 1);
    const cell1 = row.insertCell(0);
    const cell2 = row.insertCell(1);
    const cell3 = row.insertCell(2);
    const cell4 = row.insertCell(3);
    const cell5 = row.insertCell(4);
    const cell6 = row.insertCell(5);
    const cell7 = row.insertCell(6);
    const cell8 = row.insertCell(7);
    const cell9 = row.insertCell(8);

    const id = data.id;

    cell1.innerHTML = '<a href="/users/' + data.username + '">' + data.username + '</a>';
    cell2.innerHTML = data.fullName;
    cell3.innerHTML = data.email;
    cell4.innerHTML = data.topic;
    cell5.innerHTML = roleToStringMapping[data.role];
    cell5.id = 'user-role-' + id;
    cell5.setAttribute('role', data.role);
    cell6.innerHTML = data.isBlocked ? 'Блокиран' : 'Нормален';
    cell6.id = 'user-status-' + id;
    cell6.setAttribute('status', data.isBlocked);
    cell7.innerHTML = '<div class="button ' + (data.isBlocked ? 'unblock' : 'block') + '" onclick=onBlockClick(' + id + ')>' + (data.isBlocked ? 'Отблокирай' : 'Блокирай') + '</div>';
    cell7.id = 'user-status-button-' + id;
    cell8.innerHTML = '<div class="button role ' + data.role + '" onclick=onPromoteClick(' + id + ')>' + roleToButtonStringMapping[data.role] + '</div>';
    cell8.id = 'user-role-button-' + id;
    cell9.id = 'email-reset-' + id;
    cell9.setAttribute('email', data.email);
    cell9.innerHTML = '<div class="button reset" onclick=onResetPasswordClick(' + id + ')>Рестартирай парола</div>';
}

function onPageLoad() {
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                console.error(e);
            }

            if (this.status === 200) {
                response.data.forEach(constructTableRow);
            }
        }
    };

    xhttp.open('GET', '/api/users' , true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send();
}

function onBlockClick(id) {
    const statusCell = document.getElementById('user-status-' + id);
    const statusButtonCell = document.getElementById('user-status-button-' + id);
    const status = statusCell.getAttribute('status');
    const newStatus = !(status === 'true');

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                console.error(e);
            }

            if (this.status === 200) {
                statusCell.innerHTML = response.data.isBlocked ? 'Блокиран' : 'Нормален';
                statusCell.setAttribute('status', response.data.isBlocked);
                statusButtonCell.innerHTML = '<div class="button ' + (response.data.isBlocked ? 'unblock' : 'block') + '" onclick=onBlockClick(' + id + ')>' + (response.data.isBlocked ? 'Отблокирай' : 'Блокирай') + '</div>';
            }
        }
    };

    xhttp.open('PUT', '/api/users/' + id, true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ action: 'updateBlocked', data: { isBlocked: newStatus } }));
}

function onPromoteClick(id) {
    const roleCell = document.getElementById('user-role-' + id);
    const roleButtonCell = document.getElementById('user-role-button-' + id);
    const role = roleCell.getAttribute('role');
    const newRole = role === 'admin' ? 'user' : 'admin';

    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                console.error(e);
            }

            if (this.status === 200) {
                roleCell.innerHTML = roleToStringMapping[response.data.role];
                roleCell.setAttribute('role', response.data.role);
                roleButtonCell.innerHTML = '<div class="button role ' + response.data.role + '" onclick=onPromoteClick(' + id + ')>' + roleToButtonStringMapping[response.data.role] + '</div>';
            }
        }
    };

    xhttp.open('PUT', '/api/users/' + id, true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ action: 'updateRole', data: { role: newRole } }));
}

function onResetPasswordClick(id) {
    const email = document.getElementById('email-reset-' + id).getAttribute('email');
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                return;
            }

            if (this.status === 200) {
            } else {
            }
        }
    };

    xhttp.open('POST', '/api/users/password-reset', true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ email }));
}

function onSearchClick() {
    const searchString = document.getElementById('search-input').value;
    const requestUrl = searchString ? '/api/search/users?q=' + searchString  : '/api/users';

   const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
        if (this.readyState === 4) {
            var response = {};

            try {
                response = JSON.parse(xhttp.responseText);
            } catch (e) {
                return;
            }

            if (this.status === 200) {
                const table = document.getElementById('table');
                while(table.rows.length > 1) {
                    table.deleteRow(1);
                }
                response.data.forEach(constructTableRow);
            } else {
            }
        }
    };

    xhttp.open('GET', requestUrl, true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send();
}

window.onload = onPageLoad;