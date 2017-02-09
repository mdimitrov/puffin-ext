function constructTableRow(data, index) {
    var table = document.getElementById("table");
    var row = table.insertRow(index + 1);
    var cell1 = row.insertCell(0);
    var cell2 = row.insertCell(1);
    var cell3 = row.insertCell(2);
    var cell4 = row.insertCell(3);
    var cell5 = row.insertCell(4);
    var cell6 = row.insertCell(5);
    var cell7 = row.insertCell(6);
    var cell8 = row.insertCell(7);

    cell1.innerHTML = data.username;
    cell2.innerHTML = data.email;
    cell3.innerHTML = data.projectNumber;
    cell4.innerHTML = data.projectName;
    cell5.innerHTML = data.uploadDate;
    cell6.innerHTML = data.lastComment;
    cell7.innerHTML = data.isLocked ? 'Заклюен' : 'Отключен';
    cell8.innerHTML = '<img class="lock-icon" src=\'/assets/icons/' +  (data.isLocked ? 'unlock' : 'lock') + '.png\'>';
}

function onPageLoad() {
    console.log('TRUGVAM');
    httpRequest({
        'url': '/api/admin/users',
        'method': 'GET'
    })
        .then(function(response) {

        })
        .catch(function(error) {
            setTimeout(() =>{
                const data = [
                    {
                        username: 'Nasko Picha',
                        email: 'nasko123@abv.bg',
                        projectNumber: 12,
                        projectName: 'Pichovski proekt',
                        uploadDate: new Date(),
                        lastComment: 'Ne pip che plqs',
                        isLocked: false
                    },
                    {
                        username: 'Misho Toploto',
                        email: 'mishakamadafaka@abv.bg',
                        projectNumber: 13,
                        projectName: 'Tup proekt',
                        uploadDate: new Date(),
                        lastComment: 'Help me',
                        isLocked: true
                    },
                    {
                        username: 'Rado Mastifa',
                        email: 'radkapiratka@abv.bg',
                        projectNumber: 14,
                        projectName: 'Smotan proekt',
                        uploadDate: new Date(),
                        lastComment: 'Kvo e tva be',
                        isLocked: false
                    }
                ];

                data.forEach(constructTableRow);
            }, 2000);
        });
}

window.onload = onPageLoad;