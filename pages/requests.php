<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit;
}

require_once("../pkg/connection.php");

$user_id = (int) $_SESSION['user_id'];

$requests = $db->query("
    SELECT 
        r.id, 
        s.name as service, 
        st.name as status, 
        r.datetime, 
        r.adress
    FROM request r
    JOIN services s ON r.id_services = s.id
    JOIN status st ON r.id_status = st.id
    WHERE r.id_user = " . $user_id . "
    ORDER BY r.datetime DESC
");
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Мои заявки</title>
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
      max-width: 1000px;
      margin: 0 auto;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: calc(var(--spacing) * 2);
      background: white;
      padding: var(--spacing);
      border-radius: var(--radius);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .header__title {
      font-size: 24px;
      font-weight: 600;
    }

    .header__actions {
      display: flex;
      gap: var(--spacing);
    }

    .header__link {
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 500;
    }

    .header__link:hover {
      text-decoration: underline;
    }

    .button {
      padding: 8px 16px;
      background: var(--color-primary);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }

    .button:hover {
      opacity: 0.9;
    }

    .table {
      background: white;
      border-collapse: collapse;
      width: 100%;
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table th {
      background: #f8f9fa;
      padding: var(--spacing);
      text-align: left;
      font-weight: 600;
      border-bottom: 1px solid var(--color-border);
    }

    .table td {
      padding: var(--spacing);
      border-bottom: 1px solid var(--color-border);
    }

    .table tr:last-child td {
      border-bottom: none;
    }

    .empty {
      text-align: center;
      padding: calc(var(--spacing) * 3);
      color: #999;
    }

    .logout {
      margin-left: auto;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1 class="header__title">Мои заявки</h1>
      <div class="header__actions">
        <a class="button" href="request.php">Создать заявку</a>
        <a class="header__link logout" href="logout.php">Выход</a>
      </div>
    </div>

    <?php if ($requests->num_rows > 0): ?>
      <table class="table">
        <thead>
          <tr>
            <th>Услуга</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Адрес</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($request = $requests->fetch_assoc()): ?>
            <tr>
              <td>
                <?= htmlspecialchars($request['service']) ?>
              </td>
              <td>
                <?= htmlspecialchars($request['status']) ?>
              </td>
              <td>
                <?= htmlspecialchars($request['datetime']) ?>
              </td>
              <td>
                <?= htmlspecialchars($request['adress']) ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty">
        <p>У вас нет заявок</p>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>