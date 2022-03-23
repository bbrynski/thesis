<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 18.11.2018
 * Time: 23:15
 */

namespace Models;
use \PDO;

use Config\Database as DB;

class Buty extends Model
{
    public function getAll($option = false){

        try
        {
            if($option == false) {
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Buty::$model . ',
            ' . DB\DBConfig\Buty::$rozmiar . ',
            `' . DB\DBConfig::$tabelaButyKategoria . '`.`' . DB\DBConfig\ButyKategoria::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`,
            COUNT(*) AS Ilosc

             FROM `' . DB\DBConfig::$tabelaButy . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` 
                ON `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`
             INNER JOIN `' . DB\DBConfig::$tabelaButyKategoria . '` 
                ON `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$idKategoria . '`=`' . DB\DBConfig::$tabelaButyKategoria . '`.`' . DB\DBConfig\ButyKategoria::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             GROUP BY `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`, `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$model . '`, `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$rozmiar . '`, `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`, `' . DB\DBConfig::$tabelaButyKategoria . '`.`' . DB\DBConfig\ButyKategoria::$nazwa . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`
             ');
            } else if($option == true){
                $stmt = $this->pdo->query('SELECT 
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            ' . DB\DBConfig\Buty::$model . ',
            ' . DB\DBConfig\Buty::$rozmiar . ',
            `' . DB\DBConfig::$tabelaButyKategoria . '`.`' . DB\DBConfig\ButyKategoria::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`

             FROM `' . DB\DBConfig::$tabelaButy . '` 
             INNER JOIN `' . DB\DBConfig::$tabelaSprzet . '` 
                ON `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$id . '`=`' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`
             INNER JOIN `' . DB\DBConfig::$tabelaButyKategoria . '` 
                ON `' . DB\DBConfig::$tabelaButy . '`.`' . DB\DBConfig\Buty::$idKategoria . '`=`' . DB\DBConfig::$tabelaButyKategoria . '`.`' . DB\DBConfig\ButyKategoria::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaProducent . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`=`' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$id . '`
             INNER JOIN `' . DB\DBConfig::$tabelaPlec . '` 
                ON `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`=`' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$id . '`
             ORDER BY `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`
             ');
            }

            $buty = $stmt->fetchAll();

            $stmt->closeCursor();



            if($buty && !empty($buty))
            {
                $data['boots'] = $buty;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak butów';
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

    public function add($model, $rozmiar, $kategoria, $ilosc){

        $idBoots = array();

        for ($i=0;$i<$ilosc;$i++) {
            try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaButy . '`
             (`' . \Config\Database\DBConfig\Buty::$model . '`
             ,`' . \Config\Database\DBConfig\Buty::$idKategoria . '`
             ,`' . \Config\Database\DBConfig\Buty::$rozmiar . '`) 
                                           VALUES (:model, :kategoria, :rozmiar)');

                $stmt->bindValue(':model', $model, PDO::PARAM_STR);
                $stmt->bindValue(':kategoria', $kategoria, PDO::PARAM_INT);
                $stmt->bindValue(':rozmiar', $rozmiar, PDO::PARAM_STR);

                $result = $stmt->execute();


                $id = $this->pdo->lastInsertId();
                $idBoots[] = intval($id);


            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
        }

        return $idBoots;

    }

    public function getOne($id){
        try
        {

            $stmt = $this->pdo->prepare('SELECT
 `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$cena . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idPlec . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idProducent . '`,
            ' . DB\DBConfig\Buty::$model . ',
            ' . DB\DBConfig\Buty::$rozmiar . ',
             ' . DB\DBConfig\Buty::$idKategoria . ',
            `' . DB\DBConfig::$tabelaButyKategoria . '`.`' . DB\DBConfig\ButyKategoria::$nazwa . '`,
            `' . DB\DBConfig::$tabelaProducent . '`.`' . DB\DBConfig\Producent::$nazwa . '`,
            `' . DB\DBConfig::$tabelaPlec . '`.`' . DB\DBConfig\Plec::$nazwa . '`
            
            FROM `'.DB\DBConfig::$tabelaButy.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                ON `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idButy.'`
            INNER JOIN `'.DB\DBConfig::$tabelaProducent.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=`'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaPlec.'` 
                ON `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=`'.DB\DBConfig::$tabelaPlec.'`.`'.DB\DBConfig\Plec::$id.'`
            INNER JOIN `'.DB\DBConfig::$tabelaButyKategoria.'` 
                ON `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$idKategoria.'`=`'.DB\DBConfig::$tabelaButyKategoria.'`.`'.DB\DBConfig\ButyKategoria::$id.'`
             
            WHERE `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $boots = $stmt->fetchAll();
            $stmt->closeCursor();


            if($boots && !empty($boots))
            {
                return $boots;
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

    public function getOneType($model, $rozmiar, $kategoria, $producent, $plec){
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$id.'` as IdSprzet,
                                                `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$id.'` as IdButy
                                        FROM 
                                        `'.DB\DBConfig::$tabelaButy.'`
                                         INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                                            ON `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idButy.'`           
             WHERE `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$model.'`=:model AND
                   `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$rozmiar.'` =:rozmiar AND
                   `'.DB\DBConfig::$tabelaButy.'`.`'.DB\DBConfig\Buty::$idKategoria.'`=:kategoria AND
                   `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`=:producent AND
                   `'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idPlec.'`=:plec
             ');


            $stmt->bindValue(':model', $model, PDO::PARAM_STR);
            $stmt->bindValue(':rozmiar', $rozmiar, PDO::PARAM_STR);
            $stmt->bindValue(':kategoria', $kategoria, PDO::PARAM_INT);
            $stmt->bindValue(':producent',$producent, PDO::PARAM_INT);
            $stmt->bindValue(':plec', $plec, PDO::PARAM_INT);


            //d($stmt);

            $result = $stmt->execute();
            $boots = $stmt->fetchAll();
            $stmt->closeCursor();


            if($boots && !empty($boots))
            {
                return $boots;
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

    public function update($indeksy, $boots){
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

                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaButy . '` SET
                    `' . \Config\Database\DBConfig\Buty::$model . '`=:model,
                    `' . \Config\Database\DBConfig\Buty::$idKategoria . '`=:idKategoria,
                    `' . \Config\Database\DBConfig\Buty::$rozmiar . '`=:rozmiar
                
                 WHERE `' . \Config\Database\DBConfig\Buty::$id . '`=:id');

                $stmt->bindValue(':id', $indeks['IdButy'], PDO::PARAM_INT);
                $stmt->bindValue(':model', $boots['model'], PDO::PARAM_STR);
                $stmt->bindValue(':idKategoria', $boots['kategoria'], PDO::PARAM_INT);
                $stmt->bindValue(':rozmiar', $boots['rozmiar'], PDO::PARAM_STR);

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
            $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaButy . '` WHERE  `' . \Config\Database\DBConfig\Buty::$id . '`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            if($result){
                $data['msg'] = "Poprawnie usunięto buty";
            }else{
                $data['error'] = "Wystąpił błąd podczas usuwania butów";
            }
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            //$data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

}