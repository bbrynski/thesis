<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 15.11.2018
 * Time: 13:01
 */

namespace Models;
use \PDO;

use Config\Database as DB;

class Kijki extends Model
{
    public function getAll($option = false){

        try
        {
            if($option == false) {
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Kijki::$model . ',
            ' . DB\DBConfig\Kijki::$dlugosc . ',
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`,
            COUNT(*) AS Ilosc

             FROM `' . DB\DBConfig::$tabelaKijki . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` 
                ON `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             GROUP BY `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`, `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$model . '`, `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$dlugosc . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`
             ');
            } else if ($option == true){
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Kijki::$model . ',
            ' . DB\DBConfig\Kijki::$dlugosc . ',
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`

             FROM `' . DB\DBConfig::$tabelaKijki . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` 
                ON `' . DB\DBConfig::$tabelaKijki . '`.`' . DB\DBConfig\Kijki::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`
             ');
            }

            $skipoles = $stmt->fetchAll();

            $stmt->closeCursor();



            if($skipoles && !empty($skipoles))
            {
                $data['skiPoles'] = $skipoles;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak kijków';
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
`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`,
            ' . DB\DBConfig\Kijki::$model . ',
            ' . DB\DBConfig\Kijki::$dlugosc . ',
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
            
            FROM `'.DB\DBConfig::$tabelaKijki.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                ON `'.DB\DBConfig::$tabelaKijki.'`.`'.DB\DBConfig\Kijki::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idKijki.'`
            INNER JOIN `'.DB\DBConfig::$tabelaProducent.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=`'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaPlec.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=`'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$id.'`
            WHERE `'.DB\DBConfig::$tabelaKijki.'`.`'.DB\DBConfig\Kijki::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $skiPoles = $stmt->fetchAll();
            $stmt->closeCursor();


            if($skiPoles && !empty($skiPoles))
            {
                return $skiPoles;
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

    public function getOneType($model, $dlugosc, $producent, $plec){
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$id.'` as IdSprzet,
                                                `'.DB\DBConfig::$tabelaKijki.'`.`'.DB\DBConfig\Kijki::$id.'` as IdKijki
                                        FROM 
                                        `'.DB\DBConfig::$tabelaKijki.'`
                                         INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                                            ON `'.DB\DBConfig::$tabelaKijki.'`.`'.DB\DBConfig\Kijki::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idKijki.'`           
             WHERE `'.DB\DBConfig::$tabelaKijki.'`.`'.DB\DBConfig\Kijki::$model.'`=:model AND
                   `'.DB\DBConfig::$tabelaKijki.'`.`'.DB\DBConfig\Kijki::$dlugosc.'` like  :dlugosc AND
                   `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=:producent AND
                   `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=:plec
             ');
            $tmp = $stmt;


            $stmt->bindValue(':model', $model, PDO::PARAM_STR);
            $stmt->bindValue(':dlugosc', $dlugosc, PDO::PARAM_STR);
            $stmt->bindValue(':producent',$producent, PDO::PARAM_STR);
            $stmt->bindValue(':plec', $plec, PDO::PARAM_STR);

            //d($stmt);

            $result = $stmt->execute();
            $skiPoles = $stmt->fetchAll();
            $stmt->closeCursor();


            if($skiPoles && !empty($skiPoles))
            {
                return $skiPoles;
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

    public function add($model, $dlugosc, $ilosc){

        $idSkiPoles = array();

        for ($i=0;$i<$ilosc;$i++) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaKijki . '`
             (`' . \Config\Database\DBConfig\Narty::$model . '`
             ,`' . \Config\Database\DBConfig\Narty::$dlugosc . '`) 
                                           VALUES (:model, :dlugosc)');

                $stmt->bindValue(':model', $model, PDO::PARAM_STR);
                $stmt->bindValue(':dlugosc', $dlugosc, PDO::PARAM_STR);

                $result = $stmt->execute();


                $id = $this->pdo->lastInsertId();
                $idSkiPoles[] = intval($id);


            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }

        return $idSkiPoles;

    }

    public function update($indeksy, $skiPoles){
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

                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaKijki . '` SET
                    `' . \Config\Database\DBConfig\Kijki::$model . '`=:model,
                    `' . \Config\Database\DBConfig\Kijki::$dlugosc . '`=:dlugosc
                
                 WHERE `' . \Config\Database\DBConfig\Kijki::$id . '`=:id');

                $stmt->bindValue(':id', $indeks['IdKijki'], PDO::PARAM_INT);
                $stmt->bindValue(':model', $skiPoles['model'], PDO::PARAM_STR);
                $stmt->bindValue(':dlugosc', $skiPoles['dlugosc'], PDO::PARAM_STR);

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

    public function delete($id){
        $data = array();

        try {
            $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaKijki . '` WHERE  `' . \Config\Database\DBConfig\Kijki::$id . '`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            if($result){
                $data['msg'] = "Poprawnie usunięto kijki";
            }else{
                $data['error'] = "Wystąpił błąd podczas usuwania kijków";
            }
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            //$data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

}