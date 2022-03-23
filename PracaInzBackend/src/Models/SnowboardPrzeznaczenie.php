<?php

namespace Models;

use Config\Database as DB;
class SnowboardPrzeznaczenie extends Model
{
    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaSnowboardPrzeznaczenie.'`');

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

    public function getAllForSelect(){
        $data = $this->getAll();
        $type = array();
        if(!isset($data['error']))
            foreach($data as $item) {
                $type[$item[\Config\Database\DBConfig\SnowboardPrzeznaczenie::$id]] = $item[\Config\Database\DBConfig\SnowboardPrzeznaczenie::$nazwa];
            }
                return $type;
    }


}