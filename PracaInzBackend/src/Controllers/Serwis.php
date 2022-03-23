<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 06.01.2019
 * Time: 16:40
 */

namespace Controllers;


class Serwis extends Controller
{
    public function getAllServiceActivity(){
        $model = $this->getModel('CzynnoscTyp');
        echo json_encode($model->getAll());
    }

    public function addActivityToEquipment(){
        $data = json_decode(file_get_contents('php://input'), true);
        $response = array();
        $response['error'] = null;
        $response['msg'] = null;

        $modelServiceActivity = $this->getModel('CzynnoscSerwisowa');
        $modelServiceHistory = $this->getModel('HistoriaSerwisowa');
        $modelEquipment = $this->getModel('Sprzet');

        $responseServiceActivity = $modelServiceActivity->add($data['idCzynnosc'], date('Y-m-d', strtotime($data['data'])), $data['opis']);

        if (!isset($responseServiceActivity['error'])){
            $responseServiceHistory = $modelServiceHistory->add($responseServiceActivity['idWstawione'],$data['idSprzet'],$data['idPracownik'] );
            if (!isset($responseServiceHistory['error'])){
                $responseEquipment = $modelEquipment->changeStatusAfterService($data['idSprzet']);
                $response['msg'] = "Poprawnie dodano czynnosc serwisowa";

                echo json_encode($response);
            } else {
                $response['error'] = "Nie udało się dodać czynnosci serwisowej";
                echo json_encode($response);
            }
        } else {
            $response['error'] = "Nie udało się dodać czynnosci serwisowej";
            echo json_encode($response);
        }
    }

    public function getOneServiceEquipmentHistory($id){
        $response = array();
        $response['error'] = null;
        $response['msg'] = null;

        $modelServiceHistory = $this->getModel('HistoriaSerwisowa');
        $responseServiceHistory = $modelServiceHistory->getOneServiceEquipmentHistory($id);
        if(!isset($responseServiceHistory['error'])){
            $response['msg'] = 'OK';
            $response['history'] = $responseServiceHistory['history'];
            echo json_encode($response);

        } else {
            $response['error'] = $responseServiceHistory['error'];
            echo json_encode($response);
        }

    }
}