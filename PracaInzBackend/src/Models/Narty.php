<?php

namespace Models;
use \PDO;

use Config\Database as DB;

class Narty extends Model
{

    public function getAll($option = false){

        try
        {
            if($option == false) {
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Narty::$model . ',
            ' . DB\DBConfig\Narty::$dlugosc . ',
            `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`,
            COUNT(*) AS Ilosc

             FROM `' . DB\DBConfig::$tabelaNarty . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             GROUP BY `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`, `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$model . '`, `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$dlugosc . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`
             ');
            } else if($option == true){
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Narty::$model . ',
            ' . DB\DBConfig\Narty::$dlugosc . ',
            `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`

             FROM `' . DB\DBConfig::$tabelaNarty . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPrzeznaczenie . '` 
                ON `' . DB\DBConfig::$tabelaNarty . '`.`' . DB\DBConfig\Narty::$idPrzeznaczenie . '`=`' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`
             ');
            }

            $narty = $stmt->fetchAll();

            $stmt->closeCursor();



            if($narty && !empty($narty))
            {
                $data['skis'] = $narty;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak nart';
                return $data;
                //$data['categories'] = array();
                //$data['msg'] = 'Brak kategorii do wyświetlenia';
            }

        }
        catch(\PDOException $e)
        {
            //$data['msg'] = 'Połączenie z bazą nie powidoło się!';
        }
    }

    public function getOne($id){
        try
        {

            $stmt = $this->pdo->prepare('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`,
            ' . DB\DBConfig\Narty::$model . ',
            ' . DB\DBConfig\Narty::$dlugosc . ',
            ' . DB\DBConfig\Narty::$idPrzeznaczenie . ',
            `' . DB\DBConfig::$tabelaPrzeznaczenie . '`.`' . DB\DBConfig\Przeznaczenie::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
            
            FROM `'.DB\DBConfig::$tabelaNarty.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                ON `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idNarty.'`
            INNER JOIN `'.DB\DBConfig::$tabelaProducent.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=`'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaPlec.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=`'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaPrzeznaczenie.'` 
                ON `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$idPrzeznaczenie.'`=`'.DB\DBConfig::$tabelaPrzeznaczenie.'`.`'.DB\DBConfig\Przeznaczenie::$id.'`
             
            WHERE `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $skis = $stmt->fetchAll();
            $stmt->closeCursor();


            if($skis && !empty($skis))
            {
                return $skis;
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

    public function add($model, $przeznaczenie, $dlugosc, $ilosc){

        $idSkis = array();

        for ($i=0;$i<$ilosc;$i++) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaNarty . '`
             (`' . \Config\Database\DBConfig\Narty::$model . '`
             ,`' . \Config\Database\DBConfig\Narty::$idPrzeznaczenie . '`
             ,`' . \Config\Database\DBConfig\Narty::$dlugosc . '`) 
                                           VALUES (:model, :przeznaczenie, :dlugosc)');

                $stmt->bindValue(':model', $model, PDO::PARAM_STR);
                $stmt->bindValue(':przeznaczenie', $przeznaczenie, PDO::PARAM_INT);
                $stmt->bindValue(':dlugosc', $dlugosc, PDO::PARAM_STR);

                $result = $stmt->execute();


                $id = $this->pdo->lastInsertId();
                $idSkis[] = intval($id);


            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }

        return $idSkis;

    }

    public function getOneType($model, $przeznaczenie, $dlugosc, $producent, $plec){
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$id.'` as IdSprzet,
                                                `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$id.'` as IdNarty
                                        FROM 
                                        `'.DB\DBConfig::$tabelaNarty.'`
                                         INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                                            ON `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idNarty.'`           
             WHERE `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$model.'`=:model AND
                   `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$dlugosc.'` like  :dlugosc AND
                   `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=:producent AND
                   `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=:plec AND
                   `'.DB\DBConfig::$tabelaNarty.'`.`'.DB\DBConfig\Narty::$idPrzeznaczenie.'`=:przeznaczenie
             ');


            $stmt->bindValue(':model', $model, PDO::PARAM_STR);
            $stmt->bindValue(':dlugosc', $dlugosc, PDO::PARAM_STR);
            $stmt->bindValue(':producent',$producent, PDO::PARAM_STR);
            $stmt->bindValue(':plec', $plec, PDO::PARAM_STR);
            $stmt->bindValue(':przeznaczenie', $przeznaczenie, PDO::PARAM_STR);

            //d($stmt);

            $result = $stmt->execute();
            $skis = $stmt->fetchAll();
            $stmt->closeCursor();


            if($skis && !empty($skis))
            {
                return $skis;
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

    public function update($indeksy, $skis){
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

                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaNarty . '` SET
                    `' . \Config\Database\DBConfig\Narty::$model . '`=:model,
                    `' . \Config\Database\DBConfig\Narty::$idPrzeznaczenie . '`=:idPrzeznaczenie,
                    `' . \Config\Database\DBConfig\Narty::$dlugosc . '`=:dlugosc
                
                 WHERE `' . \Config\Database\DBConfig\Narty::$id . '`=:id');

                $stmt->bindValue(':id', $indeks['IdNarty'], PDO::PARAM_INT);
                $stmt->bindValue(':model', $skis['model'], PDO::PARAM_STR);
                $stmt->bindValue(':idPrzeznaczenie', $skis['przeznaczenie'], PDO::PARAM_INT);
                $stmt->bindValue(':dlugosc', $skis['dlugosc'], PDO::PARAM_STR);

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

    public function updateOptions($idSkis, $idOpcje){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        if($idSkis == null){
            $data['msg'] = "Puste indeksy";
            return $data;
        }
        try	{


            $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaNarty . '` SET
                    `' . \Config\Database\DBConfig\Narty::$idOpcje . '`=:idOpcje
                
                 WHERE `' . \Config\Database\DBConfig\Narty::$id . '`=:id');

            $stmt->bindValue(':id', $idSkis, PDO::PARAM_INT);
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

    public function delete($id){
        $data = array();

        try {
            $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaNarty . '` WHERE  `' . \Config\Database\DBConfig\Narty::$id . '`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            if($result){
                $data['msg'] = "Poprawnie usunięto narty";
            }else{
                $data['error'] = "Wystąpił błąd podczas usuwania nart";
            }
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            //$data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    /*

    public function getAllSnowboard()
    {

        try {
            $stmt = $this->pdo->query('SELECT *

             FROM `' . DB\DBConfig::$tabelaSnowboard . '` INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` ON `' . DB\DBConfig::$tabelaSnowboard . '`.`' . DB\DBConfig\Snowboard::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`
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
                `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$id.'`, 
                `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$model.'`,
                `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$dlugosc.'`,
                `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$nazwa.'`,
                `'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$nazwa.'`,
                `'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'`.`'.DB\DBConfig\SnowboardPrzeznaczenie::$nazwa.'`,
                `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idSnowboard.'`,
                `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$cena.'`
            FROM `'.DB\DBConfig::$tabelaSnowboard.'` INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idSnowboard.'`
             INNER JOIN `'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'` ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$idPrzeznaczenie.'`=`'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'`.`'.DB\DBConfig\SnowboardPrzeznaczenie::$id.'`
             INNER JOIN `'.DB\DBConfig::$tabelaProducent.'` ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=`'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`
             INNER JOIN `'.DB\DBConfig::$tabelaPlec.'` ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=`'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$id.'` 
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

    public function getOneType($snowboard){

        //d($snowboard);
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$id.'` FROM 
                                        `'.DB\DBConfig::$tabelaSnowboard.'`
                                         INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                                            ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idSnowboard.'`
                                        INNER JOIN `'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'` 
                                            ON `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$idPrzeznaczenie.'`=`'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'`.`'.DB\DBConfig\SnowboardPrzeznaczenie::$id.'`
                                        INNER JOIN `'.DB\DBConfig::$tabelaProducent.'`
                                            ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=`'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`
                                        INNER JOIN `'.DB\DBConfig::$tabelaPlec.'` 
                                            ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=`'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$id.'` 
             WHERE `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$model.'`=:model AND
                   `'.DB\DBConfig::$tabelaSnowboard.'`.`'.DB\DBConfig\Snowboard::$dlugosc.'` like  :dlugosc AND
                   `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$nazwa.'`=:producent AND
                   `'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$nazwa.'`=:plec AND
                   `'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'`.`'.DB\DBConfig\SnowboardPrzeznaczenie::$nazwa.'`=:przeznaczenie
             ');


            $stmt->bindValue(':model', $snowboard['Model'], PDO::PARAM_STR);
            $stmt->bindValue(':dlugosc', $snowboard['Dlugosc'], PDO::PARAM_STR);
            $stmt->bindValue(':producent', $snowboard['NazwaProducent'], PDO::PARAM_STR);
            $stmt->bindValue(':plec', $snowboard['NazwaPlec'], PDO::PARAM_STR);
            $stmt->bindValue(':przeznaczenie', $snowboard['NazwaPrzeznaczenie'], PDO::PARAM_STR);

            //d($stmt);

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
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($id === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        try	{
            $stmt = $this->pdo->prepare('DELETE FROM  `'.\Config\Database\DBConfig::$tabelaSnowboard.'` WHERE  `'.\Config\Database\DBConfig\Snowboard::$id.'`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $stmt->closeCursor();
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }
    */

}