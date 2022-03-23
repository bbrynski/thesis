<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 06.01.2019
 * Time: 17:22
 */

namespace Models;

use \PDO;

class HistoriaSerwisowa extends Model
{
    public function add($idCzynnosc, $idSprzet, $idUzytkownik){
        if($this->pdo === null){
            $response['error'] = \Config\Database\DBErrorName::$connection;
            return $response;
        }
        if($idCzynnosc === null && $idSprzet === null && $idUzytkownik === null ){
            $response['error'] = \Config\Database\DBErrorName::$empty;
            return $response;
        }
        $response = array();

        try {

            $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaHistoriaSerwisowa . '`
             (`' . \Config\Database\DBConfig\HistoriaSerwisowa::$idCzynnoscSerwisowa . '`
             ,`' . \Config\Database\DBConfig\HistoriaSerwisowa::$idSprzet . '`
             ,`' . \Config\Database\DBConfig\HistoriaSerwisowa::$idUzytkownik . '`) 
                                           VALUES (:idCzynnosc, :idSprzet, :idUzytkownik)');

            $stmt->bindValue(':idCzynnosc', $idCzynnosc, PDO::PARAM_INT);
            $stmt->bindValue(':idSprzet', $idSprzet, PDO::PARAM_INT);
            $stmt->bindValue(':idUzytkownik', $idUzytkownik, PDO::PARAM_INT);

            $result = $stmt->execute();

            if(!$result) {
                $response['error'] = 'Nie udało sie dodać';
            } else {
                $response['msg'] = 'OK';
            }
            $stmt->closeCursor();

        } catch (\PDOException $e) {
            $response['error'] = \Config\Database\DBErrorName::$query;
        }
        return $response;
    }

    public function getOneServiceEquipmentHistory($id){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($id === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = array();
        $data['history'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaHistoriaSerwisowa.'`
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaCzynnoscSerwisowa.'`
                ON `'.\Config\Database\DBConfig::$tabelaHistoriaSerwisowa.'`.`'.\Config\Database\DBConfig\HistoriaSerwisowa::$idCzynnoscSerwisowa.'`=`'.\Config\Database\DBConfig::$tabelaCzynnoscSerwisowa.'`.`'.\Config\Database\DBConfig\CzynnoscSerwisowa::$id.'` 
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaCzynnoscTyp.'`
                ON `'.\Config\Database\DBConfig::$tabelaCzynnoscSerwisowa.'`.`'.\Config\Database\DBConfig\CzynnoscSerwisowa::$idCzynnosc.'`=`'.\Config\Database\DBConfig::$tabelaCzynnoscTyp.'`.`'.\Config\Database\DBConfig\CzynnoscTyp::$id.'`
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaUzytkownik.'`
                ON `'.\Config\Database\DBConfig::$tabelaHistoriaSerwisowa.'`.`'.\Config\Database\DBConfig\HistoriaSerwisowa::$idUzytkownik.'`=`'.\Config\Database\DBConfig::$tabelaUzytkownik.'`.`'.\Config\Database\DBConfig\Uzytkownik::$id.'`
            WHERE  `'.\Config\Database\DBConfig::$tabelaHistoriaSerwisowa.'`.`'.\Config\Database\DBConfig\HistoriaSerwisowa::$idSprzet.'`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            $history = $stmt->fetchAll();
            $stmt->closeCursor();
            if($history && !empty($history))
                $data['history'] = $history;
            else
                $data['error'] = "Brak historii serwisowej";
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }
}