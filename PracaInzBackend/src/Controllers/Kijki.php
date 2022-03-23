<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 15.11.2018
 * Time: 13:01
 */

namespace Controllers;


class Kijki extends Controller
{
    public function getAll(){
        $skiPolesModel = $this->getModel("Kijki");
        $skiPolesAll = $skiPolesModel->getAll();
        echo json_encode($skiPolesAll);
    }

    public function getAllSingle(){
        $skiPolesModel = $this->getModel("Kijki");
        $skiPolesAll = $skiPolesModel->getAll(true);
        echo json_encode($skiPolesAll);
    }

    public function getOne($id){
        $model = $this->getModel('Kijki');
        echo json_encode($model->getOne($id));
    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelSkiPoles = $this->getModel("Kijki");
        $modelSprzet = $this->getModel("Sprzet");

        //sprawdzenie czy juz istnieje taki snowboard
        //echo json_encode($dane);
        $isSkiPolesExist = $modelSkiPoles->getOneType($dane['model'], (float)$dane['dlugosc'], (int)$dane['producent'], (int)$dane['plec']);
        if ($isSkiPolesExist != null) {
            $data['error'] = "Sprzęt już istnieje w bazie danych";
            echo json_encode($data);
        } else {
            $idSkiPoles = $modelSkiPoles->add($dane['model'], (float)$dane['dlugosc'], (int)$dane['ilosc']);
            $data = $modelSprzet->addSkiPoles($idSkiPoles, (int)$dane['producent'], (int)$dane['plec'], (float)$dane['cena']);
            if($data['msg']) {
                $data['msg'] = 'Kijki zostały dodane';
                echo json_encode($data);
            }
        }

    }

    public function edit(){
        $newSkiPoles = json_decode(file_get_contents('php://input'), true);
        $modelSkiPoles = $this->getModel("Kijki");
        $modelSprzet = $this->getModel("Sprzet");
        //pobranie starych wartosci kijkow, zeby sprawdzic ile bylo konkretnych
        $oldSkiPoles = $modelSkiPoles->getOne($newSkiPoles['id']);

        /*
         * pobranie indeksów tych samych snowboardow
         * $allTypeSnowboardId zawiera indeksy snowboardow w tabeli snowboard i indeksy z tabeli sprzet powiazanym z danym snowboardem
         */
        $allTypeSkiPolesId = $modelSkiPoles->getOneType($oldSkiPoles[0]['Model'], $oldSkiPoles[0]['Dlugosc'], $oldSkiPoles[0]['IdProducent'], $oldSkiPoles[0]['IdPlec']);
        //sprawdzenie ilosci > to dodajemy < to odejmujemy
        $difference = $newSkiPoles['ilosc'] - sizeof($allTypeSkiPolesId);
        if($difference > 0){
            $idSkiPoles = $modelSkiPoles->add($oldSkiPoles[0]['Model'], (float)$oldSkiPoles[0]['Dlugosc'], $difference);
            $data = $modelSprzet->addSkiPoles($idSkiPoles, (int)$oldSkiPoles[0]['IdProducent'], (int)$oldSkiPoles[0]['IdPlec'], $oldSkiPoles[0]['Cena']);
        }

        if($difference < 0){
            $ilosc = sizeof($allTypeSkiPolesId)-1;
            for($i=$ilosc;$i>($ilosc-abs($difference));$i--){
                $modelSkiPoles->delete($allTypeSkiPolesId[$i]['IdKijki']);
            }
        }

        //sprawdzamy jeszcze raz indeksy bo mogly sie zmienic
        $allTypeSkiPolesId = $modelSkiPoles->getOneType($oldSkiPoles[0]['Model'], $oldSkiPoles[0]['Dlugosc'], $oldSkiPoles[0]['IdProducent'], $oldSkiPoles[0]['IdPlec']);
        $resultSkiPoles = $modelSkiPoles->update($allTypeSkiPolesId, $newSkiPoles);
        $resultSprzet = $modelSprzet->updateSnowboard($allTypeSkiPolesId, $newSkiPoles);

        $data['msg'] = "Poprawnie zaktualizowano kijki";

        echo json_encode($data);
    }

    public function delete($id){
        $data = array();
        //$data = json_decode(file_get_contents('php://input'), true);
        //$accessController = new \Controllers\Dostep();
        //$accessController->islogin();
        $modelSkiPoles = $this->getModel("Kijki");
        //$modelSprzet = $this->getModel("Sprzet");
        $skiPoles = $modelSkiPoles->getOne($id);
        //pobranie wszystkich id
        $allTypeSkiPolesId = $modelSkiPoles->getOneType($skiPoles[0]['Model'], $skiPoles[0]['Dlugosc'], $skiPoles[0]['IdProducent'], $skiPoles[0]['IdPlec']);

        if (is_array($allTypeSkiPolesId) || is_object($allTypeSkiPolesId)) {
            foreach ($allTypeSkiPolesId as $idSkiPoles) {
                $modelSkiPoles->delete($idSkiPoles['IdKijki']);
            }
        }
        $data['msg'] = "Poprawnie usunięto sprzęt";
        echo json_encode($data);
        }

    public function getByDate(){
        $dane = json_decode(file_get_contents('php://input'), true);

        $response = array();
        $skiPolesList = array();

        $dateFrom = date('Y-m-d', strtotime($dane['dataOd']));
        $dateTo = date('Y-m-d', strtotime($dane['dataDo']));

        $modelSkiPoles = $this->getModel('Kijki');
        $modelReservation = $this->getModel('Rezerwacja');
        $modelRental = $this->getModel('Wypozyczenie');

        $allSkiPoles = $modelSkiPoles->getAll(true);

        foreach ($allSkiPoles['skiPoles'] as $item) { //po kazdym sprzecie
            $skiPolesReservation = $modelReservation->getOne($item['Id']); //wszystkie rezerwacje danego sprzetu

            if (!isset($skiPolesReservation['error'])) {
                foreach ($skiPolesReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $skiPolesList = $this->updateList($skiPolesList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $skiPolesList = $this->updateList($skiPolesList, $item, false);
                    }
                } // foreach rezerwacje
            } else {
                $response['test'] = $item;
                $skiPolesList = $this->updateList($skiPolesList, $item, true);
            }

            //sprawdzenie wypozyczen
            $skiPolesRental = $modelRental->getOne($item['Id']); //wszystkie wypozyczenia sprzetu
            if (!isset($skiPolesRental['error'])){
                foreach ($skiPolesRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $skiPolesList = $this->updateList($skiPolesList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $skiPolesList = $this->updateList($skiPolesList, $item, false);
                    }
                }
            }
        } // foreach sprzet
        //wybranie wolnego
        $tmp = array();
        $counter = 0;
        $response['snowb'] = $skiPolesList;
        foreach ($skiPolesList as $item) {
            if ($item['flaga'] == true) {
                $tmp[$counter] = $item['IdKijki'];
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
                if ($item['IdKijki'] == $idEquipment && $item['flaga']!= false) {
                    $contain = true;
                    $tableEquipment[$counter]['IdKijki'] = $idEquipment;
                    $tableEquipment[$counter]['flaga'] = $option;
                    $counter=0;
                    break;
                }else if($item['IdKijki'] == $idEquipment){
                    $contain = true;
                    $counter=0;
                    break;
                }
                $counter+=1;
            }
            if($contain == null){
                array_push($tableEquipment, ['IdKijki' => $idEquipment, 'flaga' => $option]);
            }
        }else{
            $tableEquipment[] = ['IdKijki' => $idEquipment, 'flaga' => $option];
        }

        return $tableEquipment;
    }

}