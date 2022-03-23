<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 05.06.2018
 * Time: 01:03
 */

namespace Models;

use \PDO;
use Config\Database as DB;
class Klient extends Model
{
    public function add($imie, $nazwisko, $telefon, $email=null){

        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($imie == null && $nazwisko == null && $telefon == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        try {

            $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaKlient . '`
             (`' . \Config\Database\DBConfig\Klient::$imie . '`
             ,`' . \Config\Database\DBConfig\Klient::$nazwisko . '`
             ,`' . \Config\Database\DBConfig\Klient::$telefon . '`
             ,`' . \Config\Database\DBConfig\Klient::$email . '`
             ,`' . \Config\Database\DBConfig\Klient::$iloscRezerwacji . '`
             ) 
                                           VALUES (:imie, :nazwisko, :telefon, :email, :iloscRezerwacji)');

            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':iloscRezerwacji', 0, PDO::PARAM_INT);

            $result = $stmt->execute();
            $stmt->closeCursor();


            $idKlient = $this->pdo->lastInsertId();
            $idKlient = intval($idKlient);


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $idKlient;

    }

    public function getOne($id){
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
            FROM `'.DB\DBConfig::$tabelaKlient.'` 
            WHERE `'.DB\DBConfig::$tabelaKlient.'`.`'.DB\DBConfig\Klient::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $customer = $stmt->fetchAll();
            $stmt->closeCursor();


            if($customer && !empty($customer))
            {
                return $customer;
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

    public function getOneType($telefon, $email){
        try
        {

            $stmt = $this->pdo->prepare('SELECT *
                                        FROM 
                                        `'.DB\DBConfig::$tabelaKlient.'`         
             WHERE `'.DB\DBConfig::$tabelaKlient.'`.`'.DB\DBConfig\Klient::$telefon.'`=:telefon OR
                   `'.DB\DBConfig::$tabelaKlient.'`.`'.DB\DBConfig\Klient::$email.'` =:email ');


            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);

            $result = $stmt->execute();
            $customer = $stmt->fetchAll();
            $stmt->closeCursor();


            if($customer && !empty($customer))
            {
                return $customer;
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

    public function update($customer){
        $data = array();
        if($this->pdo === null){
            $data['msg'] = "Brak połączenia";
            return $data;
        }
        try	{

            $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaKlient . '` 
            SET
                    `' . \Config\Database\DBConfig\Klient::$imie . '`=:imie,
                    `' . \Config\Database\DBConfig\Klient::$nazwisko . '`=:nazwisko,
                    `' . \Config\Database\DBConfig\Klient::$telefon . '`=:telefon,
                    `' . \Config\Database\DBConfig\Klient::$email . '`=:email
                
                 WHERE `' . \Config\Database\DBConfig\Klient::$id . '`=:id');

            $stmt->bindValue(':id', $customer['id'], PDO::PARAM_INT);
            $stmt->bindValue(':imie', $customer['imie'], PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $customer['nazwisko'], PDO::PARAM_STR);
            $stmt->bindValue(':telefon', $customer['telefon'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $customer['email'], PDO::PARAM_STR);

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

    public function updateReservationAmount($idCustomer, $amountReservation){
        $data = array();
        if($this->pdo === null){
            $data['error'] = "Brak połączenia";
            return $data;
        }
        if($idCustomer === null){
            $data['error'] = "Brak id klienta";
            return $data;
        }
        try	{

            $stmt = $this->pdo->prepare('UPDATE  `' . \Config\Database\DBConfig::$tabelaKlient . '` 
            SET
                    `' . \Config\Database\DBConfig\Klient::$iloscRezerwacji . '`=:iloscRezerwacji
                
                 WHERE `' . \Config\Database\DBConfig\Klient::$id . '`=:id');

            $stmt->bindValue(':id', $idCustomer, PDO::PARAM_INT);
            $stmt->bindValue(':iloscRezerwacji', $amountReservation, PDO::PARAM_INT);

            $result = $stmt->execute();
            $rows = $stmt->rowCount();
            if (!$result)
                $data['error'] = "Brak pasujących wyników";
            else
                $data['msg'] = "OK";
            $stmt->closeCursor();

        }
        catch(\PDOException $e)	{
            $data['error'] = "Błędne zapytanie";
        }
        return $data;
    }

    public function getAssignedToReservation($id){
        try
        {

            $stmt = $this->pdo->prepare('SELECT `'.DB\DBConfig::$tabelaRezerwacja.'`. `'.DB\DBConfig\Rezerwacja::$id.'`
            FROM `'.DB\DBConfig::$tabelaRezerwacja.'` 
            INNER JOIN `'.DB\DBConfig::$tabelaKlient.'` 
                ON `'.DB\DBConfig::$tabelaRezerwacja.'`.`'.DB\DBConfig\Rezerwacja::$idKlient.'`=`'.DB\DBConfig::$tabelaKlient.'`.`'.DB\DBConfig\Klient::$id.'`
             
            WHERE `'.DB\DBConfig::$tabelaKlient.'`.`'.DB\DBConfig\Klient::$id.'`=:id');


            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $customer = $stmt->fetchAll();
            $stmt->closeCursor();


            if($customer && !empty($customer))
            {
                return $customer;
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

    public function delete($id){
        $data = array();

        try {
            $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaKlient . '` WHERE  `' . \Config\Database\DBConfig\Klient::$id . '`=:id');
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

    public function check($imie, $nazwisko, $telefon, $email){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($imie == null && $nazwisko == null && $telefon == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        try {

            $stmt = $this->pdo->prepare('SELECT * FROM  `' . \Config\Database\DBConfig::$tabelaKlient . '`
            WHERE `'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$imie.'`=:imie AND
            `'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$nazwisko.'`=:nazwisko AND 
            `'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$telefon.'`=:telefon AND
            `'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$email.'`=:email
            ');

            $stmt->bindValue(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindValue(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindValue(':telefon', $telefon, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);

            $result = $stmt->execute();
            $Klient = $stmt->fetchAll();
            $stmt->closeCursor();
            if($Klient && !empty($Klient))
                $data['Klient'] = $Klient;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;

    }
    public function getAll(){

        $data= array();
        $data['customer'] = null;
        $data['error'] = null;
        try
        {
            $stmt = $this->pdo->query('SELECT * FROM `'.\Config\Database\DBConfig::$tabelaKlient.'`');
            $customer = $stmt->fetchAll();

            $stmt->closeCursor();



            if($customer && !empty($customer))
            {
                $data['customer'] = $customer;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak klientów';
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

    public function getAllForSelect(){
        $data = $this->getAll();

        $klient = array();
        if(!isset($data['error']))
            $klient[0] = "Wybierz...";
            foreach($data as $item) {
                $klient[$item[\Config\Database\DBConfig\Klient::$id]] = $item[\Config\Database\DBConfig\Klient::$imie].' '.$item[\Config\Database\DBConfig\Klient::$nazwisko];
            }
        return $klient;
    }

}