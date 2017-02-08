<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                background-color: #f4f4f4;
            }
            .login-form-wrapper {
                margin: auto;
                width: 400px;
                padding: 10px;
                border-radius: 10px;
                background: #f4f4f4;
                box-shadow: 0px 3px 0px 0px #d4d4d4;
                border: 1px solid #d4d4d4;
                margin: auto;
                margin-top: 100px;
            }
            .header {
                margin: 20px 0;
                font-size: 24px;
                text-align: center;
                font-weight: bold;
            }
            .input-label {
                margin: 15px 0;
                font-size: 19px;
                text-align: center;
            }
            .login-button {
                flex: 1;
                margin: 20px 0;
                border-radius: 3px;
                padding: 3px 7px;
                text-decoration: none;
                color: #fff;
                background-color: #2ecc71;
                box-shadow: 0px 3px 0px 0px #15B358;
                font-size: 19px;
                text-align: center;
                cursor: pointer;
            }
            input {
                width: 98%;
                height: 25px;
                margin: 0;
                margin-bottom: 10px;
            }
        </style>
        <script src="/public/assets/js/login.js"></script>
    </head>
    <body>
        <h1><?= $userId ?></h1>
        <div class="login-form-wrapper">
            <div class="header">Влезте в своя профил</div>
            <div class="input-label">Потребител:</div>
            <input id="username" type="text" name="username">
            <div class="input-label">Парола:</div>
            <input id="password" type="password" name="password">
            <div id="error" class="error"></div>
            <div class="login-button" onclick="onSend()">Вход</div>
        </div>
    </body>
</html>