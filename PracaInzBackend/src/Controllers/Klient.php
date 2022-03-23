<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 20.11.2018
 * Time: 01:40
 */

namespace Controllers;


class Klient extends Controller
{
    public function getAll(){
        $modelCustomer = $this->getModel("Klient");
        $customerAll = $modelCustomer->getAll();
        echo json_encode($customerAll);
    }

    public function getOne($id){
        $model = $this->getModel('Klient');
        echo json_encode($model->getOne($id));
    }

    public function edit(){
        $newCustomer = json_decode(file_get_contents('php://input'), true);
        $modelCustomer = $this->getModel("Klient");

        $resultCustomer = $modelCustomer->update($newCustomer);

        $data['msg'] = "Poprawnie zaktualizowano producenta";

        echo json_encode($data);
    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelCustomer = $this->getModel("Klient");

        $isCustomerExist = $modelCustomer->getOneType($dane['telefon'], $dane['email']);

        if ($isCustomerExist != null) {
            $data['error'] = "Klient o takim numerze telefonu lub adresie email istnieje w bazie";
            echo json_encode($data);
        } else {
            $modelCustomer->add($dane['imie'], $dane['nazwisko'], $dane['telefon'], $dane['email']);
            //$data = $modelSprzet->addSkis($idSkis, (int)$dane['producent'], (int)$dane['plec'], (float)$dane['cena']);
            //if($data['msg']) {
                $data['msg'] = 'Klient został dodany';
                echo json_encode($data);
           // }
        }

    }

    public function delete($id){
        $data = array();
        //$data = json_decode(file_get_contents('php://input'), true);
        //$accessController = new \Controllers\Dostep();
        //$accessController->islogin();
        $modelCustomer = $this->getModel("Klient");
        $customerAssignedToReservation = $modelCustomer->getAssignedToReservation($id);
        //$skis = $modelSkis->getOne($id);
        //pobranie wszystkich id
        //$allTypeSnowboardId = $modelSkis->getOneType($skis[0]['Model'], $skis[0]['IdPrzeznaczenie'], $skis[0]['Dlugosc'], $skis[0]['IdProducent'], $skis[0]['IdPlec']);
        //if (is_array($allTypeSnowboardId) || is_object($allTypeSnowboardId)) {
        //   foreach ($allTypeSnowboardId as $idSkis) {
        //       $modelSkis->delete($idSkis['IdNarty']);
        //   }
        // }
        if($customerAssignedToReservation != null){
            $data['error'] = "Nie można usunąc ponieważ klient jest przypisany do rezerwacji";
            echo json_encode($data);
        }else{
            $data = $modelCustomer->delete($id);
            $data['msg'] = 'Producent został usunięty';
            echo json_encode($data);
        }
    }

    public function updateAmountReservation(){
        $idCustomer = json_decode(file_get_contents('php://input'), true);

        $modelCustomer = $this->getModel('Klient');
        $modelReservation = $this->getModel('Rezerwacja');
        $allReservation = $modelReservation->getCustomerReservationGroupByDate($idCustomer);

        $responseCustomer = $modelCustomer->updateReservationAmount($idCustomer, sizeof($allReservation['rezerwacja']));


        $response['all'] = $allReservation;
        echo json_encode($response);
    }

}