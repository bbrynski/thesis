<?php
    namespace Config\Database;
    use \PDO;

    class DBErrorName{
        public static $connection = 'Błąd połączenia z bazą danych';
        public static $create_table = "Błąd tworzenia tabeli: ";
        public static $delete_table = "Błąd usuwania tabeli: ";
        public static $query = "Błąd zapytania do bazy danych!";
        public static $noadd = "Nie udało dodać się rekordu do bazy!";
        public static $nomatch = "Nie dopasowano rekordu w bazie!";
        public static $empty = "Podana wartość nie może być pusta!";
    }
