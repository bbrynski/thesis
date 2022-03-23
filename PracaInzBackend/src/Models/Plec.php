<?php

namespace Models;

use Config\Database as DB;
class Plec extends Model
{
    public function getAll(){
        $data = array();
        try
        {

            $stmt = $this->pdo->query('SELECT * FROM `'.DB\DBConfig::$tabelaPlec.'`');

            $plec = $stmt->fetchAll();
            $stmt->closeCursor();
            if($plec && !empty($plec))
            {
                return $plec;
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
        $plec = array();
        if(!isset($data['error']))
            foreach($data as $item) {
                $plec[$item[\Config\Database\DBConfig\Plec::$id]] = $item[\Config\Database\DBConfig\Plec::$nazwa];
            }
        return $plec;
    }


}