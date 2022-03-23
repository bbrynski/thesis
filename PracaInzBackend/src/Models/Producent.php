<?php

namespace Models;
use \PDO;
use Config\Database as DB;
class Producent extends Model
{
    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaProducent.'`');

            $producent = $stmt->fetchAll();
            $stmt->closeCursor();
            if($producent && !empty($producent))
            {
                return $producent;
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
        return $data;
    }


    public function getAllForSelect(){
        $data = $this->getAll();

        $producent = array();
        if(!isset($data['error']))
            foreach($data as $item) {
                $producent[$item[\Config\Database\DBConfig\Producent::$id]] = $item[\Config\Database\DBConfig\Producent::$nazwa];
            }
        return $producent;
    }

    public function getOneType($nazwa){
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'` as IdProducent
                                        FROM 
                                        `'.DB\DBConfig::$tabelaProducent.'`
                                                   
             WHERE `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$nazwa.'`=:nazwa
             ');


            $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);

            //d($stmt);

            $result = $stmt->execute();
            $producer = $stmt->fetchAll();
            $stmt->closeCursor();


            if($producer && !empty($producer))
            {
                return $producer;
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

    public function add($nazwa){

        $data = array();
            try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaProducent . '`
             (`' . \Config\Database\DBConfig\Producent::$nazwa . '`) 
                                           VALUES (:nazwa)');

                $stmt->bindValue(':nazwa', $nazwa, PDO::PARAM_STR);
                $result = $stmt->execute();


            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }


        return $data;

    }

    public function getOne($id){
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
            FROM `'.DB\DBConfig::$tabelaProducent.'` 
            WHERE `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $producer = $stmt->fetchAll();
            $stmt->closeCursor();


            if($producer && !empty($producer))
            {
                return $producer;
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


    public function getAssignedToEquipment($id){
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaProducent.'`. `'.DB\DBConfig\Producent::$id.'`
            FROM `'.DB\DBConfig::$tabelaProducent.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaSprzet.'` 
                ON `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`=`'.DB\DBConfig::$tabelaSprzet.'`.`'.DB\DBConfig\Sprzet::$idProducent.'`
             
            WHERE `'.DB\DBConfig::$tabelaProducent.'`.`'.DB\DBConfig\Producent::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $producer = $stmt->fetchAll();
            $stmt->closeCursor();


            if($producer && !empty($producer))
            {
                return $producer;
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

    public function update($producer){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        try	{

                $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaProducent . '` SET
                    `' . \Config\Database\DBConfig\Producent::$nazwa . '`=:nazwa
                
                 WHERE `' . \Config\Database\DBConfig\Producent::$id . '`=:id');

                $stmt->bindValue(':id', $producer['id'], PDO::PARAM_INT);
                $stmt->bindValue(':nazwa', $producer['nazwa'], PDO::PARAM_STR);

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
            $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaProducent . '` WHERE  `' . \Config\Database\DBConfig\Producent::$id . '`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            if($result){
                $data['msg'] = "Poprawnie usunięto producenta";
            }else{
                $data['error'] = "Wystąpił błąd podczas usuwania producenta";
            }
            $stmt->closeCursor();
        } catch (\PDOException $e) {
            //$data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

}