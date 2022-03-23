<?php

namespace Controllers;


class Snowboard extends Controller
{
    public function getAll(){
        //$headers = getallheaders();
        $response = array();

        //$data['auth'] = $headers['Authorization'];

        //$data = explode(" ", $headers['Authorization']);
        //d($data[1]);

        //$accessModel = $this->getModel("Dostep");
        //$auth = $accessModel->checkJWT($data[1]);
        //echo json_encode($auth);

        //if($auth['msg'] != null && $auth['error'] == null) {
            $snowboardModel = $this->getModel("Snowboard");
            $snowboardAll = $snowboardModel->getAll();
            echo json_encode($snowboardAll);
      //  } else {
      //      $response['error'] = $auth['error'];
      //      echo json_encode($response);
       // }
    }

    public function getAllSingle(){
        $snowboardModel = $this->getModel("Snowboard");
        $snowboardAll = $snowboardModel->getAll(true);
        echo json_encode($snowboardAll);
    }


    public function getSnowboardLeg(){
        $snowboardLeg = $this->getModel("SnowboardUstawienie");
        $data = $snowboardLeg->getAll();
        echo json_encode($data);
    }

    public function getOne($id){
        $model = $this->getModel('Snowboard');
        echo json_encode($model->getOne($id));
    }

    public function edit(){
        $newSnowboard = json_decode(file_get_contents('php://input'), true);
        $modelSnowboard = $this->getModel("Snowboard");
        $modelSprzet = $this->getModel("Sprzet");
        //pobranie starych wartosci snowboardu, zeby sprawdzic ile bylo konkretnych snowboardow
        $oldSnowboard = $modelSnowboard->getOne($newSnowboard['id']);
        /*
         * pobranie indeksów tych samych snowboardow
         * $allTypeSnowboardId zawiera indeksy snowboardow w tabeli snowboard i indeksy z tabeli sprzet powiazanym z danym snowboardem
         */
        $allTypeSnowboardId = $modelSnowboard->getOneType($oldSnowboard[0]);
        //sprawdzenie ilosci > to dodajemy < to odejmujemy
        $difference = $newSnowboard['ilosc'] - sizeof($allTypeSnowboardId);
        if($difference > 0){
            $idSnowboard = $modelSnowboard->add($oldSnowboard[0]['Model'], (int)$oldSnowboard[0]['IdPrzeznaczenie'], (float)$oldSnowboard[0]['Dlugosc'], $difference);
            $data = $modelSprzet->add($idSnowboard, (int)$oldSnowboard[0]['IdProducent'], (int)$oldSnowboard[0]['IdPlec'], $oldSnowboard[0]['Cena']);
        }
        if($difference < 0){
            $ilosc = sizeof($allTypeSnowboardId)-1;
            for($i=$ilosc;$i>($ilosc-abs($difference));$i--){
                $modelSnowboard->delete($allTypeSnowboardId[$i]['IdSnowboard']);
            }
        }
        //sprawdzamy jeszcze raz indeksy bo mogly sie zmienic
        $allTypeSnowboardId = $modelSnowboard->getOneType($oldSnowboard[0]);
        $resultSnowboard = $modelSnowboard->update($allTypeSnowboardId, $newSnowboard);
        $resultSprzet = $modelSprzet->updateSnowboard($allTypeSnowboardId, $newSnowboard);

            $data['msg'] = "Poprawnie zaktualizowano snowboard";

        echo json_encode($data);
    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelSnowboard = $this->getModel("Snowboard");
        $modelSprzet = $this->getModel("Sprzet");

        //sprawdzenie czy juz istnieje taki snowboard
        $isSnowboardExist = $modelSnowboard->isSnowboardExist($dane['model'], (int)$dane['przeznaczenie'], (float)$dane['dlugosc'], (int)$dane['producent'], (int)$dane['plec']);
        if ($isSnowboardExist) {
            $data['error'] = "Sprzęt już istnieje w bazie danych";
            echo json_encode($data);
        } else {
            $idSnowboard = $modelSnowboard->add($dane['model'], (int)$dane['przeznaczenie'], (float)$dane['dlugosc'], (int)$dane['ilosc']);
            $data = $modelSprzet->add($idSnowboard, (int)$dane['producent'], (int)$dane['plec'], (float)$dane['cena']);
            if($data['msg']) {
                $data['msg'] = 'Snowboard został dodany';
                echo json_encode($data);
            }
        }
    }

    //usuwa wszystkie egzemplarze
    public function delete($id){
        $data = array();
        //$data = json_decode(file_get_contents('php://input'), true);
        //$accessController = new \Controllers\Dostep();
        //$accessController->islogin();
        $modelSnowboard = $this->getModel("Snowboard");
        //$modelSprzet = $this->getModel("Sprzet");
        $snowboard = $modelSnowboard->getOne($id);
        //pobranie wszystkich id
       $allTypeSnowboardId = $modelSnowboard->getOneType($snowboard[0]);
        if (is_array($allTypeSnowboardId) || is_object($allTypeSnowboardId)) {
            foreach ($allTypeSnowboardId as $idSnowboard) {
                $modelSnowboard->delete($idSnowboard['IdSnowboard']);
            }
        }
       $data['msg'] = "Poprawnie usunięto sprzęt";
       echo json_encode($data);
    }

    public function getByDate(){
        $dane = json_decode(file_get_contents('php://input'), true);

        $response = array();
        $snowboardList = array();

        $dateFrom = date('Y-m-d', strtotime($dane['dataOd']));
        $dateTo = date('Y-m-d', strtotime($dane['dataDo']));

        $modelSnowboard = $this->getModel('Snowboard');
        $modelReservation = $this->getModel('Rezerwacja');
        $modelRental = $this->getModel('Wypozyczenie');

        $allSnowboard = $modelSnowboard->getAll(true);

        foreach ($allSnowboard['snowboard'] as $item) { //po kazdym sprzecie
            $snowboardReservation = $modelReservation->getOne($item['Id']); //wszystkie rezerwacje danego sprzetu

            if (!isset($snowboardReservation['error'])) {
                foreach ($snowboardReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $snowboardList = $this->updateList($snowboardList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $snowboardList = $this->updateList($snowboardList, $item, false);
                    }
                } // foreach rezerwacje
            } else {
                $response['test'] = $item;
                $snowboardList = $this->updateList($snowboardList, $item, true);
            }

            //sprawdzenie wypozyczen
            $snowboardRental = $modelRental->getOne($item['Id']); //wszystkie wypozyczenia sprzetu
            if (!isset($snowboardRental['error'])){
                foreach ($snowboardRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $snowboardList = $this->updateList($snowboardList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $snowboardList = $this->updateList($snowboardList, $item, false);
                    }
                }
            }
        } // foreach sprzet
        //wybranie wolnego
        $tmp = array();
        $counter = 0;
        $response['snowb'] = $snowboardList;
        foreach ($snowboardList as $item) {
            if ($item['flaga'] == true) {
                $tmp[$counter] = $item['IdSnowboard'];
                $counter += 1;
            }
        }
        $response['wolne'] = $tmp;
        echo json_encode($response);

    }

    //sprawdzenie czy zawiera rezerwacje/wypozyczenie i uaktualnia stan
    public function updateList($tableEquipment,$idEquipment, $option){
        if(isset($tableEquipment[0])) {
            $counter = 0;
            foreach ($tableEquipment as $item) {

                $contain = null;
                if ($item['IdSnowboard'] == $idEquipment && $item['flaga']!= false) {
                    $contain = true;
                    $tableEquipment[$counter]['IdSnowboard'] = $idEquipment;
                    $tableEquipment[$counter]['flaga'] = $option;
                    $counter=0;
                    break;
                }else if($item['IdSnowboard'] == $idEquipment){
                    $contain = true;
                    $counter=0;
                    break;
                }
                $counter+=1;
            }
            if($contain == null){
                array_push($tableEquipment, ['IdSnowboard' => $idEquipment, 'flaga' => $option]);
            }
        }else{
            $tableEquipment[] = ['IdSnowboard' => $idEquipment, 'flaga' => $option];
        }

        return $tableEquipment;
    }

}