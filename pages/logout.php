<?php
session_start();
session_destroy();

$root = dirname(dirname(__FILE__));
header('Location: ' . $root . '/index.php');
exit;