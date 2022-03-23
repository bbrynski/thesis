<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 22.11.2018
 * Time: 21:54
 */

namespace Models;
use \PDO;

use Config\Database as DB;

class Uzytkownik extends Model
{
    public function getAll(){

        try
        {
            $stmt = $this->pdo->query('SELECT 
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$id.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$imie.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$nazwisko.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$telefon.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$email.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$aktywny.'`,
            `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$nazwa.'`,
            `'.DB\DBConfig::$tabelaPrawo.'`.`'.DB\DBConfig\Prawo::$nazwa.'`

             FROM `'.DB\DBConfig::$tabelaUzytkownik.'` 
             INNER JOIN `'.DB\DBConfig::$tabelaPrawo.'` 
                ON `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$idPrawo.'`=`'.DB\DBConfig::$tabelaPrawo.'`.`'.DB\DBConfig\Prawo::$id.'`
             ORDER BY `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$nazwisko.'`
             ');


            $user = $stmt->fetchAll();

            $stmt->closeCursor();



            if($user && !empty($user))
            {
                $data['user'] = $user;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak uzytkownikow';
                return $data;
                //$data['categories'] = array();
                //$data['msg'] = 'Brak kategorii do wyświetlenia';
            }

        }
        catch(\PDOException $e)
        {
            $data['error'] = 'Połączenie z bazą nie powidoło się!';
        }
    }

    public function getOne($id){
        $data = array();
        $data['user'] = null;
        $data['error'] = null;
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
            FROM `'.DB\DBConfig::$tabelaUzytkownik.'` 
            WHERE `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $user = $stmt->fetchAll();
            $stmt->closeCursor();


            if($user && !empty($user))
            {
                $data['user'] = $user;
                return $data;
            }
            else
            {

                $data['error'] = 'Brak uzytkownika';
                return $data;
                //$data['categories'] = array();
                //$data['msg'] = 'Brak kategorii do wyświetlenia';
            }

        }
        catch(\PDOException $e)
        {
            $data['error'] = 'Połączenie z bazą nie powidoło się!';
            return $data;
        }
    }

    public function getOneType($telefon, $email){
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
                                        FROM 
                                        `'.DB\DBConfig::$tabelaUzytkownik.'`         
             WHERE `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$telefon.'`=:telefon OR
                   `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$email.'` =:email ');


            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);

            $result = $stmt->execute();
            $user = $stmt->fetchAll();
            $stmt->closeCursor();


            if($user && !empty($user))
            {
                return $user;
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

    public function getOneUsername($nazwaUzytkownik){
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
                                        FROM 
                                        `'.DB\DBConfig::$tabelaUzytkownik.'`         
             WHERE `'.DB\DBConfig::$tabelaUzytkownik.'`.`'.DB\DBConfig\Uzytkownik::$nazwa.'`=:nazwaUzytkownik');


            $stmt->bindValue(':nazwaUzytkownik', $nazwaUzytkownik, PDO::PARAM_STR);

            $result = $stmt->execute();
            $user = $stmt->fetchAll();
            $stmt->closeCursor();


            if($user && !empty($user))
            {
                return $user;
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

    public function add($imie, $nazwisko, $telefon, $email, $nazwaUzytkownik, $haslo, $idPrawo){

        $data = array();
        $data['error'] = null;
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($imie == null && $nazwisko == null && $telefon == null && $email == null && $nazwaUzytkownik == null && $haslo == null && $idPrawo == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        try {

            $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaUzytkownik . '`
             (`' . \Config\Database\DBConfig\Uzytkownik::$imie . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$nazwisko . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$telefon . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$email . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$nazwa . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$haslo . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$idPrawo . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$aktywny . '`) 
                                           VALUES (:imie, :nazwisko, :telefon, :email, :nazwaUzytkownik, :haslo, :idPrawo, :aktywny)');

            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':nazwaUzytkownik', $nazwaUzytkownik, PDO::PARAM_STR);
            $stmt->bindValue(':haslo', password_hash($haslo, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $stmt->bindValue(':idPrawo', $idPrawo, PDO::PARAM_INT);
            $stmt->bindValue(':aktywny', true, PDO::PARAM_BOOL);

            $result = $stmt->execute();
            $stmt->closeCursor();


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function update($id, $imie, $nazwisko, $telefon, $email,$nazwaUzytkownik, $haslo, $idPrawo, $aktywny){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        try	{

            $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaUzytkownik . '` 
            SET
                    `' . \Config\Database\DBConfig\Uzytkownik::$imie . '`=:imie,
                    `' . \Config\Database\DBConfig\Uzytkownik::$nazwisko . '`=:nazwisko,
                    `' . \Config\Database\DBConfig\Uzytkownik::$telefon . '`=:telefon,
                    `' . \Config\Database\DBConfig\Uzytkownik::$email . '`=:email,
                    `' . \Config\Database\DBConfig\Uzytkownik::$nazwa . '`=:nazwaUzytkownik,
                    `' . \Config\Database\DBConfig\Uzytkownik::$haslo . '`=:haslo,
                    `' . \Config\Database\DBConfig\Uzytkownik::$idPrawo . '`=:idPrawo,
                    `' . \Config\Database\DBConfig\Uzytkownik::$aktywny . '`=:aktywny
                
                 WHERE `' . \Config\Database\DBConfig\Uzytkownik::$id . '`=:id');

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':nazwaUzytkownik', $nazwaUzytkownik, PDO::PARAM_STR);
            $stmt->bindValue(':haslo', password_hash($haslo, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $stmt->bindValue(':idPrawo', $idPrawo, PDO::PARAM_INT);
            $stmt->bindValue(':aktywny', $aktywny, PDO::PARAM_BOOL);

            $result = $stmt->execute();
            $rows = $stmt->rowCount();
            if (!$result)
                $data['error'] = "Brak pasujących wyników";
            else
                $data['msg'] = "Zaktualizowano uzytkownika";
            $stmt->closeCursor();

        }
        catch(\PDOException $e)	{
            $data['error'] = "Błędne zapytanie";
        }
        return $data;
    }

    /*********************************************************************/



    public function add2($nazwaUzytkownik, $haslo, $idPracownik, $idPrawo){

        $idUser = array();

            try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaUzytkownik . '`
             (`' . \Config\Database\DBConfig\Uzytkownik::$nazwa . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$haslo . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$idPracownik . '`
             ,`' . \Config\Database\DBConfig\Uzytkownik::$idPrawo . '`) 
                                           VALUES (:nazwaUzytkownik, :haslo, :idPracownik, :idPrawo)');

                $stmt->bindValue(':nazwaUzytkownik', $nazwaUzytkownik, PDO::PARAM_STR);
                $stmt->bindValue(':haslo', md5($haslo), PDO::PARAM_STR);
                $stmt->bindValue(':idPracownik', $idPracownik, PDO::PARAM_INT);
                $stmt->bindValue(':idPrawo', $idPrawo, PDO::PARAM_INT);

                $result = $stmt->execute();


                //$id = $this->pdo->lastInsertId();
                //$idUser[] = intval($id);


            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }

        return $idUser;

    }

}