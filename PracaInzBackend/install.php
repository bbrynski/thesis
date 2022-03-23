<?php
    require 'vendor/autoload.php';

    use Config\Database\DBConfig as DB;
    use Config\Database\DBConnection as DBConnection;

    DBConnection::setDBConnection(DB::$user,DB::$password,DB::$hostname,DB::$databaseType,DB::$port);
    try{
        $pdo = DBConnection::getHandle();
    }catch (\PDOException $e){
        echo \Config\Database\DBErrorName::$connection;
        exit(1);
    }

/* ***********************************************************************
   USUWANIE TABEL
************************************************************************** */
$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaUzytkownik;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table.DB::$tabelaUzytkownik;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaWypozyczenie;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table.DB::$tabelaWypozyczenie;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaRezerwacja;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table.DB::$tabelaRezerwacja;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaHistoriaSerwisowa;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaCzynnoscSerwisowa;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}


$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaCzynnoscTyp;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}


$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaSprzet;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table.DB::$tabelaSprzet;
}



$query = ' DROP TABLE IF EXISTS '.DB::$tabelaProducent;
    try{
        $pdo->exec($query);
    }catch(PDOException $e){
        echo \Config\Database\DBErrorName::$delete_table.DB::$tabelaProducent;
    }

$query = ' DROP TABLE IF EXISTS '.DB::$tabelaPlec;
try{
    $pdo->exec($query);
}catch(PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaSnowboard;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaSnowboardOpcje;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaSnowboardUstawienie;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaSnowboardPrzeznaczenie;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}


$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaNarty;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaNartyOpcje;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaNartyPrzeznaczenie;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaButy;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaButyKategoria;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaKijki;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}


$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaStatus;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}



$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaKlient;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}

$query = ' DROP TABLE IF EXISTS ' . DB::$tabelaPracownik;
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$delete_table;
}





/* ***********************************************************************
   TWORZENIE TABEL
************************************************************************** */

    $query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaProducent . '` (
		`' . DB\Producent::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Producent::$nazwa . '` VARCHAR(30) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
    try {
        $pdo->exec($query);
    } catch (PDOException $e) {
        echo \Config\Database\DBErrorName::$create_table . DB::$tabelaProducent;
    }

//*****************************************************************************
    $query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaPlec . '` (
		`' . DB\Plec::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Plec::$nazwa . '` VARCHAR(30) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
    try {
        $pdo->exec($query);
    } catch (PDOException $e) {
        echo \Config\Database\DBErrorName::$create_table . DB::$tabelaPlec;
    }

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaSnowboardUstawienie . '` (
		`' . DB\SnowboardUstawienie::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\SnowboardUstawienie::$nazwa . '` VARCHAR(30) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaSnowboardOpcje;
}


//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaSnowboardOpcje . '` (
		`' . DB\SnowboardOpcje::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\SnowboardOpcje::$idUstawienie . '` INT NOT NULL,
		`' . DB\SnowboardOpcje::$KatPrawa . '` INT NOT NULL,
		`' . DB\SnowboardOpcje::$KatLewa . '` INT NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\SnowboardOpcje::$idUstawienie.') REFERENCES '.DB::$tabelaSnowboardUstawienie.'('.DB\SnowboardUstawienie::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaSnowboardOpcje;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaSnowboardPrzeznaczenie . '` (
		`' . DB\SnowboardPrzeznaczenie::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\SnowboardPrzeznaczenie::$nazwa . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaSnowboardPrzeznaczenie;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaSnowboard . '` (
		`' . DB\Snowboard::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Snowboard::$model . '` VARCHAR(30) NOT NULL,
		`' . DB\Snowboard::$idPrzeznaczenie . '` INT NOT NULL,
		`' . DB\Snowboard::$dlugosc . '` FLOAT NOT NULL,
		`' . DB\Snowboard::$idOpcje . '` INT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\Snowboard::$idOpcje.') REFERENCES '.DB::$tabelaSnowboardOpcje.'('.DB\SnowboardOpcje::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Snowboard::$idPrzeznaczenie.') REFERENCES '.DB::$tabelaSnowboardPrzeznaczenie.'('.DB\SnowboardPrzeznaczenie::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaSnowboard;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaNartyOpcje . '` (
		`' . DB\NartyOpcje::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\NartyOpcje::$waga . '` FLOAT NOT NULL,
		`' . DB\NartyOpcje::$dlugoscButa .'` FLOAT NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaNartyOpcje;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaNartyPrzeznaczenie . '` (
		`' . DB\NartyPrzeznaczenie::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\NartyPrzeznaczenie::$nazwa . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaNartyPrzeznaczenie;
}


//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaNarty . '` (
		`' . DB\Narty::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Narty::$model . '` VARCHAR(50) NOT NULL,
		`' . DB\Narty::$dlugosc . '` FLOAT NOT NULL,
		`' . DB\Narty::$idPrzeznaczenie . '` INT NOT NULL,
		`' . DB\Narty::$idOpcje . '` INT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\Narty::$idOpcje.') REFERENCES '.DB::$tabelaNartyOpcje.'('.DB\NartyOpcje::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Narty::$idPrzeznaczenie.') REFERENCES '.DB::$tabelaNartyPrzeznaczenie.'('.DB\NartyPrzeznaczenie::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaNarty;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaButyKategoria . '` (
		`' . DB\ButyKategoria::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\ButyKategoria::$nazwa . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaButyKategoria;
}


//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaButy . '` (
		`' . DB\Buty::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Buty::$model . '` VARCHAR(30) NOT NULL,
		`' . DB\Buty::$idKategoria . '` INT NOT NULL,
		`' . DB\Buty::$rozmiar . '` FLOAT NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\Buty::$idKategoria.') REFERENCES '.DB::$tabelaButyKategoria.'('.DB\ButyKategoria::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaButy;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaKijki . '` (
		`' . DB\Kijki::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Kijki::$model . '` VARCHAR(40) NOT NULL,
		`' . DB\Kijki::$dlugosc . '` FLOAT NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaKijki;
}



//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaSprzet . '` (
		`' . DB\Sprzet::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Sprzet::$idSnowboard . '` INT NULL,
		`' . DB\Sprzet::$idNarty . '` INT NULL,
		`' . DB\Sprzet::$idButy . '` INT NULL,
		`' . DB\Sprzet::$idKijki . '` INT NULL,
		`' . DB\Sprzet::$idProducent . '` INT NOT NULL,
		`' . DB\Sprzet::$data . '` DATE NOT NULL,
		`' . DB\Sprzet::$idPlec . '` INT NOT NULL,
		`' . DB\Sprzet::$cena . '` FLOAT NOT NULL,
		`' . DB\Sprzet::$zwrocone . '` BIT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\Sprzet::$idSnowboard.') REFERENCES '.DB::$tabelaSnowboard.'('.DB\Snowboard::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Sprzet::$idNarty.') REFERENCES '.DB::$tabelaNarty.'('.DB\Narty::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Sprzet::$idButy.') REFERENCES '.DB::$tabelaButy.'('.DB\Buty::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Sprzet::$idKijki.') REFERENCES '.DB::$tabelaKijki.'('.DB\Kijki::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Sprzet::$idProducent.') REFERENCES '.DB::$tabelaProducent.'('.DB\Producent::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Sprzet::$idPlec.') REFERENCES '.DB::$tabelaPlec.'('.DB\Plec::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaSprzet;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaKlient . '` (
		`' . DB\Klient::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Klient::$imie . '` VARCHAR(40) NOT NULL,
		`' . DB\Klient::$nazwisko . '` VARCHAR(40) NOT NULL,
		`' . DB\Klient::$telefon . '` VARCHAR(20) NOT NULL,
		`' . DB\Klient::$email . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaKlient;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaPracownik . '` (
		`' . DB\Pracownik::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Pracownik::$imie . '` VARCHAR(40) NOT NULL,
		`' . DB\Pracownik::$nazwisko . '` VARCHAR(40) NOT NULL,
		`' . DB\Pracownik::$telefon . '` VARCHAR(20) NOT NULL,
		`' . DB\Pracownik::$email . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaPracownik;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaStatus . '` (
		`' . DB\Status::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Status::$nazwa . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaPracownik;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaRezerwacja . '` (
		`' . DB\Rezerwacja::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Rezerwacja::$idSprzet . '` INT NOT NULL,
		`' . DB\Rezerwacja::$idKlient . '` INT NOT NULL,
		`' . DB\Rezerwacja::$dataOd . '` DATE NOT NULL,
		`' . DB\Rezerwacja::$dataDo . '` DATE NOT NULL,
		`' . DB\Rezerwacja::$idStatus . '` INT NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\Rezerwacja::$idSprzet.') REFERENCES '.DB::$tabelaSprzet.'('.DB\Sprzet::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Rezerwacja::$idKlient.') REFERENCES '.DB::$tabelaKlient.'('.DB\Klient::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Rezerwacja::$idStatus.') REFERENCES '.DB::$tabelaStatus.'('.DB\Status::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaNarty;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaWypozyczenie . '` (
		`' . DB\Wypozyczenie::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Wypozyczenie::$idSprzet . '` INT NOT NULL,
		`' . DB\Wypozyczenie::$idKlient . '` INT NOT NULL,
		`' . DB\Wypozyczenie::$idPracownik . '` INT NOT NULL,
		`' . DB\Wypozyczenie::$cena . '` FLOAT NOT NULL,
		`' . DB\Wypozyczenie::$dataOd . '` DATE NOT NULL,
		`' . DB\Wypozyczenie::$dataDo . '` DATE NOT NULL,
		`' . DB\Wypozyczenie::$idStatus . '` INT NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\Wypozyczenie::$idSprzet.') REFERENCES '.DB::$tabelaSprzet.'('.DB\Sprzet::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Wypozyczenie::$idKlient.') REFERENCES '.DB::$tabelaKlient.'('.DB\Klient::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Wypozyczenie::$idPracownik.') REFERENCES '.DB::$tabelaPracownik.'('.DB\Pracownik::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\Wypozyczenie::$idStatus.') REFERENCES '.DB::$tabelaStatus.'('.DB\Status::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaWypozyczenie;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaCzynnoscTyp . '` (
		`' . DB\CzynnoscTyp::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\CzynnoscTyp::$nazwa . '` VARCHAR(40) NOT NULL,
		PRIMARY KEY (id)) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaCzynnoscTyp;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaCzynnoscSerwisowa . '` (
		`' . DB\CzynnoscSerwisowa::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\CzynnoscSerwisowa::$idCzynnosc . '` INT NOT NULL,
		`' . DB\CzynnoscSerwisowa::$data . '` DATE NOT NULL,
		`' . DB\CzynnoscSerwisowa::$opis . '` VARCHAR(300) NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\CzynnoscSerwisowa::$idCzynnosc.') REFERENCES '.DB::$tabelaCzynnoscTyp.'('.DB\CzynnoscTyp::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaCzynnoscSerwisowa;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaHistoriaSerwisowa . '` (
		`' . DB\HistoriaSerwisowa::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\HistoriaSerwisowa::$idCzynnoscSerwisowa . '` INT NOT NULL,
		`' . DB\HistoriaSerwisowa::$idSprzet . '` INT NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY ('.DB\HistoriaSerwisowa::$idCzynnoscSerwisowa.') REFERENCES '.DB::$tabelaCzynnoscSerwisowa.'('.DB\CzynnoscSerwisowa::$id.') ON DELETE CASCADE,
		FOREIGN KEY ('.DB\HistoriaSerwisowa::$idSprzet.') REFERENCES '.DB::$tabelaSprzet.'('.DB\Sprzet::$id.') ON DELETE CASCADE
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaHistoriaSerwisowa;
}

//*****************************************************************************
$query = 'CREATE TABLE IF NOT EXISTS `' . DB::$tabelaUzytkownik . '` (
		`' . DB\Uzytkownik::$id . '` INT NOT NULL AUTO_INCREMENT,
		`' . DB\Uzytkownik::$nazwa . '` VARCHAR(40) NOT NULL,
		`' . DB\Uzytkownik::$haslo . '` VARCHAR (64) NOT NULL,
		PRIMARY KEY (id)
		) ENGINE=InnoDB;';
try {
    $pdo->exec($query);
} catch (PDOException $e) {
    echo \Config\Database\DBErrorName::$create_table . DB::$tabelaUzytkownik;
}



/* ***********************************************************************
   WYPEŁNIENIE TABEL
************************************************************************** */

$plec = array();
$plec[] = 'Kobieta';
$plec[] = 'Mężczyzna';

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaPlec.'` (`'.DB\Plec::$nazwa.'`) VALUES (:plec)');
    foreach ($plec as $pozycja){
        $stmt-> bindValue(':plec',$pozycja,PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$producent = array();
$producent[] = 'Burton';
$producent[] = 'Lib Tech';
$producent[] = 'Nitro';
$producent[] = 'Ride';
$producent[] = 'Rossignol';
$producent[] = 'Atomic';
$producent[] = 'Fischer';
$producent[] = 'Uvex';
$producent[] = 'Carrera';

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaProducent.'` (`'.DB\Producent::$nazwa.'`) VALUES (:producent)');
    foreach ($producent as $pozycja){
        $stmt-> bindValue(':producent',$pozycja,PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$kijki = array();
$kijki[] = array(
    'model' => 'AMT 2018',
    'dlugosc' => '1.20'
);
$kijki[] = array(
    'model' => 'AMT 2018',
    'dlugosc' => '1.30'
);
$kijki[] = array(
    'model' => 'AMT 2018',
    'dlugosc' => '1.10'
);
$kijki[] = array(
    'model' => 'Cloud',
    'dlugosc' => '1.15'
);
$kijki[] = array(
    'model' => 'Cloud',
    'dlugosc' => '1.30'
);
$kijki[] = array(
    'model' => 'Cloud',
    'dlugosc' => '1.15'
);

$kijki[] = array(
    'model' => 'Unlimited',
    'dlugosc' => '1.25'
);

$kijki[] = array(
    'model' => 'Unlimited',
    'dlugosc' => '1.35'
);

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaKijki.'` (`'.DB\Kijki::$model.'`, `'.DB\Kijki::$dlugosc.'`) VALUES (:model, :dlugosc)');
    foreach ($kijki as $pozycja){
        $stmt-> bindValue(':model',$pozycja['model'],PDO::PARAM_STR);
        $stmt-> bindValue(':dlugosc',$pozycja['dlugosc'],PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$snowboardustawienie = array();
$snowboardustawienie[] = 'Goofy (prawa)';
$snowboardustawienie[] = 'Regular (lewa)';

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaSnowboardUstawienie.'` (`'.DB\SnowboardUstawienie::$nazwa.'`) VALUES (:ustawienie)');
    foreach ($snowboardustawienie as $pozycja){
        $stmt-> bindValue(':ustawienie',$pozycja,PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$snowboardprzeznaczenie = array();
$snowboardprzeznaczenie[] = 'Freeride';
$snowboardprzeznaczenie[] = 'Freestyle';
$snowboardprzeznaczenie[] = 'All-mountain';

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaSnowboardPrzeznaczenie.'` (`'.DB\SnowboardPrzeznaczenie::$nazwa.'`) VALUES (:przeznaczenie)');
    foreach ($snowboardprzeznaczenie as $pozycja){
        $stmt-> bindValue(':przeznaczenie',$pozycja,PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************


$nartyprzeznaczenie = array();
$nartyprzeznaczenie[] = 'Allround';
$nartyprzeznaczenie[] = 'Allmountain';
$nartyprzeznaczenie[] = 'Freeride';

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaNartyPrzeznaczenie.'` (`'.DB\NartyPrzeznaczenie::$nazwa.'`) VALUES (:przeznaczenie)');
    foreach ($nartyprzeznaczenie as $pozycja){
        $stmt-> bindValue(':przeznaczenie',$pozycja,PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$snowboard = array();
$snowboard[] = array(
    'model' => 'Ripcord',
    'idPrzeznaczenie' => '3',
    'dlugosc' => '1.54',
    'idOpcje' => null
);
$snowboard[] = array(
    'model' => 'Ripcord',
    'idPrzeznaczenie' => '3',
    'dlugosc' =>'1.54',
    'idOpcje' =>null
);
$snowboard[] = array(
    'model' => 'Ripcord',
    'idPrzeznaczenie' => '3',
    'dlugosc' =>'1.40',
    'idOpcje' =>null
);
$snowboard[] = array(
    'model' => 'Feather Wmn',
    'idPrzeznaczenie' => '3',
    'dlugosc' =>'1.54',
    'idOpcje' =>null
);
$snowboard[] = array(
    'model' => 'Feather Wmn',
    'idPrzeznaczenie' => '3',
    'dlugosc' =>'1.61',
    'idOpcje' =>null
);
$snowboard[] = array(
    'model' => 'Feather Wmn',
    'idPrzeznaczenie' => '3',
    'dlugosc' =>'1.61',
    'idOpcje' =>null
);
$snowboard[] = array(
    'model' => 'Custom Smalls',
    'idPrzeznaczenie' => '1',
    'dlugosc' =>'1.54',
    'idOpcje' =>null
);

$snowboard[] = array(
    'model' => 'Custom Smalls',
    'idPrzeznaczenie' => '1',
    'dlugosc' =>'1.58',
    'idOpcje' =>null
);

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaSnowboard.'` (`'.DB\Snowboard::$model.'`, `'.DB\Snowboard::$idPrzeznaczenie.'`, `'.DB\Snowboard::$dlugosc.'`, `'.DB\Snowboard::$idOpcje.'`) VALUES (:model, :idPrzeznaczenie, :dlugosc, :idOpcje)');

    foreach ($snowboard as $pozycja){
        $stmt-> bindValue(':model',$pozycja['model'],PDO::PARAM_STR);
        $stmt-> bindValue(':idPrzeznaczenie',$pozycja['idPrzeznaczenie'],PDO::PARAM_INT);
        $stmt-> bindValue(':dlugosc',$pozycja['dlugosc'],PDO::PARAM_STR);
        $stmt-> bindValue(':idOpcje',$pozycja['idOpcje'],PDO::PARAM_INT);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$narty = array();
$narty[] = array(
    'model' => 'PROGRESSOR 900',
    'idPrzeznaczenie' => '2',
    'dlugosc' => '1.74',
    'idOpcje' => null
);

$narty[] = array(
    'model' => 'PROGRESSOR 900',
    'idPrzeznaczenie' => '2',
    'dlugosc' => '1.74',
    'idOpcje' => null
);

$narty[] = array(
    'model' => 'Temptation',
    'idPrzeznaczenie' => '2',
    'dlugosc' => '1.55',
    'idOpcje' => null
);

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaNarty.'` (`'.DB\Narty::$model.'`, `'.DB\Narty::$idPrzeznaczenie.'`, `'.DB\Narty::$dlugosc.'`, `'.DB\Narty::$idOpcje.'`) VALUES (:model, :idPrzeznaczenie, :dlugosc, :idOpcje)');

    foreach ($narty as $pozycja){
        $stmt-> bindValue(':model',$pozycja['model'],PDO::PARAM_STR);
        $stmt-> bindValue(':idPrzeznaczenie',$pozycja['idPrzeznaczenie'],PDO::PARAM_INT);
        $stmt-> bindValue(':dlugosc',$pozycja['dlugosc'],PDO::PARAM_STR);
        $stmt-> bindValue(':idOpcje',$pozycja['idOpcje'],PDO::PARAM_INT);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$sprzet = array();
$sprzet[] = array(
    'idSnowboard' => '5',
    'idNarty' => null,
    'idButy' => null,
    'idKijki' => null,
    'idProducent' => '2',
    'data' => date("Y/m/d"),
    'idPlec' => '2',
    'cena' => '30',
    'zwrocone' => false
);

$sprzet[] = array(
    'idSnowboard' => '1',
    'idNarty' => null,
    'idButy' => null,
    'idKijki' => null,
    'idProducent' => '1',
    'data' => date("Y/m/d"),
    'idPlec' => '1',
    'cena' => '20',
    'zwrocone' => false
);

$sprzet[] = array(
    'idSnowboard' => '2',
    'idNarty' => null,
    'idButy' => null,
    'idKijki' => null,
    'idProducent' => '1',
    'data' => date("Y/m/d"),
    'idPlec' => '1',
    'cena' => '20',
    'zwrocone' => false
);

$sprzet[] = array(
    'idSnowboard' => null,
    'idNarty' => '1',
    'idButy' => null,
    'idKijki' => null,
    'idProducent' => '7',
    'data' => date("Y/m/d"),
    'idPlec' => '2',
    'cena' => '40',
    'zwrocone' => false
);

$sprzet[] = array(
    'idSnowboard' => null,
    'idNarty' => '2',
    'idButy' => null,
    'idKijki' => null,
    'idProducent' => '7',
    'data' => date("Y/m/d"),
    'idPlec' => '2',
    'cena' => '40',
    'zwrocone' => false
);

$sprzet[] = array(
    'idSnowboard' => null,
    'idNarty' => '3',
    'idButy' => null,
    'idKijki' => null,
    'idProducent' => '5',
    'data' => date("Y/m/d"),
    'idPlec' => '1',
    'cena' => '30',
    'zwrocone' => false
);


try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaSprzet.'` (`'.DB\Sprzet::$idSnowboard.'`, `'.DB\Sprzet::$idNarty.'`, `'.DB\Sprzet::$idButy.'`, `'.DB\Sprzet::$idKijki.'`, `'.DB\Sprzet::$idProducent.'`, `'.DB\Sprzet::$data.'`, `'.DB\Sprzet::$idPlec.'`, `'.DB\Sprzet::$cena.'`, `'.DB\Sprzet::$zwrocone.'`) VALUES (:idSnowboard, :idNarty, :idButy, :idKijki, :idProducent, :data, :idPlec, :cena, :zwrocone)');

    foreach ($sprzet as $pozycja){
        $stmt-> bindValue(':idSnowboard',$pozycja['idSnowboard'],PDO::PARAM_INT);
        $stmt-> bindValue(':idNarty',$pozycja['idNarty'],PDO::PARAM_INT);
        $stmt-> bindValue(':idButy',$pozycja['idButy'],PDO::PARAM_INT);
        $stmt-> bindValue(':idKijki',$pozycja['idKijki'],PDO::PARAM_INT);
        $stmt-> bindValue(':idProducent',$pozycja['idProducent'],PDO::PARAM_INT);
        $stmt-> bindValue(':data',$pozycja['data'],PDO::PARAM_STR);
        $stmt-> bindValue(':idPlec',$pozycja['idPlec'],PDO::PARAM_INT);
        $stmt-> bindValue(':cena',$pozycja['cena'],PDO::PARAM_STR);
        $stmt-> bindValue(':zwrocone',$pozycja['zwrocone'],PDO::PARAM_BOOL);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$status = array();
$status[] = 'Zarezerwowane';
$status[] = 'Przygotowane';
$status[] = 'Wypożyczone';
$status[] = 'Zakończone';

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaStatus.'` (`'.DB\Status::$nazwa.'`) VALUES (:status)');
    foreach ($status as $pozycja){
        $stmt-> bindValue(':status',$pozycja,PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************

$uzytkownik = array();
$uzytkownik[] = array(
    'nazwa' => 'admin',
    'haslo' => '81dc9bdb52d04dc20036dbd8313ed055'
);

try{
    $stmt = $pdo -> prepare('INSERT INTO `'.DB::$tabelaUzytkownik.'` (`'.DB\Uzytkownik::$nazwa.'`,`'.DB\Uzytkownik::$haslo.'` ) VALUES (:nazwa, :haslo)');
    foreach ($uzytkownik as $pozycja){
        $stmt-> bindValue(':nazwa',$pozycja['nazwa'],PDO::PARAM_STR);
        $stmt-> bindValue(':haslo',$pozycja['haslo'],PDO::PARAM_STR);
        $stmt->execute();
    }

}catch(PDOException $e){
    echo \Config\Database\DBErrorName::$noadd;
}

// ***********************************************************************