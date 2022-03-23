<?php
namespace Tools;

class Access extends Session {
    private static $login = 'user';
    private static $loginTime = 'logintime';
    private static $sessionTime = 6000;

    //zaloguj
    public static function login($login) {
        //sprawdzenie istniejącej sesji
        if(parent::check() === true)
        {
            //zmieniając poziom dostępu regenrerujemy sesję
            parent::regenerate();
            parent::set(self::$login, $login);
            parent::set(self::$loginTime, time());
        }
    }
    //wyloguj
    public static function logout() {
        parent::clear(self::$login);
        parent::clear(self::$loginTime);
        parent::regenerate();
    }
    //sprawdź czy jest zalogowany
    public static function islogin() {
        if(parent::is(self::$login) === true) {

            if(time() > parent::get(self::$loginTime) + self::$sessionTime) {
                //przekroczono czas sesji, wyloguj
                self::logout();
                return false;
            }
            parent::set(self::$loginTime, time());
            return true;
        }
        return false;
    }
}