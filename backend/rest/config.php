<?php

// Set the reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));

class Config
{
    public static function DB_NAME()
    {
        $url = getenv('JAWSDB_MARIA_URL');
        $dbparts = parse_url($url);
        return ltrim($dbparts['path'], '/');
    }
    public static function DB_PORT()
    {
        $url = getenv('JAWSDB_MARIA_URL');
        $dbparts = parse_url($url);
        return $dbparts['port'];
    }
    public static function DB_USER()
    {
        $url = getenv('JAWSDB_MARIA_URL');
        $dbparts = parse_url($url);
        return $dbparts['user'];
    }
    public static function DB_PASSWORD()
    {
        $url = getenv('JAWSDB_MARIA_URL');
        $dbparts = parse_url($url);
        return $dbparts['pass'];
    }
    public static function DB_HOST()
    {
        $url = getenv('JAWSDB_MARIA_URL');
        $dbparts = parse_url($url);
        return $dbparts['host'];
    }

    public static function JWT_SECRET()
    {
        return 'your_key_string_key';
    }
}
