<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 29.11.2018
 * Time: 01:38
 */

namespace Models;
use \PDO;

use Config\Database as DB;

class SnowboardOpcje extends Model
{
    public function add($idUstawienie, $katL, $katP){

        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($idUstawienie == null && $katL == null && $katP == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        try {

            $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaSnowboardOpcje . '`
             (`' . \Config\Database\DBConfig\SnowboardOpcje::$idUstawienie . '`
             ,`' . \Config\Database\DBConfig\SnowboardOpcje::$KatLewa . '`
             ,`' . \Config\Database\DBConfig\SnowboardOpcje::$KatPrawa . '`) 
                                           VALUES (:idUstawienie, :katL, :katP)');

            $stmt->bindValue(':idUstawienie', $idUstawienie, PDO::PARAM_STR);
            $stmt->bindValue(':katL', $katL, PDO::PARAM_STR);
            $stmt->bindValue(':katP', $katP, PDO::PARAM_STR);

            $result = $stmt->execute();
            $stmt->closeCursor();


            $id = $this->pdo->lastInsertId();
            $id = intval($id);


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $id;

    }

    public function check($idUstawienie, $katL, $katP){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        /*
        if ($imie == null && $nazwisko == null && $telefon == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }*/
        try {

            $stmt = $this->pdo->prepare('SELECT * FROM  `' . \Config\Database\DBConfig::$tabelaSnowboardOpcje . '`
            WHERE `'.\Config\Database\DBConfig::$tabelaSnowboardOpcje.'`.`'.\Config\Database\DBConfig\SnowboardOpcje::$idUstawienie.'`=:idUstawienie AND
            `'.\Config\Database\DBConfig::$tabelaSnowboardOpcje.'`.`'.\Config\Database\DBConfig\SnowboardOpcje::$KatPrawa.'`=:katP AND 
            `'.\Config\Database\DBConfig::$tabelaSnowboardOpcje.'`.`'.\Config\Database\DBConfig\SnowboardOpcje::$KatLewa.'`=:katL
            ');

            $stmt->bindValue(':idUstawienie', $idUstawienie, PDO::PARAM_INT);
            $stmt->bindValue(':katP', $katP, PDO::PARAM_INT);
            $stmt->bindValue(':katL', $katL, PDO::PARAM_INT);

            $data['sql'] =$stmt;
            $result = $stmt->execute();
            $snowboardOptions = $stmt->fetchAll();
            $stmt->closeCursor();
            if($snowboardOptions && !empty($snowboardOptions))
                $data['snowboardOptions'] = $snowboardOptions;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;

    }



}