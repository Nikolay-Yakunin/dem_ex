# SQL IN PHP

## Connection

```php
<?php
// Переменные для подкоючения
$servername = "localhost";
// Пишите свои данные
$username = "dem_user";
$password = "secret";
$dbname = "dem_ex";

// Переменная, которую будем импортировать
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка на ошиюки подключения к бд
if ($conn->connect_error) {
  die('Connection failed: ' . $conn->connect_error);
}

// ОПЦИОНАЛЬНО: Установка чарсета
$conn->set_charset('utf8mb4');
?>
```

## Импорт в другой файл

```php
// пишите require_once и после путь к файлу
require_once __DIR__ . '/pkg/connection.php';

// Теперь можно использовать созданную нами переменную
$conn
```

## Как получить данные?

Будем использовать самую простую конкатенацию, никакой зашиты от SQL инекций не будет)

```php


```
