<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 03.06.2018
 * Time: 23:06
 */

namespace Models;

use \PDO;
use Config\Database as DB;
class Wypozyczenie extends Model
{
    public function getAll($status){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        $data = null;
        try {
        if($status == "active"){

                $stmt = $this->pdo->query('SELECT
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$id . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataDo . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$cena . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$imie . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$nazwisko . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$email . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$telefon . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '` as IdSprzet,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`,
            `' . DB\DBConfig::$tabelaStatus . '`.`' . DB\DBConfig\Status::$nazwa . '`,
            `' . DB\DBConfig::$tabelaStatus . '`.`' . DB\DBConfig\Status::$id . '` as IdStatus
 
            FROM `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`
             INNER JOIN `'.\Config\Database\DBConfig::$tabelaSprzet.'` 
                ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idSprzet.'`=`'.\Config\Database\DBConfig::$tabelaSprzet.'`.`'.\Config\Database\DBConfig\Sprzet::$id.'`
                INNER JOIN `'.\Config\Database\DBConfig::$tabelaKlient.'` 
                    ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idKlient.'`=`'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$id.'`
                    INNER JOIN `'.\Config\Database\DBConfig::$tabelaStatus.'` 
                    ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idStatus.'`=`'.\Config\Database\DBConfig::$tabelaStatus.'`.`'.\Config\Database\DBConfig\Status::$id.'`
             WHERE `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idStatus.'` =3
             ORDER BY `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$dataDo.'` ASC
             ');
        } else {
            $stmt = $this->pdo->query('SELECT
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$id . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataOd . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataDo . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$dataZwrot . '`,
            `' . DB\DBConfig::$tabelaWypozyczenie . '`.`' . DB\DBConfig\Wypozyczenie::$cena . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$imie . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$nazwisko . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$email . '`,
            `' . DB\DBConfig::$tabelaKlient . '`.`' . DB\DBConfig\Klient::$telefon . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$id . '` as IdSprzet,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idSnowboard . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idNarty . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idKijki . '`,
            `' . DB\DBConfig::$tabelaSprzet . '`.`' . DB\DBConfig\Sprzet::$idButy . '`,
            `' . DB\DBConfig::$tabelaStatus . '`.`' . DB\DBConfig\Status::$nazwa . '`,
            `' . DB\DBConfig::$tabelaStatus . '`.`' . DB\DBConfig\Status::$id . '` as IdStatus
 
            FROM `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`
             INNER JOIN `'.\Config\Database\DBConfig::$tabelaSprzet.'` 
                ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idSprzet.'`=`'.\Config\Database\DBConfig::$tabelaSprzet.'`.`'.\Config\Database\DBConfig\Sprzet::$id.'`
                INNER JOIN `'.\Config\Database\DBConfig::$tabelaKlient.'` 
                    ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idKlient.'`=`'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$id.'`
                    INNER JOIN `'.\Config\Database\DBConfig::$tabelaStatus.'` 
                    ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idStatus.'`=`'.\Config\Database\DBConfig::$tabelaStatus.'`.`'.\Config\Database\DBConfig\Status::$id.'`
             WHERE `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idStatus.'` =4
             ORDER BY `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$dataDo.'` ASC
             ');
        }



            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['rentals'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function add($idSprzet, $idKlient, $idUzytkownik, $cena, $dataOd, $dataDo){
        $data['error']=null;

        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($idKlient == null && $dataOd == null && $dataDo == null && $idSprzet == null && $idUzytkownik == null && $cena == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }

        try {
                $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaWypozyczenie . '`
             (`' . \Config\Database\DBConfig\Wypozyczenie::$idSprzet. '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$idKlient . '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$idUzytkownik . '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$cena . '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$dataOd . '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$dataDo . '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$dataZwrot . '`
             ,`' . \Config\Database\DBConfig\Wypozyczenie::$idStatus . '`
             
             ) 
                                           VALUES (:idSprzet, :idKlient, :idUzytkownik, :cena, :dataOd, :dataDo, :dataZwrot, :idStatus)');

                $stmt->bindValue(':idSprzet', $idSprzet, PDO::PARAM_INT);
                $stmt->bindValue(':idKlient', $idKlient, PDO::PARAM_INT);
                $stmt->bindValue(':idUzytkownik', $idUzytkownik, PDO::PARAM_INT);
                $stmt->bindValue(':cena', $cena, PDO::PARAM_INT);
                $stmt->bindValue(':dataOd', $dataOd, PDO::PARAM_STR);
                $stmt->bindValue(':dataDo', $dataDo, PDO::PARAM_STR);
                $stmt->bindValue(':dataZwrot', null, PDO::PARAM_STR);
                $stmt->bindValue(':idStatus',3, PDO::PARAM_INT);

                $result = $stmt->execute();


        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    public function getOne($id){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($id === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = array();
        $data['wypozyczenie'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'` 
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaSprzet.'`
            ON `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$idSprzet.'`=`'.\Config\Database\DBConfig::$tabelaSprzet.'`.`'.\Config\Database\DBConfig\Sprzet::$id.'`
            WHERE  `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'`.`'.\Config\Database\DBConfig\Wypozyczenie::$id.'`=:id');

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            $result = $stmt->execute();
            $wypozyczenie = $stmt->fetchAll();
            $stmt->closeCursor();
            if($wypozyczenie && !empty($wypozyczenie))
                $data['wypozyczenie'] = $wypozyczenie;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    public function changeStatus($idRent, $returnDate){
        $data = array();
        $data['error'] = null;
        $data['msg'] = null;

        //id statusu zakonczone
        $idStatus = 4;
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idRent == null){
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }

        try	{
            $stmt = $this->pdo->prepare('UPDATE  `'.\Config\Database\DBConfig::$tabelaWypozyczenie.'` 
            SET
                    
                    `' .\Config\Database\DBConfig\Wypozyczenie::$idStatus.'`=:status,
                    `' .\Config\Database\DBConfig\Wypozyczenie::$dataZwrot.'`=:dataZwrot
                
                WHERE `' .\Config\Database\DBConfig\Wypozyczenie::$id.'`=:id');

            $stmt->bindValue(':id', $idRent, PDO::PARAM_INT);
            $stmt->bindValue(':status',$idStatus, PDO::PARAM_INT);
            $stmt->bindValue(':dataZwrot',$returnDate, PDO::PARAM_STR);

            $result = $stmt->execute();

            if(!$result)
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
            else
                $data['msg'] = "Sprzęt zwrócony";
            $stmt->closeCursor();
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

}