<?php
    namespace Config\Database;

    class DBConfig{
        public static $databaseName ='inzynierska';

        public static $hostname ='localhost';
        public static $databaseType= 'mysql';
        public static $port='3306';
        public static $user = 'root';
        public static $password = '';

        //tabele
        public static $tabelaProducent = 'Producent';
        public static $tabelaPlec = 'Plec';
        public static $tabelaSnowboardUstawienie ='SnowboardUstawienie';
        public static $tabelaSnowboardPrzeznaczenie = 'SnowboardPrzeznaczenie';
        public static $tabelaSnowboardOpcje = 'SnowboardOpcje';
        public static $tabelaSnowboard = 'Snowboard';
        public static $tabelaNartyPrzeznaczenie = 'NartyPrzeznaczenie';
        public static $tabelaNartyOpcje = 'NartyOpcje';
        public static $tabelaNarty = 'Narty';
        public static $tabelaButyKategoria = 'ButyKategoria';
        public static $tabelaButy = 'Buty';
        public static $tabelaKijki = 'Kijki';
        public static $tabelaSprzet = 'Sprzet';
        public static $tabelaKlient = 'Klient';
        public static $tabelaPracownik = 'Pracownik';
        public static $tabelaStatus = 'Status';
        public static $tabelaRezerwacja = 'Rezerwacja';
        public static $tabelaWypozyczenie = 'Wypozyczenie';
        public static $tabelaCzynnoscTyp = 'CzynnoscTyp';
        public static $tabelaCzynnoscSerwisowa = 'CzynnoscSerwisowa';
        public static $tabelaHistoriaSerwisowa = 'HistoriaSerwisowa';
        public static $tabelaPrzeznaczenie = "Przeznaczenie";

        public static $tabelaUzytkownik = 'Uzytkownik';
        public static $tabelaPrawo = 'Prawo';
    }
