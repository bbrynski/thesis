<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 06.01.2019
 * Time: 16:39
 */

namespace Models;
use \PDO;
use Config\Database as DB;

class CzynnoscTyp extends Model
{
    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaCzynnoscTyp.'`');

            $czynnosc = $stmt->fetchAll();
            $stmt->closeCursor();
            if($czynnosc && !empty($czynnosc))
            {
                return $czynnosc;
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

}