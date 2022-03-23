<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 27.11.2018
 * Time: 20:57
 */

namespace Models;
use \PDO;
use Config\Database as DB;

class SnowboardUstawienie extends Model
{
    public function getAll(){

        try
        {
            $stmt = $this->pdo->query('SELECT * FROM `'.\Config\Database\DBConfig::$tabelaSnowboardUstawienie.'`');
            $options = $stmt->fetchAll();

            $stmt->closeCursor();



            if($options && !empty($options))
            {
                $data['snowboardUstawienie'] = $options;
                return $data;
            }
            else
            {
                $data['error'] = 'Brak danych';
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

}