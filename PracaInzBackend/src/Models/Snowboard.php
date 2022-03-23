<?php

namespace Models;
use \PDO;

use Config\Database as DB;

class Snowboard extends Model
{
    //pobranie wszystkich snowboardow pogrupowanych + innerjoin z sprzet i typem
    public function getAll($option = false){
        try
        {
            if($option == false) {
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Snowboard::$model . ',
            ' . DB\DBConfig\Snowboard::$dlugosc . ',
            `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`,
            COUNT(*) AS Ilosc

             FROM `' . DB\DBConfig::$tabelaSnowboard . '` INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             GROUP BY `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`, `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$model . '`, `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$dlugosc . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`
             ');
            } else if ($option == true){
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Snowboard::$model . ',
            ' . DB\DBConfig\Snowboard::$dlugosc . ',
            `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`

             FROM `' . DB\DBConfig::$tabelaSnowboard . '` INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`
             ');
            }

            $snowboard = $stmt->fetchAll();
            $stmt->closeCursor();

            if($snowboard && !empty($snowboard))
            {
                $data['snowboard'] = $snowboard;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak snowboardów';
                return $data;
            }
        }
        catch(\PDOException $e)
        {
            //$data['msg'] = 'Połączenie z bazą nie powidoło się!';
        }
    }


    public function getAllSnowboard()
    {

        try {
            $stmt = $this->pdo->query('SELECT *

             FROM `' . DB\DBConfig::$tabelaSnowboard . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`
             INNER JOIN `' . DB\DBConfig::$tabelaSnowboardPrzeznaczenie . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaSnowboardPrzeznaczenie . '`.`' . DB\DBConfig\SnowboardPrzeznaczenie::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
            
             ');


            $snowboard = $stmt->fetchAll();

            $stmt->closeCursor();



            if ($snowboard && !empty($snowboard)) {
                return $snowboard;
            } else {
                return null;
                //$data['categories'] = array();
                //$data['msg'] = 'Brak kategorii do wyświetlenia';
            }

        } catch (\PDOException $e) {
            //$data['msg'] = 'Połączenie z bazą nie powidoło się!';
        }
    }

        public function getOne($id){

        try
        {

            $stmt = $this->pdo->prepare('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`,
            ' . DB\DBConfig\Snowboard::$model . ',
            ' . DB\DBConfig\Snowboard::$dlugosc . ',
             ' . DB\DBConfig\Snowboard::$idPrzeznaczenie . ',
            `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
                
            FROM `'.DB\DBConfig::$tabelaSnowboard.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idSnowboard.'`
            INNER JOIN `'.DB\DBConfig::$tabelaProducent.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=`'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaPlec.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=`'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaPrzeznaczenie.'` 
                ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$idPrzeznaczenie.'`=`'.DB\DBConfig::$tabelaPrzeznaczenie.'`.`'.DB\DBConfig\Przeznaczenie::$id.'` 
            WHERE `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
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

    //zwraca indeksy takich samych snowboardow z tabeli snowboard i tabeli sprzet
    public function getOneType($snowboard){

        //d($snowboard);
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Sprzet::$id.'` as IdSnowboard,
                `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$id.'` as IdSprzet
                FROM `'.DB\DBConfig::$tabelaSnowboard.'`
                    INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                        ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idSnowboard.'` 
                WHERE `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$model.'`=:model AND
                    `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$dlugosc.'` like  :dlugosc AND
                    `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=:producent AND
                    `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=:plec AND
                    `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$idPrzeznaczenie.'`=:przeznaczenie
             ');


            $stmt->bindValue(':model', $snowboard['Model'], PDO::PARAM_STR);
            $stmt->bindValue(':dlugosc', $snowboard['Dlugosc'], PDO::PARAM_STR);
            $stmt->bindValue(':producent', $snowboard['IdProducent'], PDO::PARAM_INT);
            $stmt->bindValue(':plec', $snowboard['IdPlec'], PDO::PARAM_INT);
            $stmt->bindValue(':przeznaczenie', $snowboard['IdPrzeznaczenie'], PDO::PARAM_INT);

            $result = $stmt->execute();
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

    /*
     * sprawdzenie czy dany snowboard istnieje (model, idPrzeznaczeniem, dlugosc, idProducent, idPlec)
     * zlaczenie tabel sprzet+snowboard
     */
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


    public function add($model, $przeznaczenie, $dlugosc, $ilosc){
        $idSnowboard = array();
        for ($i=0;$i<$ilosc;$i++) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaSnowboard . '`
             (`' . \Config\Database\DBConfig\Snowboard::$model . '`
             ,`' . \Config\Database\DBConfig\Snowboard::$idPrzeznaczenie . '`
             ,`' . \Config\Database\DBConfig\Snowboard::$dlugosc . '`) 
                                           VALUES (:model, :przeznaczenie, :dlugosc)');

                $stmt->bindValue(':model', $model, PDO::PARAM_STR);
                $stmt->bindValue(':przeznaczenie', $przeznaczenie, PDO::PARAM_INT);
                $stmt->bindValue(':dlugosc', $dlugosc, PDO::PARAM_STR);

                $result = $stmt->execute();

                if(!$result)
                    return $data['error'] = "Nie udało się dodać snowboardu";

                $id = $this->pdo->lastInsertId();
                $idSnowboard[] = intval($id);


            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }

        return $idSnowboard;

    }

    public function delete($id){
        $data = array();

            try {
                $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaSnowboard . '` WHERE  `' . \Config\Database\DBConfig\Snowboard::$id . '`=:id');
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $result = $stmt->execute();
                if($result){
                    $data['msg'] = "Poprawnie usunięto snowboard";
                }else{
                    $data['error'] = "Wystąpił błąd podczas usuwania snowboardu";
                }
                $stmt->closeCursor();
            } catch (\PDOException $e) {
                //$data['error'] = \Config\Database\DBErrorName::$query;
            }

        return $data;
    }

    public function update($indeksy, $snowboard){
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

                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaSnowboard . '` SET
                    `' . \Config\Database\DBConfig\Snowboard::$model . '`=:model,
                    `' . \Config\Database\DBConfig\Snowboard::$idPrzeznaczenie . '`=:idPrzeznaczenie,
                    `' . \Config\Database\DBConfig\Snowboard::$dlugosc . '`=:dlugosc
                
                 WHERE `' . \Config\Database\DBConfig\Snowboard::$id . '`=:id');

                $stmt->bindValue(':id', $indeks['IdSnowboard'], PDO::PARAM_INT);
                $stmt->bindValue(':model', $snowboard['model'], PDO::PARAM_STR);
                $stmt->bindValue(':idPrzeznaczenie', $snowboard['przeznaczenie'], PDO::PARAM_INT);
                $stmt->bindValue(':dlugosc', $snowboard['dlugosc'], PDO::PARAM_STR);

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

    public function updateOptions($idSnowboard, $idOpcje){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        if($idSnowboard == null){
            $data['msg'] = "Puste indeksy";
            return $data;
        }
        try	{


                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaSnowboard . '` SET
                    `' . \Config\Database\DBConfig\Snowboard::$idOpcje . '`=:idOpcje
                
                 WHERE `' . \Config\Database\DBConfig\Snowboard::$id . '`=:id');

                $stmt->bindValue(':id', $idSnowboard, PDO::PARAM_INT);
                $stmt->bindValue(':idOpcje', $idOpcje, PDO::PARAM_INT);


                $result = $stmt->execute();
                $rows = $stmt->rowCount();
                if (!$result)
                    $data['msg'] = "Brak pasujących wyników";
                else
                    $data['msg'] = "OK";
                $stmt->closeCursor();

        }
        catch(\PDOException $e)	{
            $data['msg'] = "Błędne zapytanie";
        }
        return $data;
    }
}