<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 22.11.2018
 * Time: 20:37
 */

namespace Controllers;


class Pracownik extends Controller
{
    public function getAll(){
        $employeeModel = $this->getModel("Pracownik");
        $employeeAll = $employeeModel->getAll();
        echo json_encode($employeeAll);
    }

    public function getOne($id){
        $model = $this->getModel('Pracownik');
        echo json_encode($model->getOne($id));
    }

    public function getByUsername($id){
        $model = $this->getModel('Pracownik');
        echo json_encode($model->getByUsername($id));

    }

    public function edit(){
        $newEmployee = json_decode(file_get_contents('php://input'), true);
        $modelEmployee = $this->getModel("Pracownik");

        $resultEmployee = $modelEmployee->update($newEmployee['id'], $newEmployee['imie'], $newEmployee['nazwisko'], $newEmployee['telefon'], $newEmployee['email'], $newEmployee['aktywny']);

        $data['msg'] = "Poprawnie zaktualizowano pracownika";

        echo json_encode($data);
    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelEmployee = $this->getModel("Pracownik");
        $modelUser = $this->getModel("Uzytkownik");

        //czy taki pracownik istnieje i czy istnije taka nazwa uzytkownika
        $isEmployeeExist = $modelEmployee->getOneType($dane['telefon'], $dane['email']);
        $isUsernameExist = $modelUser->getOne($dane['nazwaUzytkownik']);


        if ($isEmployeeExist != null) {
            $data['error'] = "Pracownik już istnieje w bazie";
            echo json_encode($data);
        } else if ($isUsernameExist != null) {
            $data['error'] = "Nazwa użytkownika już istnieje w bazie";
            echo json_encode($data);
        } else {
            $idEmployee = $modelEmployee->add($dane['imie'], $dane['nazwisko'], $dane['telefon'], $dane['email']);
            $data = $modelUser->add($dane['nazwaUzytkownik'], $dane['haslo'], $idEmployee, (int)$dane['prawo']);

                $data['msg'] = 'Pracownik został dodany';
                echo json_encode($data);

        }
    }

    public function deactivate($id){
        $data = array();
        $modelEmployee = $this->getModel("Pracownik");
        $data = $modelEmployee->getOne($id);

        if($data == null) {
            $data['error'] = "Brak pracownika o określonym id";
            echo json_encode($data);
        } else {
            $modelEmployee->update($data[0]['Id'], $data[0]['Imie'], $data[0]['Nazwisko'], $data[0]['Telefon'], $data[0]['Email'], false);
            $data = array();
            $data['msg'] = "Poprawnie zdezaktywowano pracownika";
            echo json_encode($data);
        }

    }

}