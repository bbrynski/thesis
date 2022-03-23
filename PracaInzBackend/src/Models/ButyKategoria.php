<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 18.11.2018
 * Time: 22:43
 */

namespace Models;
use \PDO;
use Config\Database as DB;

class ButyKategoria extends Model
{
    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaButyKategoria.'`');

            $buty = $stmt->fetchAll();
            $stmt->closeCursor();
            if($buty && !empty($buty))
            {
                return $buty;
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