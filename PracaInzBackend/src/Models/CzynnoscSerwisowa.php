<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 06.01.2019
 * Time: 17:04
 */

namespace Models;

use \PDO;
class CzynnoscSerwisowa extends Model
{
    public function add($idCzynnosc, $data, $opis){
        if($this->pdo === null){
            $response['error'] = \Config\Database\DBErrorName::$connection;
            return $response;
        }
        if($idCzynnosc === null && $data === null && $opis === null ){
            $response['error'] = \Config\Database\DBErrorName::$empty;
            return $response;
        }
        $response = array();

            try {

                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaCzynnoscSerwisowa . '`
             (`' . \Config\Database\DBConfig\CzynnoscSerwisowa::$idCzynnosc . '`
             ,`' . \Config\Database\DBConfig\CzynnoscSerwisowa::$data . '`
             ,`' . \Config\Database\DBConfig\CzynnoscSerwisowa::$opis . '`) 
                                           VALUES (:idCzynnosc, :data, :opis)');

                $stmt->bindValue(':idCzynnosc', $idCzynnosc, PDO::PARAM_INT);
                $stmt->bindValue(':data', $data, PDO::PARAM_STR);
                $stmt->bindValue(':opis', $opis, PDO::PARAM_STR);

                $result = $stmt->execute();

                if(!$result) {
                    $response['error'] = 'Nie udało sie dodać sprzętu';
                } else {
                    $response['msg'] = 'OK';
                    $id = $this->pdo->lastInsertId();
                    $response['idWstawione'] = intval($id);
                }
                $stmt->closeCursor();

            } catch (\PDOException $e) {
                $response['error'] = \Config\Database\DBErrorName::$query;
            }
        return $response;
    }

}