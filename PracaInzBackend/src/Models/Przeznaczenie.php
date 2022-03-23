<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 19.10.2018
 * Time: 15:54
 */

namespace Models;

use Config\Database as DB;
class Przeznaczenie extends Model
{

    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaPrzeznaczenie.'`');

            $przeznaczenie = $stmt->fetchAll();
            $stmt->closeCursor();
            if($przeznaczenie && !empty($przeznaczenie))
            {
                return $przeznaczenie;
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