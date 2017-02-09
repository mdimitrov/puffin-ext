<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                background-color: #f4f4f4;
            }
            .forms-wrapper {
                display: flex;
            }
            .form-wrapper {
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
            .form-header {
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
            .button {
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
            .error {
                text-align: center;
                margin-top: 10px;
                height: 20px;
                color: red;
            }
            .error.success {
                color: green;
            }
            input {
                width: 98%;
                height: 25px;
                margin: 0;
                margin-bottom: 10px;
            }
        </style>
        <script src="/assets/js/utils.js"></script>
        <script src="/assets/js/user-profile.js"></script>
    </head>
    <body>
        <h1>Здравей, <?= $username !== null ? $username : 'Анонимен' ?></h1>
        <div class="forms-wrapper">
            <div class="form-wrapper">
                <div class="form-header">Променете своите данни</div>
                <div class="input-label">Потребителско име:</div>
                <input id="form-username" type="text" name="username">
                <div class="input-label">Email:</div>
                <input id="form-email" type="text" name="email">
                <div id="edit-profile-error" class="error"></div>
                <div class="button" onclick="onEditProfileSend()">Запази</div>
            </div>
            <div class="form-wrapper">
                <div class="form-header">Променете своята парола</div>
                <div class="input-label">Текуща парола:</div>
                <input id="old-password" type="password" name="password">
                <div class="input-label">Нова парола:</div>
                <input id="new-password" type="password" name="password">
                <div class="input-label">Потвърди новата парола:</div>
                <input id="repeat-new-password" type="password" name="password">
                <div id="change-password-error" class="error"></div>
                <div class="button" onclick="onChangePasswordSend()">Запази</div>
            </div>
        </div>
    </body>
</html>