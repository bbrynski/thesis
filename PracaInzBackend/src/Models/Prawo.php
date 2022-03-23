<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 22.11.2018
 * Time: 20:54
 */

namespace Models;
use Config\Database as DB;

class Prawo extends Model
{
    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaPrawo.'`');

            $prawo = $stmt->fetchAll();
            $stmt->closeCursor();
            if($prawo && !empty($prawo))
            {
                return $prawo;
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