<?php
if(!defined('GRAD_PAGE'))
    die('Access Denied'); // nope

if(!defined('GRAD_INCLUDED'))
    die();

class Database {
    private static $DB;
    private static $inited = false;

    public static function init($host, $database, $username, $password) {
        self::$DB = new PDO("mysql:host=" . $host . ";dbname=" . $database, $username, $password);
        self::$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$inited = true;
    }

    /**
     * @return PDO
     * @throws Exception
     */
    public static function get() {
        if (!self::$inited) {
            throw new Exception('Database was not initialized.');
        }

        return self::$DB;
    }
}