<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 22.11.2018
 * Time: 20:38
 */

namespace Models;
use \PDO;

use Config\Database as DB;

class Pracownik extends Model
{
    public function getAll(){

        try
        {
            $stmt = $this->pdo->query('SELECT 
            `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$id.'`,
            `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$imie.'`,
            `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$nazwisko.'`,
            `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$telefon.'`,
            `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$email.'`,
            `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$aktywny.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$nazwa.'`,
            `'.DB\DBConfig::$tabelaPrawo.'`.`'.DB\DBConfig\Prawo::$nazwa.'`

             FROM `'.DB\DBConfig::$tabelaPracownik.'` 
             INNER JOIN `'.DB\DBConfig::$tabelaUzytkownik.'` 
                ON `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$idPracownik.'`=`'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$id.'`
             INNER JOIN `'.DB\DBConfig::$tabelaPrawo.'` 
                ON `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$idPrawo.'`=`'.DB\DBConfig::$tabelaPrawo.'`.`'.DB\DBConfig\Prawo::$id.'`
             ORDER BY `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$nazwisko.'`
             ');


            $employee = $stmt->fetchAll();

            $stmt->closeCursor();



            if($employee && !empty($employee))
            {
                $data['employee'] = $employee;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak pracownikow';
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

            $stmt = $this->pdo->prepare('SELECT *
            FROM `'.DB\DBConfig::$tabelaPracownik.'` 
            WHERE `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $employee = $stmt->fetchAll();
            $stmt->closeCursor();


            if($employee && !empty($employee))
            {
                return $employee;
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

    public function getByUsername($username){
        $data = array();
        $data['error'] = null;
        $data['user'] = null;

        if($username == null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
        }

        try
        {

            $stmt = $this->pdo->prepare('SELECT *
            FROM `'.DB\DBConfig::$tabelaPracownik.'`
            INNER JOIN  `'.DB\DBConfig::$tabelaUzytkownik.'`
            ON `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$id.'` = `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$idPracownik.'`
            WHERE `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$nazwa.'`=:username');


            $stmt->bindValue(':username', $username, PDO::PARAM_INT);
            $result = $stmt->execute();
            $employee = $stmt->fetchAll();
            $stmt->closeCursor();


            if($employee && !empty($employee))
            {
                $data['user'] = $employee;
                return $data;
            }

            return $data;

        }
        catch(\PDOException $e)
        {
            $data['error'] = 'Połączenie z bazą nie powidoło się!';
        }
    }

    public function getOneType($telefon, $email){
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
                                        FROM 
                                        `'.DB\DBConfig::$tabelaPracownik.'`         
             WHERE `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$telefon.'`=:telefon OR
                   `'.DB\DBConfig::$tabelaPracownik.'`.`'.DB\DBConfig\Pracownik::$email.'` =:email ');


            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);

            $result = $stmt->execute();
            $employee = $stmt->fetchAll();
            $stmt->closeCursor();


            if($employee && !empty($employee))
            {
                return $employee;
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

    public function add($imie, $nazwisko, $telefon, $email){

        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($imie == null && $nazwisko == null && $telefon == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        try {

            $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaPracownik . '`
             (`' . \Config\Database\DBConfig\Pracownik::$imie . '`
             ,`' . \Config\Database\DBConfig\Pracownik::$nazwisko . '`
             ,`' . \Config\Database\DBConfig\Pracownik::$telefon . '`
             ,`' . \Config\Database\DBConfig\Pracownik::$email . '`
             ,`' . \Config\Database\DBConfig\Pracownik::$aktywny . '`) 
                                           VALUES (:imie, :nazwisko, :telefon, :email, :aktywny)');

            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':aktywny', true, PDO::PARAM_BOOL);

            $result = $stmt->execute();
            $stmt->closeCursor();


            $idEmployee = $this->pdo->lastInsertId();
            $idEmployee = intval($idEmployee);


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $idEmployee;

    }

    public function update($id, $imie, $nazwisko, $telefon, $email, $aktywny){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        try	{

            $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaPracownik . '` 
            SET
                    `' . \Config\Database\DBConfig\Pracownik::$imie . '`=:imie,
                    `' . \Config\Database\DBConfig\Pracownik::$nazwisko . '`=:nazwisko,
                    `' . \Config\Database\DBConfig\Pracownik::$telefon . '`=:telefon,
                    `' . \Config\Database\DBConfig\Pracownik::$email . '`=:email,
                    `' . \Config\Database\DBConfig\Pracownik::$aktywny . '`=:aktywny
                
                 WHERE `' . \Config\Database\DBConfig\Pracownik::$id . '`=:id');

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':telefon',$telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':aktywny', $aktywny, PDO::PARAM_BOOL);

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