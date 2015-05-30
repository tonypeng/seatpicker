<?php
require_once __DIR__.'/access_guard.php';

require_once __DIR__.'/password_compat.php';

require_once __DIR__.'/config.php';

require_once __DIR__.'/globals.php';

require_once __DIR__.'/Request.php';
require_once __DIR__.'/Response.php';
require_once __DIR__.'/RedirectException.php';
require_once __DIR__.'/Session.php';
require_once __DIR__.'/ISessionDriver.php';
require_once __DIR__.'/DatabaseBackedSessionDriver.php';
require_once __DIR__.'/SessionManager.php';
require_once __DIR__.'/../controllers/Controller.php';
require_once __DIR__.'/../controllers/SiteController.php';
require_once __DIR__.'/../components/Component.php';
require_once __DIR__.'/model/Model.php';
require_once __DIR__.'/model/functions.php';

define('GRAD_INCLUDED', 1);

/* Section: Database */
require_once __DIR__.'/db/pdo/Database.php';

# Database host #
$db_host = 'localhost';
# Database username #
$db_username = 'root';
# Matching password for the username above. #
$db_password = '';
# Name of the database to use #
$db_database = 'gradseating';

Database::init($db_host, $db_database, $db_username, $db_password);

function db() {
    return Database::get();
}