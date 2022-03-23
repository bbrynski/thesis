<?php

namespace Controllers;


class Narty extends Controller
{
    public function getAll(){
        $skisModel = $this->getModel("Narty");
        $skisAll = $skisModel->getAll();
        echo json_encode($skisAll);
    }

    public function getAllSingle(){
        $skisModel = $this->getModel("Narty");
        $skisAll = $skisModel->getAll(true);
        echo json_encode($skisAll);
    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelSkis = $this->getModel("Narty");
        $modelSprzet = $this->getModel("Sprzet");

        //sprawdzenie czy juz istnieje taki snowboard
        //echo json_encode($dane);
        $isSkisExist = $modelSkis->getOneType($dane['model'], (int)$dane['przeznaczenie'], (float)$dane['dlugosc'], (int)$dane['producent'], (int)$dane['plec']);

        if ($isSkisExist != null) {
            $data['error'] = "Sprzęt już istnieje w bazie danych";
            echo json_encode($data);
        } else {
            $idSkis = $modelSkis->add($dane['model'], (int)$dane['przeznaczenie'], (float)$dane['dlugosc'], (int)$dane['ilosc']);
            $data = $modelSprzet->addSkis($idSkis, (int)$dane['producent'], (int)$dane['plec'], (float)$dane['cena']);
            if($data['msg']) {
                $data['msg'] = 'Narty zostały dodane';
                echo json_encode($data);
            }
        }
    }

    public function getOne($id){
        $model = $this->getModel('Narty');
        echo json_encode($model->getOne($id));
    }

    public function edit(){
        $newSkis = json_decode(file_get_contents('php://input'), true);
        $modelSkis = $this->getModel("Narty");
        $modelSprzet = $this->getModel("Sprzet");
        //pobranie starych wartosci snowboardu, zeby sprawdzic ile bylo konkretnych snowboardow
        $oldSkis = $modelSkis->getOne($newSkis['id']);

        /*
         * pobranie indeksów tych samych snowboardow
         * $allTypeSnowboardId zawiera indeksy snowboardow w tabeli snowboard i indeksy z tabeli sprzet powiazanym z danym snowboardem
         */
        $allTypeSkisId = $modelSkis->getOneType($oldSkis[0]['Model'], $oldSkis[0]['IdPrzeznaczenie'], $oldSkis[0]['Dlugosc'], $oldSkis[0]['IdProducent'], $oldSkis[0]['IdPlec']);
        //sprawdzenie ilosci > to dodajemy < to odejmujemy
        $difference = $newSkis['ilosc'] - sizeof($allTypeSkisId);
        if($difference > 0){
            $idSkis = $modelSkis->add($oldSkis[0]['Model'], (int)$oldSkis[0]['IdPrzeznaczenie'], (float)$oldSkis[0]['Dlugosc'], $difference);
            $data = $modelSprzet->addSkis($idSkis, (int)$oldSkis[0]['IdProducent'], (int)$oldSkis[0]['IdPlec'], $oldSkis[0]['Cena']);
        }

        if($difference < 0){
            $ilosc = sizeof($allTypeSkisId)-1;
            for($i=$ilosc;$i>($ilosc-abs($difference));$i--){
                $modelSkis->delete($allTypeSkisId[$i]['IdNarty']);
            }
        }

        //sprawdzamy jeszcze raz indeksy bo mogly sie zmienic
        $allTypeSkisId = $modelSkis->getOneType($oldSkis[0]['Model'], $oldSkis[0]['IdPrzeznaczenie'], $oldSkis[0]['Dlugosc'], $oldSkis[0]['IdProducent'], $oldSkis[0]['IdPlec']);
        $resultSkis = $modelSkis->update($allTypeSkisId, $newSkis);
        $resultSprzet = $modelSprzet->updateSnowboard($allTypeSkisId, $newSkis);

        $data['msg'] = "Poprawnie zaktualizowano narty";

        echo json_encode($data);
    }

    public function delete($id){
        $data = array();
        //$data = json_decode(file_get_contents('php://input'), true);
        //$accessController = new \Controllers\Dostep();
        //$accessController->islogin();
        $modelSkis = $this->getModel("Narty");
        //$modelSprzet = $this->getModel("Sprzet");
        $skis = $modelSkis->getOne($id);
        //pobranie wszystkich id
        $allTypeSnowboardId = $modelSkis->getOneType($skis[0]['Model'], $skis[0]['IdPrzeznaczenie'], $skis[0]['Dlugosc'], $skis[0]['IdProducent'], $skis[0]['IdPlec']);
        if (is_array($allTypeSnowboardId) || is_object($allTypeSnowboardId)) {
            foreach ($allTypeSnowboardId as $idSkis) {
                $modelSkis->delete($idSkis['IdNarty']);
            }
        }
        $data['msg'] = "Poprawnie usunięto sprzęt";
        echo json_encode($data);
    }

    public function getByDate(){
        $dane = json_decode(file_get_contents('php://input'), true);

        $response = array();
        $skisList = array();

        $dateFrom = date('Y-m-d', strtotime($dane['dataOd']));
        $dateTo = date('Y-m-d', strtotime($dane['dataDo']));

        $modelSkis = $this->getModel('Narty');
        $modelReservation = $this->getModel('Rezerwacja');
        $modelRental = $this->getModel('Wypozyczenie');

        $allSkis = $modelSkis->getAll(true);

        foreach ($allSkis['skis'] as $item) { //po kazdym sprzecie
            $skisReservation = $modelReservation->getOne($item['Id']); //wszystkie rezerwacje danego sprzetu

            if (!isset($skisReservation['error'])) {
                foreach ($skisReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $skisList = $this->updateList($skisList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $skisList = $this->updateList($skisList, $item, false);
                    }
                } // foreach rezerwacje
            } else {
                $skisList = $this->updateList($skisList, $item, true);
            }

            //sprawdzenie wypozyczen
            $skisRental = $modelRental->getOne($item['Id']); //wszystkie wypozyczenia sprzetu
            if (!isset($skisRental['error'])){
                foreach ($skisRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $skisList = $this->updateList($skisList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $skisList = $this->updateList($skisList, $item, false);
                    }
                }
            }
        } // foreach sprzet
        //wybranie wolnego
        $tmp = array();
        $counter = 0;
        $response['snowb'] = $skisList;
        foreach ($skisList as $item) {
            if ($item['flaga'] == true) {
                $tmp[$counter] = $item['IdNarty'];
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
                if ($item['IdNarty'] == $idEquipment && $item['flaga']!= false) {
                    $contain = true;
                    $tableEquipment[$counter]['IdNarty'] = $idEquipment;
                    $tableEquipment[$counter]['flaga'] = $option;
                    $counter=0;
                    break;
                }else if($item['IdNarty'] == $idEquipment){
                    $contain = true;
                    $counter=0;
                    break;
                }
                $counter+=1;
            }
            if($contain == null){
                array_push($tableEquipment, ['IdNarty' => $idEquipment, 'flaga' => $option]);
            }
        }else{
            $tableEquipment[] = ['IdNarty' => $idEquipment, 'flaga' => $option];
        }

        return $tableEquipment;
    }

}