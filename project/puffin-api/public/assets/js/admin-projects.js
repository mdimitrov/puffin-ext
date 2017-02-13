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

    const id = data.id;

    cell1.innerHTML = data.topic;
    cell2.innerHTML = data.dateUploaded;
    cell3.innerHTML = data.version;
    cell4.innerHTML = data.comment;
    cell5.innerHTML = '<a href="/users/' + data.username + '">' + data.username + '</a>';
    cell6.innerHTML = data.fullName;
    cell7.innerHTML = data.status === 'locked' ? 'Заклюен' : 'Отключен';
    cell7.id = 'topic-status-' + data.id;
    cell7.setAttribute('status', data.status);
    cell8.innerHTML = '<img class="lock-icon" src=\'/assets/icons/' +  (data.status === 'locked' ? 'unlock' : 'lock') + '.png\' onclick=onLockClick(' + id + ')>';
    cell8.id = 'topic-status-icon-' + data.id;
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

    xhttp.open('GET', '/api/projects?limit=5' , true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send();
}

function onLockClick(id) {
    const statusCell = document.getElementById('topic-status-' + id);
    const iconCell = document.getElementById('topic-status-icon-' + id);
    const status = statusCell.getAttribute('status');
    const newStatus = status === 'locked' ? 'unlocked' : 'locked';

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
                const data = response.data.status;
                statusCell.innerHTML = response.data.status === 'locked' ? 'Заклюен' : 'Отключен';
                statusCell.setAttribute('status', response.data.status);
                iconCell.innerHTML = '<img class="lock-icon" src=\'/assets/icons/' +  (response.data.status === 'locked' ? 'unlock' : 'lock') + '.png\' onclick=onLockClick(' + id + ')>';
            }
        }
    };

    xhttp.open('PUT', '/api/projects/' + id, true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.send(JSON.stringify({ action: 'updateStatus', data: { status: newStatus } }));
}

function onSearchClick() {
    const searchString = document.getElementById('search-input').value;
    const requestUrl = searchString ? '/api/search/projects?q=' + searchString  : '/api/projects';

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