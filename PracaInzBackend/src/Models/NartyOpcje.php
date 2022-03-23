<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 06.12.2018
 * Time: 21:39
 */

namespace Models;
use \PDO;

use Config\Database as DB;

class NartyOpcje extends Model
{
    public function add($waga, $rozmiar){

        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($waga == null && $rozmiar == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }
        try {

            $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaNartyOpcje . '`
             (`' . \Config\Database\DBConfig\NartyOpcje::$waga . '`
             ,`' . \Config\Database\DBConfig\NartyOpcje::$rozmiar . '`) 
                                           VALUES (:waga, :rozmiar)');

            $stmt->bindValue(':waga', $waga, PDO::PARAM_STR);
            $stmt->bindValue(':rozmiar', $rozmiar, PDO::PARAM_STR);

            $result = $stmt->execute();
            $stmt->closeCursor();


            $id = $this->pdo->lastInsertId();
            $id = intval($id);


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $id;

    }

    public function check($waga, $rozmiar){
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

            $stmt = $this->pdo->prepare('SELECT * FROM  `' . \Config\Database\DBConfig::$tabelaNartyOpcje . '`
            WHERE `'.\Config\Database\DBConfig::$tabelaNartyOpcje.'`.`'.\Config\Database\DBConfig\NartyOpcje::$waga.'`=:waga AND
            `'.\Config\Database\DBConfig::$tabelaNartyOpcje.'`.`'.\Config\Database\DBConfig\NartyOpcje::$rozmiar.'`=:rozmiar
            ');

            $stmt->bindValue(':waga', $waga, PDO::PARAM_STR);
            $stmt->bindValue(':rozmiar', $rozmiar, PDO::PARAM_STR);

            $result = $stmt->execute();
            $skisOptions = $stmt->fetchAll();
            $stmt->closeCursor();
            if($skisOptions && !empty($skisOptions))
                $data['skisOptions'] = $skisOptions;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;

    }
}