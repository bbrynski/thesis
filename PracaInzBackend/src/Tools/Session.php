<?php
namespace Tools;

class Session{
    private static $regenerateTime = 6000;
    private static $regenrateRequest = 10;

    private static $active = 'active';
    private static $ip = 'ip';
    private static $startTime = 'time';
    private static $reqCount = 'request';

    //rozpoczęcie lub odtworzenie sesji
    public static function start() {
        session_start();
    }
    //rozpoczęcie lub odtworzenie sesji
    public static function initialize() {
        self::start();
        if(self::is(self::$active) === true){
            self::set(self::$reqCount, self::get(self::$reqCount) + 1);
            self::check();
        }
        else {
            self::set(self::$active, true);
            self::set(self::$ip, $_SERVER['REMOTE_ADDR']);//'192.168.1.1'
            self::set(self::$startTime, time());
            self::set(self::$reqCount, 1);
        }
    }
    //sprawdzenie stanu sesji
    public static function check(){
        $error = false;
        if(self::get(self::$ip) !== $_SERVER['REMOTE_ADDR'])
            $error = true;
        //inne ewentualne testy
        if($error === true){
            self::destroy();
            //die('Proba przejecia sesji!');
            return false;
        }

        $now = time();
        if ($now > self::get(self::$startTime) + self::$regenerateTime
            || self::get(self::$reqCount) > self::$regenrateRequest)
        {
            self::regenerate();
        }
        return true;
    }
    //ustawienie zmiennej sesyjnej
    public static function set($name, $value) {
        $_SESSION[$name] = $value;
    }
    //pobranie zmiennej sesyjnej
    public static function get($name) {
        if(self::is($name))
            return $_SESSION[$name];
        else
            return null;
    }
    //sprawdzenie czy zmienna sesyjna jest ustawiona
    public static function is($name) {
        return isset($_SESSION[$name]);
    }
    //wyczyszczenie zmiennej sesyjnej
    public static function clear($name) {
        unset($_SESSION[$name]);
    }
    //wyczyszczenie	zmiennych w sesji
    public static function clearAll() {
        $_SESSION = array();

    }
    //zniszczenie sesji
    public static function destroy() {
        self::clearAll();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();
    }
    //nadanie nazwy sesji, domyślnie jest to PHPSESSID
    public static function name($name=null) {
        if($name !== null)
            return session_name($name);
        else
            return session_name();
    }
    //zmiana identyfikatora sesji
    public static function regenerate() {
        session_regenerate_id();
        self::set(self::$startTime, time());
        self::set(self::$reqCount, 1);
    }
}