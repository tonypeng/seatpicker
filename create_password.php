<?php
$pass = $_GET['q'];

require_once __DIR__.'/core/password_compat.php';

$hashed = password_hash($pass, PASSWORD_DEFAULT);

echo $pass.'->'.$hashed;