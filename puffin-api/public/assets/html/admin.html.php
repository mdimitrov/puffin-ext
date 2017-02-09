<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                background-color: #f4f4f4;
            }
            .page-header {
                text-align: center;
                font-size: 50px;
            }
            table {
                background: white;
                border-radius: 7px;
                border-collapse: collapse;
                /* height: 320px; */
                margin: auto;
                margin-top: 20px;
                /* max-width: 600px; */
                padding: 5px;
                width: 100%;
                box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
                animation: float 5s infinite;
                font-family: "Roboto", helvetica, arial, sans-serif;
            }
            tr {
                color: #666B85;
                font-size: 16px;
                font-weight: normal;
                text-shadow: 0 1px 1px rgba(255, 255, 255, 0.1);
            }
            th {
                color: #D5DDE5;
                background: #1b1e24;
                border-bottom: 4px solid #9ea7af;
                border-right: 1px solid #343a45;
                padding: 24px;
                text-align: center;
                text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
                vertical-align: middle;
            }
            td {
                background: #FFFFFF;
                padding: 20px;
                text-align: center;
                vertical-align: middle;
                text-shadow: -1px -1px 1px rgba(0, 0, 0, 0.1);
                border-right: 1px solid #C1C3D1;
            }
            tr:nth-child(odd) td {
                background: #EBEBEB;
            }
            .lock-icon {
                cursor: pointer;
            }
        </style>
         <script src="/assets/js/utils.js"></script>
        <script src="/assets/js/admin.js"></script>
    </head>
    <body>
        <div class="page-header">Администратор</h1>
        <table id="table">
            <thead>
                <tr>
                    <th>Потребител</th>
                    <th>Email</th>
                    <th>Номер на проект</th>
                    <th>Име на проект</th>
                    <th>Качен на</th>
                    <th>Последен коментар</th>
                    <th>Статус на проекта</th>
                    <th>Заключи/отключи проект</th>
                </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </body>
</html>