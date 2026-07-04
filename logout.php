<?php
require_once __DIR__ . '/auth_helpers.php';
logout();
header('Location: login.php');
exit;
