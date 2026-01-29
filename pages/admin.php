<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
  header('Location: ../index.php');
  exit;
}

require_once("../pkg/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['status_id'])) {
  $request_id = (int) $_POST['request_id'];
  $status_id = (int) $_POST['status_id'];

  $db->query("UPDATE request SET id_status = " . $status_id . " WHERE id = " . $request_id);
}

$requests = $db->query("
    SELECT 
        r.id, 
        u.fio, 
        s.name as service, 
        st.name as status, 
        r.datetime, 
        r.adress
    FROM request r
    JOIN user u ON r.id_user = u.id
    JOIN services s ON r.id_services = s.id
    JOIN status st ON r.id_status = st.id
    ORDER BY r.datetime DESC
");

$statuses = $db->query("SELECT id, name FROM status");
$statusMap = [];
while ($status = $statuses->fetch_assoc()) {
  $statusMap[$status['id']] = $status['name'];
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Администратор</title>
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

    .header__link {
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 500;
    }

    .header__link:hover {
      text-decoration: underline;
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

    .form-inline {
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .select {
      padding: 6px;
      border: 1px solid var(--color-border);
      border-radius: var(--radius);
      font-size: 14px;
      font-family: inherit;
    }

    .button {
      padding: 6px 12px;
      background: var(--color-primary);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
    }

    .button:hover {
      opacity: 0.9;
    }

    .empty {
      text-align: center;
      padding: calc(var(--spacing) * 3);
      color: #999;
    }
  </style>
</head>

<body>
  <div class="container">

    <div class="header">
      <div class="header__actions">
        <a class="header__link logout" href="logout.php">Выход</a>
      </div>
    </div>

    <?php if ($requests->num_rows > 0): ?>
      <table class="table">
        <thead>
          <tr>
            <th>Клиент</th>
            <th>Услуга</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Адрес</th>
            <th>Действие</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($request = $requests->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($request['fio']) ?></td>
              <td><?= htmlspecialchars($request['service']) ?></td>
              <td><?= htmlspecialchars($request['status']) ?></td>
              <td><?= htmlspecialchars($request['datetime']) ?></td>
              <td><?= htmlspecialchars($request['adress']) ?></td>
              <td>
                <form class="form-inline" method="POST">
                  <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                  <select class="select" name="status_id">
                    <?php foreach ($statusMap as $id => $name): ?>
                      <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button class="button" type="submit">Изменить</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty">
        <p>Заявок нет</p>
      </div>
    <?php endif; ?>
  </div>
</body>

</html>