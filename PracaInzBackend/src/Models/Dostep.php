<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 05.06.2018
 * Time: 18:39
 */

namespace Models;
use \PDO;
use Firebase\JWT\JWT;

use Firebase\JWT\SignatureInvalidException;

class Dostep extends Model
{
    public function checkJWT($token){
        $data = array();
        $data['error'] = null;
        $data['msg'] = null;
        try {
            $jwtDecoded = JWT::decode($token, 'galaxy', array('HS256'));
            $data['msg'] = "OK";
            return $data;
        } catch (SignatureInvalidException $e){
            $data['error'] = 'Wymagana autoryzacja';
            return $data;
        }
    }

    public function login($login, $password){
        //tutaj powinno nastąpić weryfikacja podanych danych
        //z tymi zapisanymi w bazie
        $response = array();
        $response['user'] = null;
        $response['principle'] = null;
        $data = $this->getAll();


        foreach ($data['users'] as $user) {

            // if ($login === ($user['NazwaUzytkownik']) && ($password === $user['Haslo']) ) {
            if ($login === ($user['NazwaUzytkownik']) && (password_verify($password, $user['Haslo']))) {
                $response['user'] = $user['NazwaUzytkownik'];
                $response['principle'] = $user['NazwaPrawo'];
                //zainicjalizowanie sesji
                //\Tools\Access::login($login);

                return $response;
            }
        }
        $response['error'] = \Config\Website\ErrorName::$wrongdata;
        return $response;
    }
    /*
    public function logout(){
        \Tools\Access::logout();
    }
    */

    public function getAll()
    {
        if($this->pdo === null){
            $data['error'] = \Config\Database\DBErrorName::$connection;
            return $data;
        }
        $data = array();
        $data['users'] = array();
        try	{
            $stmt = $this->pdo->query('SELECT * FROM `'.\Config\Database\DBConfig::$tabelaUzytkownik.'`
             INNER JOIN `'.\Config\Database\DBConfig::$tabelaPrawo.'` 
             ON `'.\Config\Database\DBConfig::$tabelaUzytkownik.'`.`'.\Config\Database\DBConfig\Uzytkownik::$idPrawo.'`=`'.\Config\Database\DBConfig::$tabelaPrawo.'`.`'.\Config\Database\DBConfig\Prawo::$id.'`');
            $users = $stmt->fetchAll();
            $stmt->closeCursor();
            if($users && !empty($users))
                $data['users'] = $users;
        }
        catch(\PDOException $e)	{
            $data['error'] = \Config\Database\DBErrorName::$query;
        }
        return $data;

    }

}