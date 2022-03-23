<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 05.06.2018
 * Time: 18:38
 */

namespace Controllers;
use Firebase\JWT\JWT;

use Firebase\JWT\SignatureInvalidException;

class Dostep extends  Controller
{
    public function login(){
        $data = json_decode(file_get_contents('php://input'), true);
        $response['error'] = null;
        $modelAccess = $this->getModel('Dostep');
        $viewAccess = $this->getView('Dostep');

        if (isset($data['login']) && isset($data['password'])){
            $response = $modelAccess->login($data['login'], $data['password']);

            if($response['user'] != null && $response['principle'] != null) {
                $token = array();
                $token['user'] = $response['user'];
                $token['principle'] = $response['principle'];
                $jwt = JWT::encode($token, 'galaxy');
                $response['token'] = $jwt;
                $viewAccess->login($response);
            } else {
                $viewAccess->login($response);
            }
        } else {
            $response['error'] = true;
            $viewAccess->login($response);
        }
    }


    /*
    public function logform($data = null){
        if(\Tools\Session::is('message'))
            $data['message'] = \Tools\Session::get('message');
        if(\Tools\Session::is('error'))
            $data['error'] = \Tools\Session::get('error');
        $view=$this->getView('Dostep');
        $view->logform($data);
        \Tools\Session::clear('message');
        \Tools\Session::clear('error');
    }
    //zalogowuje do systemu
    public function login(){
        $model=$this->getModel('Dostep');
        $data = $model->login($_POST['login'],md5($_POST['password']));
        if(!isset($data['error']))
            $this->redirect('');
        else
            $this->logform($data);
    }

    //wylogowuje z systemu
    public function logout(){
        $model=$this->getModel('Dostep');
        $model->logout();
        $this->redirect('');
    }

    public function islogin(){
        if(\Tools\Access::islogin() !== true){
            \Tools\Session::set('message', \Config\Website\MessageName::$mustlogin);
            $this->redirect('dostep/formularz');
        }
    }
    */

}