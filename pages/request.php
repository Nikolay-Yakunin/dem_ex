<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit;
}

require_once("../pkg/connection.php");

$services = $db->query("SELECT id, name FROM services");
$oplaias = $db->query("SELECT id, name FROM oplata");

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $service_id = (int) ($_POST['service_id'] ?? 0);
  $date = trim($_POST['date'] ?? '');
  $time = trim($_POST['time'] ?? '');
  $datetime = $date . ' ' . $time . ':00';
  $adress = trim($_POST['adress'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $oplata_id = (int) ($_POST['oplata_id'] ?? 0);

  if ($service_id === 0)
    $errors[] = 'Выберите услугу';
  if (empty($datetime))
    $errors[] = 'Укажите дату и время';
  if (empty($adress))
    $errors[] = 'Укажите адрес';
  if (empty($phone) || strlen($phone) < 10)
    $errors[] = 'Укажите корректный номер';
  if ($oplata_id === 0)
    $errors[] = 'Выберите способ оплаты';

  if (empty($errors)) {
    $user_id = (int) $_SESSION['user_id'];
    $adress_escaped = $db->real_escape_string($adress);
    $phone_escaped = $db->real_escape_string($phone);
    $datetime_escaped = $db->real_escape_string($datetime);

    $result = $db->query("
            INSERT INTO request (id_user, id_services, id_status, id_oplata, datetime, adress, user_phone) 
            VALUES (" . $user_id . ", " . $service_id . ", 1, " . $oplata_id . ", '" . $datetime_escaped . "', '" . $adress_escaped . "', '" . $phone_escaped . "')
        ");

    if ($result) {
      $success = true;
      header('Location: requests.php?success=1');
      exit;
    } else {
      $errors[] = 'Ошибка при создании заявки';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Создать заявку</title>
  <style>
    :root {
      --color-primary: #007bff;
      --color-text: #333;
      --color-border: #ddd;
      --color-bg: #f5f5f5;
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
      background: var(--color-bg);
      color: var(--color-text);
      padding: var(--spacing);
    }

    .container {
      max-width: 500px;
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

    .form__input,
    .form__select {
      width: 100%;
      padding: 10px;
      border: 1px solid var(--color-border);
      border-radius: var(--radius);
      font-size: 14px;
      font-family: inherit;
    }

    .form__input:focus,
    .form__select:focus {
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
      <h1 class="form__title">Создать заявку</h1>

      <?php if (!empty($errors)): ?>
        <div class="alert">
          <?php foreach ($errors as $error): ?>
            <div class="alert__item">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="form__group">
        <label class="form__label" for="service">Услуга</label>
        <select class="form__select" id="service" name="service_id" required>
          <option value="">Выберите услугу</option>
          <?php while ($service = $services->fetch_assoc()): ?>
            <option value="<?= $service['id'] ?>">
              <?= htmlspecialchars($service['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form__group">
        <label class="form__label" for="date">Дата</label>
        <input class="form__input" type="date" id="date" name="date"
          value="<?= htmlspecialchars($_POST['date'] ?? '') ?>" required>
      </div>

      <div class="form__group">
        <label class="form__label" for="time">Время</label>
        <input class="form__input" type="time" id="time" name="time"
          value="<?= htmlspecialchars($_POST['time'] ?? '') ?>" required>
      </div>

      <div class="form__group">
        <label class="form__label" for="adress">Адрес</label>
        <input class="form__input" type="text" id="adress" name="adress"
          value="<?= htmlspecialchars($_POST['adress'] ?? '') ?>" placeholder="Введите адрес" required>
      </div>

      <div class="form__group">
        <label class="form__label" for="phone">Телефон</label>
        <input class="form__input" type="tel" id="phone" name="phone"
          value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" placeholder="+7 (XXX) XXX-XX-XX" required>
      </div>

      <div class="form__group">
        <label class="form__label" for="oplata">Способ оплаты</label>
        <select class="form__select" id="oplata" name="oplata_id" required>
          <option value="">Выберите способ</option>
          <?php $oplaias->data_seek(0); ?>
          <?php while ($oplata = $oplaias->fetch_assoc()): ?>
            <option value="<?= $oplata['id'] ?>">
              <?= htmlspecialchars($oplata['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <button class="form__button" type="submit">Создать заявку</button>

      <div class="form__link">
        <a href="requests.php">← Вернуться к заявкам</a>
      </div>
    </form>
  </div>
</body>

</html>