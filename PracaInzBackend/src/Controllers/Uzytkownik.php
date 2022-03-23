<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 16.12.2018
 * Time: 00:51
 */

namespace Controllers;


class Uzytkownik extends Controller
{
    public function getAll(){
        $userModel = $this->getModel("Uzytkownik");
        $userAll = $userModel->getAll();
        echo json_encode($userAll);
    }

    public function getOne($id){
        $model = $this->getModel('Uzytkownik');
        echo json_encode($model->getOne($id));
    }

    public function getByUsername($id){
        $model = $this->getModel('Uzytkownik');
        echo json_encode($model->getOneUsername($id));

    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelUser = $this->getModel("Uzytkownik");

        //czy taki pracownik istnieje i czy istnije taka nazwa uzytkownika
        $isUserExist = $modelUser->getOneType($dane['telefon'], $dane['email']);
        $isUsernameExist = $modelUser->getOneUsername($dane['nazwaUzytkownik']);


        if ($isUserExist != null) {
            $data['error'] = "Uzytkownik już istnieje w bazie";
            echo json_encode($data);
        } else if ($isUsernameExist != null) {
            $data['error'] = "Nazwa użytkownika już istnieje w bazie";
            echo json_encode($data);
        } else {
            // $idEmployee = $modelEmployee->add($dane['imie'], $dane['nazwisko'], $dane['telefon'], $dane['email']);
            $data = $modelUser->add($dane['imie'], $dane['nazwisko'], $dane['telefon'], $dane['email'], $dane['nazwaUzytkownik'], $dane['haslo'], (int)$dane['prawo']);

            if($data['error'] == null) {
                $data['msg'] = 'Uzytkownik został dodany';
                echo json_encode($data);
            }else{
                echo json_encode($data);
            }
        }
    }

    public function edit(){
        $newUser = json_decode(file_get_contents('php://input'), true);
        $modelUser = $this->getModel("Uzytkownik");

        $data = $modelUser->update($newUser['id'], $newUser['imie'], $newUser['nazwisko'], $newUser['telefon'], $newUser['email'],$newUser['nazwaUzytkownik'],$newUser['haslo'],$newUser['prawo'], $newUser['aktywny']);



        echo json_encode($data);
    }

    public function deactivate($id){
        $data = array();
        $modelUser = $this->getModel("Uzytkownik");
        $user = $modelUser->getOne($id);

        if($user == null) {
            $data['error'] = "Brak uzytkownika o określonym id";
            echo json_encode($data);
        } else {
            $data = $modelUser->update($user['user'][0]['Id'], $user['user'][0]['Imie'], $user['user'][0]['Nazwisko'], $user['user'][0]['Telefon'], $user['user'][0]['Email'],$user['user'][0]['NazwaUzytkownik'],$user['user'][0]['Haslo'],$user['user'][0]['IdPrawo'], false);
            echo json_encode($data);
        }

    }

}