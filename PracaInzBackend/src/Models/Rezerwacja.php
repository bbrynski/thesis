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

class Rezerwacja extends Model
{
    public function add($IdKlient, $dataOd, $dataDo, $IdSprzet)
    {

        $data=null;

        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }

        if ($IdKlient == null && $dataOd == null && $dataDo == null) {
            $data['error'] = \Config\Database\DBErrorName::$empty;
            return $data;
        }

        try {
            //dodanie rezerwacji
            foreach ($IdSprzet as $item) {
                $snowboardOpcje = null;
                $nartyOpcje = null;
                if(isset($item['snowboard']['options'])){
                    $snowboardOpcje = $item['snowboard']['options'];
                    $id = $item['snowboard']['id'];
                }
                if(isset($item['skis']['options'])){
                    $nartyOpcje = $item['skis']['options'];
                    $id = $item['skis']['id'];
                }
                if(isset($item['boots'])){
                    $id = $item['boots'];
                }
                if(isset($item['skiPoles'])){
                    $id = $item['skiPoles'];
                }

                    $stmt = $this->pdo->prepare('INSERT INTO `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             (`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`
             ,`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`
             ,`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`
             ,`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`
             ,`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`
             ,`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`
             ,`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`
             
             ) 
                                           VALUES (:idSprzet, :idKlient, :dataOd, :dataDo, :idStatus, :idSnowboardOpcje, :idNartyOpcje)');

                    $stmt->bindValue(':idSprzet', $id, PDO::PARAM_INT);
                    $stmt->bindValue(':idKlient', $IdKlient, PDO::PARAM_INT);
                    $stmt->bindValue(':dataOd', $dataOd, PDO::PARAM_STR);
                    $stmt->bindValue(':dataDo', $dataDo, PDO::PARAM_STR);
                    $stmt->bindValue(':idStatus', 1, PDO::PARAM_INT);
                    $stmt->bindValue(':idSnowboardOpcje', $snowboardOpcje, PDO::PARAM_INT);
                    $stmt->bindValue(':idNartyOpcje', $nartyOpcje, PDO::PARAM_INT);

                    $result = $stmt->execute();

            }

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function checkDate($OdZ,$DoZ, $OdR, $DoR){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        $data=null;
        $OdZarezerwowana = strtotime($OdZ);
        $DoZarezerwowana = strtotime($DoZ);
        $OdRezerwacja = strtotime($OdR);
        $DoRezerwacja = strtotime($DoR);

        //jesli rezerwowana wczesniejsza -> mozna rezerwowac (1)
        if($OdRezerwacja < $OdZarezerwowana && $OdRezerwacja < $DoZarezerwowana && $DoRezerwacja < $OdZarezerwowana && $DoRezerwacja < $DoZarezerwowana){
            $data['msg'] = true;
            $data['daty'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        }  if($OdRezerwacja > $OdZarezerwowana && $OdRezerwacja > $DoZarezerwowana && $DoRezerwacja > $OdZarezerwowana && $DoRezerwacja > $DoZarezerwowana){
            //jesli pozniejsza niz zarezerwowana -> mozna rezerwowac (2)
            $data['msg'] = true;
            $data['daty'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        } else if($OdRezerwacja < $OdZarezerwowana && $OdRezerwacja < $DoZarezerwowana && $DoRezerwacja > $OdZarezerwowana && $DoRezerwacja < $DoZarezerwowana){
            //rezerwowana nachodzi z lewej -> nie rezerwuj (3)
            $data['msg'] = false;
            $data['zajete'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        }else if($OdRezerwacja > $OdZarezerwowana && $OdRezerwacja < $DoZarezerwowana && $DoRezerwacja > $OdZarezerwowana && $DoRezerwacja > $DoZarezerwowana){
            //rezerwacja nachodzi z prawej -> nie rezerwuj (4)
            $data['msg'] = false;
            $data['zajete'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        }else if($OdRezerwacja > $OdZarezerwowana && $OdRezerwacja < $DoZarezerwowana && $DoRezerwacja > $OdZarezerwowana && $DoRezerwacja < $DoZarezerwowana){
            //rezerwacja wewnatrz zarezerwowanego -> nie rezerwuj (5)
            $data['msg'] = false;
            $data['zajete'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        }else if($OdRezerwacja < $OdZarezerwowana && $OdRezerwacja < $DoZarezerwowana && $DoRezerwacja > $OdZarezerwowana && $DoRezerwacja > $DoZarezerwowana){
            //rezerwacja obejmuje zarezerwowany (6)
            $data['msg'] = false;
            $data['zajete'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        }else if($OdRezerwacja == $OdZarezerwowana || $OdRezerwacja == $DoZarezerwowana || $DoRezerwacja == $OdZarezerwowana || $DoRezerwacja == $DoZarezerwowana){
            //jesli ktoras data pokrywa sie z zarezerwowanym -> nie rezerwuj (7)
            $data['msg'] = false;
            $data['zajete'][]=array(
                "DataOd" => $OdZ,
                "DataDo" => $DoZ
            );
        }


        /* stare

                $Od = strtotime($dataOd);
                $Do = strtotime($dataDo);

                $dataOdR = strtotime($OdR);
                $dataDoR = strtotime($DoR);
                //$dataOdR = strtotime($rezerwacja[0]['DataOd']);
                //$dataDoR = strtotime($rezerwacja[0]['DataDo']);
        $data['OdR'] = $OdR;
        $data['DoR'] = $DoR;
        $data['Od'] = $dataOd;
        $data['Do'] = $dataDo;

                if ($dataOdR < $Od && $dataOdR < $Do && $dataDoR < $Od && $dataDoR <$Do ) {
                    $data['msg'] = true;
                    $data['if1'] = "if1";
                }else if($dataOdR > $Od && $dataOdR > $Do && $dataDoR > $Od && $dataDoR > $Do) {
                    $data['msg'] = true;
                    $data['if2'] = "if2";
                }else if(!($dataOdR >= $Od && $dataOdR <= $Do && $dataDoR >= $Od && $dataDoR >= $Do)){
                    $data['zajete'][]=array(
                        //"IdSprzet" => $rezerwacja[0]['IdSprzet'],
                        "DataOd" => $OdR,
                        "DataDo" => $DoR
                    );
                }else if (!($dataOdR < $Od && $dataOdR < $Do && $dataDoR > $Od && $dataDoR < $Do)){
                    $data['zajete'][]=array(
                        //"IdSprzet" => $rezerwacja[0]['IdSprzet'],
                        "DataOd" => $OdR,
                        "DataDo" => $DoR
                    );
                }else if (!($dataOdR < $Od && $dataOdR < $Do && $dataDoR > $Od && $dataDoR > $Do)){
                    $data['zajete'][]=array(
                        //"IdSprzet" => $rezerwacja[0]['IdSprzet'],
                        "DataOd" => $OdR,
                        "DataDo" => $DoR
                    );
                }else if (!($dataOdR > $Od && $dataOdR < $Do && $dataDoR > $Od && $dataDoR < $Do)) {
                    $data['zajete'][] = array(
                        //"IdSprzet" => $rezerwacja[0]['IdSprzet'],
                        "DataOd" => $OdR,
                        "DataDo" => $DoR
                    );
                }
                */
        return $data;
    }

    public function getAll($Od=null, $Do=null, $status){
        if ($this->pdo === null) {
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        $data=null;
        $data['reservations'] = null;

        try {

    if($Od === null && $Do === null && $status == 'all'){
        $stmt = $this->pdo->prepare('SELECT 
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$id . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '` as IdKlient,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$imie . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$nazwisko . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idButy . '`,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '` as IdStatus,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$nazwa . '`
             
             
             FROM `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             INNER JOIN `' . \Config\Database\DBConfig::$tabelaSprzet . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`=`' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$id . '`
                INNER JOIN `' . \Config\Database\DBConfig::$tabelaKlient . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`=`' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '`
                    INNER JOIN `' . \Config\Database\DBConfig::$tabelaStatus . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`=`' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '`
             ORDER BY `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` ASC, 
             `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` ASC      
             ');
    } else if ($Od === null && $Do === null && $status == 'ended'){
        $stmt = $this->pdo->prepare('SELECT 
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$id . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`,
                        `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '` as IdKlient,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$imie . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$nazwisko . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idButy . '`,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '` as IdStatus,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$nazwa . '`
             
             
             FROM `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             INNER JOIN `' . \Config\Database\DBConfig::$tabelaSprzet . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`=`' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$id . '`
                INNER JOIN `' . \Config\Database\DBConfig::$tabelaKlient . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`=`' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '`
                    INNER JOIN `' . \Config\Database\DBConfig::$tabelaStatus . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`=`' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '`
             WHERE `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` =4
             ORDER BY `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` ASC, 
             `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` ASC      
             ');
    } else if ($Od === null && $Do === null && $status == 'active'){
        $stmt = $this->pdo->prepare('SELECT 
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$id . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`,
                        `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '` as IdKlient,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$imie . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$nazwisko . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idButy . '`,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '` as IdStatus,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$nazwa . '`
             
             
             FROM `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             INNER JOIN `' . \Config\Database\DBConfig::$tabelaSprzet . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`=`' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$id . '`
                INNER JOIN `' . \Config\Database\DBConfig::$tabelaKlient . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`=`' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '`
                    INNER JOIN `' . \Config\Database\DBConfig::$tabelaStatus . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`=`' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '`
             WHERE `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` !=4
             ORDER BY `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` ASC, 
             `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` ASC      
             ');
    } else if ($status == 'all') {
        $stmt = $this->pdo->prepare('SELECT 
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$id . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`,
                        `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '` as IdKlient,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$imie . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$nazwisko . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idButy . '`,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '` as IdStatus,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$nazwa . '`
             
             
             FROM `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             INNER JOIN `' . \Config\Database\DBConfig::$tabelaSprzet . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`=`' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$id . '`
                INNER JOIN `' . \Config\Database\DBConfig::$tabelaKlient . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`=`' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '`
                    INNER JOIN `' . \Config\Database\DBConfig::$tabelaStatus . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`=`' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '`
             WHERE `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` BETWEEN :dataOd AND :dataDo
             ORDER BY `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` ASC, 
             `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` ASC      
             ');

        $stmt->bindValue(':dataOd', $Od, PDO::PARAM_STR);
        $stmt->bindValue(':dataDo', $Do, PDO::PARAM_STR);

    } else if ($status == 'active') {
        $stmt = $this->pdo->prepare('SELECT 
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$id . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`,
                        `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '` as IdKlient,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$imie . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$nazwisko . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idButy . '`,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '` as IdStatus,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$nazwa . '`
             
             
             FROM `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             INNER JOIN `' . \Config\Database\DBConfig::$tabelaSprzet . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`=`' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$id . '`
                INNER JOIN `' . \Config\Database\DBConfig::$tabelaKlient . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`=`' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '`
                    INNER JOIN `' . \Config\Database\DBConfig::$tabelaStatus . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`=`' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '`
             WHERE `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` !=4
             ORDER BY `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` ASC, 
             `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` ASC      
             ');

        $stmt->bindValue(':dataOd', $Od, PDO::PARAM_STR);
        $stmt->bindValue(':dataDo', $Do, PDO::PARAM_STR);
    } else if ($status == 'ended') {
        $stmt = $this->pdo->prepare('SELECT 
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$id . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataDo . '`,
                        `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idNartyOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '` as IdKlient,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$imie . '`,
            `' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$nazwisko . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idSnowboard . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idNarty . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idKijki . '`,
            `' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$idButy . '`,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '` as IdStatus,
            `' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$nazwa . '`
             
             
             FROM `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`
             INNER JOIN `' . \Config\Database\DBConfig::$tabelaSprzet . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idSprzet . '`=`' . \Config\Database\DBConfig::$tabelaSprzet . '`.`' . \Config\Database\DBConfig\Sprzet::$id . '`
                INNER JOIN `' . \Config\Database\DBConfig::$tabelaKlient . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idKlient . '`=`' . \Config\Database\DBConfig::$tabelaKlient . '`.`' . \Config\Database\DBConfig\Klient::$id . '`
                    INNER JOIN `' . \Config\Database\DBConfig::$tabelaStatus . '` 
                    ON `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '`=`' . \Config\Database\DBConfig::$tabelaStatus . '`.`' . \Config\Database\DBConfig\Status::$id . '`
             WHERE `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` BETWEEN :dataOd AND :dataDo AND `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` =4
             ORDER BY `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$idStatus . '` ASC, 
             `' . \Config\Database\DBConfig::$tabelaRezerwacja . '`.`' . \Config\Database\DBConfig\Rezerwacja::$dataOd . '` ASC      
             ');

        $stmt->bindValue(':dataOd', $Od, PDO::PARAM_STR);
        $stmt->bindValue(':dataDo', $Do, PDO::PARAM_STR);
    }

            $result = $stmt->execute();
            $records =$stmt->fetchAll();
            $stmt->closeCursor();
            if($records && !empty($records) )
                $data['reservations'] = $records;

        } catch (\PDOException $e) {
            $data['error'] = \Config\Database\DBErrorName::$query;
        }

        return $data;
    }

    public function getOneByIdReservation($id){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($id === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = array();
        $data['rezerwacja'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'` 
                INNER JOIN `'.\Config\Database\DBConfig::$tabelaSprzet.'`
                ON `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idSprzet.'`=`'.\Config\Database\DBConfig::$tabelaSprzet.'`.`'.\Config\Database\DBConfig\Sprzet::$id.'`
                WHERE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$id.'`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            $rezerwacja = $stmt->fetchAll();
            $stmt->closeCursor();
            if($rezerwacja && !empty($rezerwacja))
                $data['rezerwacja'] = $rezerwacja;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
        }
        catch(\PDOException $e)	{
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
        $data['rezerwacja'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'` 
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaSprzet.'`
            ON `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idSprzet.'`=`'.\Config\Database\DBConfig::$tabelaSprzet.'`.`'.\Config\Database\DBConfig\Sprzet::$id.'`
            WHERE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idSprzet.'`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            $rezerwacja = $stmt->fetchAll();
            $stmt->closeCursor();
            if($rezerwacja && !empty($rezerwacja))
                $data['rezerwacja'] = $rezerwacja;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    //pobranie jednej rezerwacji
    public function getOneReservation($id){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($id === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = array();
        $data['rezerwacja'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaKlient.'`
            ON `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idKlient.'`=`'.\Config\Database\DBConfig::$tabelaKlient.'`.`'.\Config\Database\DBConfig\Klient::$id.'` 
            WHERE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$id.'`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            $rezerwacja = $stmt->fetchAll();
            $stmt->closeCursor();
            if($rezerwacja && !empty($rezerwacja))
                $data['rezerwacja'] = $rezerwacja;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    //pobranie wszystkich rezerwacji klienta pogrupowanych wedlug daty
    public function getCustomerReservationGroupByDate($idCustomer){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($idCustomer === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = array();
        $data['rezerwacja'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`
            WHERE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idKlient.'`=:id');
            $stmt->bindValue(':id', $idCustomer, PDO::PARAM_INT);
            $result = $stmt->execute();

            $rezerwacja = $stmt->fetchAll();
            $stmt->closeCursor();
            if($rezerwacja && !empty($rezerwacja))
                $data['rezerwacja'] = $rezerwacja;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

    public function changeStatus($idReservation, $idStatus){

            $data = array();
            if($this->pdo === null){
                $data['error'] = \Config\Database\DBErrorName::$connection;
                return $data;
            }
            if($idReservation == null || $idStatus == null){
                $data['error'] = \Config\Database\DBErrorName::$empty;
                return $data;
            }

            if($idStatus == 1) {
                $idStatus = 2;
            }

            try	{
                $stmt = $this->pdo->prepare('UPDATE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'` SET
                    `'
                    .\Config\Database\DBConfig\Rezerwacja::$idStatus.'`=:status
                
                WHERE `'
                    .\Config\Database\DBConfig\Rezerwacja::$id.'`=:id');
                $stmt->bindValue(':id', $idReservation, PDO::PARAM_INT);
                $stmt->bindValue(':status', $idStatus, PDO::PARAM_INT);

                $result = $stmt->execute();

                if(!$result)
                    $data['error'] = \Config\Database\DBErrorName::$nomatch;
                else
                    $data['message'] = "ok";
                $stmt->closeCursor();
            }
            catch(\PDOException $e)	{
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
            return $data;
        }

        public function delete($id){
            $data = array();

            try {
                $stmt = $this->pdo->prepare('DELETE FROM  `' . \Config\Database\DBConfig::$tabelaRezerwacja . '` WHERE  `' . \Config\Database\DBConfig\Rezerwacja::$id . '`=:id');
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $result = $stmt->execute();
                if($result){
                    $data['msg'] = "Poprawnie anulowano rezerwacje";
                }else{
                    $data['error'] = "Wystąpił błąd podczas usuwania rezerwacji";
                }
                $stmt->closeCursor();
            } catch (\PDOException $e) {
                $data['error'] = \Config\Database\DBErrorName::$query;
            }

            return $data;
        }

        public function getSkisOptions($id){
            if($this->pdo === null){
                $data['error'] = \Config\Database\DBErrorName::$connection;
                return $data;
            }
            if($id === null){
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
                return $data;
            }
            $data = array();
            $data['skisOptions'] = array();
            try	{
                $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaNartyOpcje.'`
            ON `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idNartyOpcje.'`=`'.\Config\Database\DBConfig::$tabelaNartyOpcje.'`.`'.\Config\Database\DBConfig\NartyOpcje::$id.'` 
            WHERE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$id.'`=:id');
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $result = $stmt->execute();

                $rezerwacja = $stmt->fetchAll();
                $stmt->closeCursor();
                if($rezerwacja && !empty($rezerwacja))
                    $data['skisOptions'] = $rezerwacja;
                else
                    $data['error'] = \Config\Database\DBErrorName::$nomatch;
            }
            catch(\PDOException $e)	{
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
            return $data;
        }

    public function getSnowboardOptions($id){
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        if($id === null){
            $data['error'] = \Config\Database\DBErrorName::$nomatch;
            return $data;
        }
        $data = array();
        $data['snowboardOptions'] = array();
        try	{
            $stmt = $this->pdo->prepare('SELECT * FROM  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaSnowboardOpcje.'`
            ON `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$idSnowboardOpcje.'`=`'.\Config\Database\DBConfig::$tabelaSnowboardOpcje.'`.`'.\Config\Database\DBConfig\SnowboardOpcje::$id.'` 
            INNER JOIN `'.\Config\Database\DBConfig::$tabelaSnowboardUstawienie.'`
            ON `'.\Config\Database\DBConfig::$tabelaSnowboardOpcje.'`.`'.\Config\Database\DBConfig\SnowboardOpcje::$idUstawienie.'`=`'.\Config\Database\DBConfig::$tabelaSnowboardUstawienie.'`.`'.\Config\Database\DBConfig\SnowboardUstawienie::$id.'`
            WHERE  `'.\Config\Database\DBConfig::$tabelaRezerwacja.'`.`'.\Config\Database\DBConfig\Rezerwacja::$id.'`=:id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            $rezerwacja = $stmt->fetchAll();
            $stmt->closeCursor();
            if($rezerwacja && !empty($rezerwacja))
                $data['snowboardOptions'] = $rezerwacja;
            else
                $data['error'] = \Config\Database\DBErrorName::$nomatch;
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;
    }

        /*

        //jednorazowych rezerwacji (jednorazowo moze 4 sprzety zarezerwowac wiec rezerwwacji w systemie moze byc w przypadku 3 jednorazowych -> 12)
        //sprawdzanie z datami
        public function checkAmountReservationCustomer($idCustomer){
            $data = array();
            if($this->pdo === null){
                $data['error'] = \Config\Database\DBErrorName::$connection;
                return $data;
            }
            if($idCustomer == null){
                $data['error'] = \Config\Database\DBErrorName::$empty;
                return $data;
            }

            try	{
                $stmt = $this->pdo->prepare('SELECT *
                FROM `'.DB\DBConfig::$tabelaRezerwacja.'` 
                WHERE `'.DB\DBConfig::$tabelaRezerwacja.'`.`'.DB\DBConfig\Rezerwacja::$idKlient.'`=:idCustomer
                GROUP BY `'.DB\DBConfig::$tabelaRezerwacja.'`.`'.DB\DBConfig\Rezerwacja::$dataOd.'`, `'.DB\DBConfig::$tabelaRezerwacja.'`.`'.DB\DBConfig\Rezerwacja::$dataDo.'`
             ');

                $stmt->bindValue(':idCustomer', $idCustomer, PDO::PARAM_INT);
                $result = $stmt->execute();
                $reservations = $stmt->fetchAll();
                if(!$result)
                    $data['error'] = \Config\Database\DBErrorName::$nomatch;
                else
                    //$data['message'] = "ok";
                    $data = $reservations;
                $stmt->closeCursor();
            }
            catch(\PDOException $e)	{
                $data['error'] = \Config\Database\DBErrorName::$query;
            }
            return $data;
        }*/

}