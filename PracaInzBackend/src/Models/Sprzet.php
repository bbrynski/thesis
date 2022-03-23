<?php

namespace Models;
use \PDO;

use Config\Database as DB;

class Sprzet extends Model
{

    public function getAllToService(){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        $data = null;

        try {
            $stmt = $this->pdo->query('SELECT
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`
 
            FROM `'.\Config\Database\DBConfig::$tabelaSprzet.'`
            WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$zwrocony . '` = 1;
             ');

            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['equipment'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function changeToReturn($idEquipment){
        $data = array();
        $data['error'] = null;
        $data['msg'] = null;
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idEquipment == null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }

        try	{
            $stmt = $this->pdo->prepare('UPDATE  `'.\Config\Database\DBConfig::$tabelaSprzet.'` SET
                    `'
                .\Config\Database\DBConfig\Sprzet::$zwrocony.'`=:zwrocony
                
                WHERE `'
                .\Config\Database\DBConfig\Sprzet::$id.'`=:id');
            $stmt->bindValue(':id', $idEquipment, PDO::PARAM_INT);
            $stmt->bindValue(':zwrocony',1, PDO::PARAM_INT);

            $result = $stmt->execute();

            if(!$result)
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
            else
                $data['msg'] = "Sprzęt zwrócony";
            $stmt->closeCursor();
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    public function changeStatusAfterService($idEquipment){
        $data = array();
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idEquipment == null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }

        try	{
            $stmt = $this->pdo->prepare('UPDATE  `'.\Config\Database\DBConfig::$tabelaSprzet.'` SET
                    `'
                .\Config\Database\DBConfig\Sprzet::$zwrocony.'`=:zwrocony
                
                WHERE `'
                .\Config\Database\DBConfig\Sprzet::$id.'`=:id');
            $stmt->bindValue(':id', $idEquipment, PDO::PARAM_INT);
            $stmt->bindValue(':zwrocony',0, PDO::PARAM_INT);

            $result = $stmt->execute();

            if(!$result)
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
            else
                $data['msg'] = "OK";
            $stmt->closeCursor();
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    public function getAllSnowboard(){

        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaSprzet.'` WHERE  `'.DB\DBConfig\Sprzet::$idSnowboard.'` =1');
            $snowboard = $stmt->fetchAll();
            $stmt->closeCursor();
            if($snowboard && !empty($snowboard))
            {
                return $snowboard;
            }
            else
            {
                return null;
                //$data['categories'] = array();
                //$data['msg'] = 'Brak kategorii do wyświetlenia';
            }

        }
        catch(\PDOException $e)
        {
            //$data['msg'] = 'Połączenie z bazą nie powidoło się!';
        }


    }

    public function add($idTabela, $producent, $plec, $cena){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idTabela === null && $producent === null && $plec === null && $cena === null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        $data = array();

        foreach ($idTabela as $idSnowboard) {

            try {
                $date = date("Y/m/d");


                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaSprzet . '`
             (`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idProducent . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$data . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idPlec . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$cena . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$zwrocony . '`) 
                                           VALUES (:idSnowboard, :producent, :date, :plec, :cena, :zwrocony)');

                $stmt->bindValue(':idSnowboard', $idSnowboard, PDO::PARAM_INT);
                $stmt->bindValue(':producent', $producent, PDO::PARAM_INT);
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->bindValue(':plec', $plec, PDO::PARAM_INT);
                $stmt->bindValue(':cena', $cena, PDO::PARAM_INT);
                $stmt->bindValue(':zwrocony', 0, PDO::PARAM_INT);

                $result = $stmt->execute();

                if(!$result)
                    $data['error'] = 'Nie udało sie dodać sprzętu';
                else
                    $data['msg'] = 'OK';

                $stmt->closeCursor();

            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }
        return $data;
}

    public function addSkis($idTabela, $producent, $plec, $cena){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idTabela === null && $producent === null && $plec === null && $cena === null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        $data = array();

        foreach ($idTabela as $idSkis) {

            try {
                $date = date("Y/m/d");


                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaSprzet . '`
             (`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idProducent . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$data . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idPlec . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$cena . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$zwrocony . '`) 
                                           VALUES (:idSkis, :producent, :date, :plec, :cena, :zwrocony)');

                $stmt->bindValue(':idSkis', $idSkis, PDO::PARAM_INT);
                $stmt->bindValue(':producent', $producent, PDO::PARAM_INT);
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->bindValue(':plec', $plec, PDO::PARAM_INT);
                $stmt->bindValue(':cena', $cena, PDO::PARAM_STR);
                $stmt->bindValue(':zwrocony', 0, PDO::PARAM_INT);

                $result = $stmt->execute();

                if(!$result)
                    $data['error'] = 'Nie udało sie dodać sprzętu';
                else
                    $data['msg'] = 'OK';

                $stmt->closeCursor();

            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }
        return $data;
    }

    public function addSkiPoles($idTabela, $producent, $plec, $cena){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idTabela === null && $producent === null && $plec === null && $cena === null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        $data = array();

        foreach ($idTabela as $idSkiPoles) {

            try {
                $date = date("Y/m/d");


                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaSprzet . '`
             (`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idProducent . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$data . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idPlec . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$cena . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$zwrocony . '`) 
                                           VALUES (:idSkiPoles, :producent, :date, :plec, :cena, :zwrocony)');

                $stmt->bindValue(':idSkiPoles', $idSkiPoles, PDO::PARAM_INT);
                $stmt->bindValue(':producent', $producent, PDO::PARAM_INT);
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->bindValue(':plec', $plec, PDO::PARAM_INT);
                $stmt->bindValue(':cena', $cena, PDO::PARAM_STR);
                $stmt->bindValue(':zwrocony', 0, PDO::PARAM_INT);

                $result = $stmt->execute();

                if(!$result)
                    $data['error'] = 'Nie udało sie dodać sprzętu';
                else
                    $data['msg'] = 'OK';

                $stmt->closeCursor();

            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }
        return $data;
    }

    public function addBoots($idTabela, $producent, $plec, $cena){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idTabela === null && $producent === null && $plec === null && $cena === null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        $data = array();

        foreach ($idTabela as $idBoots) {

            try {
                $date = date("Y/m/d");


                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaSprzet . '`
             (`' . \Config\Database\DBConfig\Sprzet::$idButy . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idProducent . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$data . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$idPlec . '`
              ,`' . \Config\Database\DBConfig\Sprzet::$cena . '`
             ,`' . \Config\Database\DBConfig\Sprzet::$zwrocony . '`) 
                                           VALUES (:idButy, :producent, :date, :plec, :cena, :zwrocony)');

                $stmt->bindValue(':idButy', $idBoots, PDO::PARAM_INT);
                $stmt->bindValue(':producent', $producent, PDO::PARAM_INT);
                $stmt->bindValue(':date', $date, PDO::PARAM_STR);
                $stmt->bindValue(':plec', $plec, PDO::PARAM_INT);
                $stmt->bindValue(':cena', $cena, PDO::PARAM_STR);
                $stmt->bindValue(':zwrocony', 0, PDO::PARAM_INT);

                $result = $stmt->execute();

                if(!$result)
                    $data['error'] = 'Nie udało sie dodać sprzętu';
                else
                    $data['msg'] = 'OK';

                $stmt->closeCursor();

            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }
        return $data;
    }

    /*
     * sprawdzenie czy dany snowboard istnieje (model, idPrzeznaczeniem, dlugosc, idProducent, idPlec)
     * zlaczenie tabel sprzet+snowboard
     */
    /*
    public function isSnowboardExist($model, $idPrzeznaczenie, $dlugosc, $idProducent, $idPlec){
        try
        {

            $stmt = $this->pdo->prepare('SELECT 
                `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`, 
                `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`,
                `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$model.'`,
                `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$dlugosc.'`,
                `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$idPrzeznaczenie.'`
            FROM `'.DB\DBConfig::$tabelaSnowboard.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idSnowboard.'` 
            WHERE `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$model.'`=:model 
            AND `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$dlugosc.'`=:dlugosc
            AND `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$idPrzeznaczenie.'`=:idPrzeznaczenie
            AND `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=:idPlec
            AND `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=:idProducent');


            $stmt->bindValue(':model', $model, PDO::PARAM_STR);
            $stmt->bindValue(':dlugosc', $dlugosc, PDO::PARAM_STR);
            $stmt->bindValue(':idPrzeznaczenie', $idPrzeznaczenie, PDO::PARAM_INT);
            $stmt->bindValue(':idPlec', $idPlec, PDO::PARAM_INT);
            $stmt->bindValue(':idProducent', $idProducent, PDO::PARAM_INT);
            $result = $stmt->execute();
            $snowboard = $stmt->fetchAll();
            $stmt->closeCursor();

            if($snowboard && !empty($snowboard))
            {
                return true;
            }
            else
            {
                return false;
                //$data['categories'] = array();
                //$data['msg'] = 'Brak kategorii do wyświetlenia';
            }

        }
        catch(\PDOException $e)
        {
            //$data['msg'] = 'Połączenie z bazą nie powidoło się!';
        }
    }
*/

    public function updateSnowboard($indeksy, $snowboard){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        if($indeksy == null){
            $data['msg'] = "Puste indeksy";
            return $data;
        }
        try	{
            foreach($indeksy as $indeks) {

                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaSprzet . '` SET
                    `' . \Config\Database\DBConfig\Sprzet::$idProducent . '`=:idProducent,
                    `' . \Config\Database\DBConfig\Sprzet::$idPlec . '`=:idPlec,
                    `' . \Config\Database\DBConfig\Sprzet::$cena . '`=:cena
                
                 WHERE `' . \Config\Database\DBConfig\Sprzet::$id . '`=:id');

                $stmt->bindValue(':id', $indeks['IdSprzet'], PDO::PARAM_INT);
                $stmt->bindValue(':idProducent', $snowboard['producent'], PDO::PARAM_INT);
                $stmt->bindValue(':idPlec', $snowboard['plec'], PDO::PARAM_INT);
                $stmt->bindValue(':cena', $snowboard['cena'], PDO::PARAM_INT);

                $result = $stmt->execute();
                $rows = $stmt->rowCount();
                if (!$result)
                    $data['msg'] = "Brak pasujących wyników";
                else
                    $data['msg'] = "OK";
                $stmt->closeCursor();
            }
        }
        catch(\PDOException $e)	{
            $data['msg'] = "Błędne zapytanie";
        }
        return $data;
    }

    public function getStatisticsProducer($dateFrom, $dateTo){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($dateFrom === null && $dateTo === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = null;
        $data['equipment'] = null;

        try {
            $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             WHERE `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');

            $stmt->bindValue(':dataOd', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dataDo', $dateTo, PDO::PARAM_STR);

            $result = $stmt->execute();
            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['equipment'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function getStatisticsSnowboard($dateFrom, $dateTo, $option=false, $gender=false){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($dateFrom === null && $dateTo === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = null;
        $data['snowboard'] = null;

        try {
            if($option == false) {

                $stmt = $this->pdo->prepare('SELECT *
 
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             ');

            } else if ($option == 'model' && $gender == 'all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'model' && $gender == 'man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`,  COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '` =2
             GROUP BY `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'model' && $gender == 'woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`,  COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '` =1
             GROUP BY `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'length' && $gender == 'all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'length' && $gender == 'man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '` =2
             GROUP BY `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'length' && $gender == 'woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '` =1
             GROUP BY `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'gender'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa. '`
             ');
            } else if ($option == 'type' && $gender == 'all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa. '`
             ');
            } else if ($option == 'type' && $gender == 'man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa. '`
             ');
            }else if ($option == 'type' && $gender == 'woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaSnowboard . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`=`' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa. '`
             ');
            }

            $stmt->bindValue(':dataOd', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dataDo', $dateTo, PDO::PARAM_STR);

            $result = $stmt->execute();
            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['snowboard'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function getStatisticsSkis($dateFrom, $dateTo, $option=false, $gender=false){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($dateFrom === null && $dateTo === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = null;
        $data['skis'] = null;

        try {
            if($option == false) {
                $stmt = $this->pdo->prepare('SELECT *
 
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             ');
            }  else if ($option == 'model' && $gender == 'all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'model' && $gender == 'man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'model' && $gender == 'woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'length' && $gender == 'all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo 
             GROUP BY `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'length' && $gender == 'man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'length' && $gender == 'woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'gender'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa. '`
             ');
            } else if ($option == 'type' && $gender == 'all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa. '`
             ');
            } else if ($option == 'type' && $gender == 'man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa. '`
             ');
            }
            else if ($option == 'type' && $gender == 'woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaNarty . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`=`' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa. '`
             ');
            }

            $stmt->bindValue(':dataOd', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dataDo', $dateTo, PDO::PARAM_STR);

            $result = $stmt->execute();
            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['skis'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function getStatisticsSkiPoles($dateFrom, $dateTo, $option=false, $gender=false){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($dateFrom === null && $dateTo === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = null;
        $data['skiPoles'] = null;

        try {
            if($option == false) {
                $stmt = $this->pdo->prepare('SELECT *
 
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             ');
            } else if ($option == 'model' && $gender=='all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaKijki . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`=`' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'model' && $gender=='man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaKijki . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`=`' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'model' && $gender=='woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaKijki . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`=`' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'length'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$dlugosc . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaKijki . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`=`' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$dlugosc. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'gender'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa. '`
             ');
            }

            $stmt->bindValue(':dataOd', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dataDo', $dateTo, PDO::PARAM_STR);

            $result = $stmt->execute();
            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['skiPoles'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function getStatisticsBoots($dateFrom, $dateTo, $option=false, $gender=false){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($dateFrom === null && $dateTo === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = null;
        $data['boots'] = null;

        try {
            if($option == false) {
                $stmt = $this->pdo->prepare('SELECT *
 
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             ');
            } else if ($option == 'model' && $gender=='all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaButy . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`=`' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model. '`
             ORDER BY Ilosc DESC
             ');
            }else if ($option == 'model' && $gender=='man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaButy . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`=`' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'model' && $gender=='woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaButy . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`=`' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }

            else if ($option == 'size' && $gender=='all'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaButy . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`=`' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar. '`
             ORDER BY Ilosc DESC
             ');
            } else if ($option == 'size' && $gender=='man'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaButy . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`=`' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=2
             GROUP BY `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }
            else if ($option == 'size' && $gender=='woman'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar . '`,`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaButy . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`=`' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`=1
             GROUP BY `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar. '` AND `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY Ilosc DESC
             ');
            }

            else if ($option == 'gender'){
                $stmt = $this->pdo->prepare('SELECT `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, COUNT(*) as Ilosc
            FROM `' . \Config\Database\DBConfig::$tabelaSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaWypozyczenie . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`=`' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$idSprzet . '`
            INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             WHERE `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '` IS NOT NULL AND `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '` BETWEEN :dataOd AND :dataDo
             GROUP BY `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa. '`
             ');
            }

            $stmt->bindValue(':dataOd', $dateFrom, PDO::PARAM_STR);
            $stmt->bindValue(':dataDo', $dateTo, PDO::PARAM_STR);

            $result = $stmt->execute();
            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['boots'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }
}