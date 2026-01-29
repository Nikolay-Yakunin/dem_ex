<?php
session_start();

require_once("../pkg/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fio = trim($_POST['fio'] ?? '');
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $errors = [];

    if (empty($fio)) $errors[] = 'ФИО не может быть пустым';
    if (empty($login) || strlen($login) < 3) $errors[] = 'Логин должен быть не менее 3 символов';
    if (empty($password) || strlen($password) < 3) $errors[] = 'Пароль должен быть не менее 3 символов';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Некорректный email';
    if (empty($phone) || strlen($phone) < 10) $errors[] = 'Некорректный номер телефона';

    if (empty($errors)) {
        $login_escaped = $db->real_escape_string($login);
        $check = $db->query("SELECT id FROM user WHERE login = '" . $login_escaped . "' OR email = '" . $db->real_escape_string($email) . "'");
        
        if ($check->num_rows > 0) {
            $errors[] = 'Логин или email уже зарегистрирован';
        } else {
            $fio_escaped = $db->real_escape_string($fio);
            $password_escaped = $db->real_escape_string($password);
            $email_escaped = $db->real_escape_string($email);
            $phone_escaped = $db->real_escape_string($phone);

            $result = $db->query("INSERT INTO user (fio, login, password, email, phone, id_role) VALUES ('" . $fio_escaped . "', '" . $login_escaped . "', '" . $password_escaped . "', '" . $email_escaped . "', '" . $phone_escaped . "', 1)");
            
            if ($result) {
                header('Location: ../index.php?success=1');
                exit;
            } else {
                $errors[] = 'Ошибка при регистрации';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <style>
        :root {
            --color-primary: #007bff;
            --color-danger: #dc3545;
            --color-text: #333;
            --color-border: #ddd;
            --spacing: 16px;
            --radius: 4px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            color: var(--color-text);
            padding: var(--spacing);
        }

        .container {
            max-width: 400px;
            margin: 40px auto;
        }

        .form {
            background: white;
            padding: calc(var(--spacing) * 2);
            border-radius: var(--radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .form__title {
            margin-bottom: calc(var(--spacing) * 2);
            font-size: 24px;
            font-weight: 600;
        }

        .form__group {
            margin-bottom: var(--spacing);
        }

        .form__label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form__input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: inherit;
        }

        .form__input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
        }

        .form__button {
            width: 100%;
            padding: 10px;
            background: var(--color-primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: var(--spacing);
        }

        .form__button:hover {
            opacity: 0.9;
        }

        .form__link {
            text-align: center;
            margin-top: var(--spacing);
            font-size: 14px;
        }

        .form__link a {
            color: var(--color-primary);
            text-decoration: none;
        }

        .form__link a:hover {
            text-decoration: underline;
        }

        .alert {
            margin-bottom: var(--spacing);
            padding: 12px;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: var(--radius);
            font-size: 14px;
        }

        .alert__item {
            margin-bottom: 4px;
        }

        .alert__item:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <form class="form" method="POST">
            <h1 class="form__title">Регистрация</h1>

            <?php if (!empty($errors)): ?>
                <div class="alert">
                    <?php foreach ($errors as $error): ?>
                        <div class="alert__item"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form__group">
                <label class="form__label" for="fio">ФИО</label>
                <input 
                    class="form__input" 
                    type="text" 
                    id="fio" 
                    name="fio" 
                    value="<?= htmlspecialchars($_POST['fio'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="login">Логин</label>
                <input 
                    class="form__input" 
                    type="text" 
                    id="login" 
                    name="login" 
                    value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Пароль</label>
                <input 
                    class="form__input" 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input 
                    class="form__input" 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="phone">Телефон</label>
                <input 
                    class="form__input" 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                    required
                >
            </div>

            <button class="form__button" type="submit">Зарегистрироваться</button>

            <div class="form__link">
                Уже есть аккаунт? <a href="../index.php">Войти</a>
            </div>
        </form>
    </div>
</body>
</html>