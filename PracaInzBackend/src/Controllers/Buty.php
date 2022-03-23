<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 18.11.2018
 * Time: 23:14
 */

namespace Controllers;


class Buty extends Controller
{
    public function getAll(){
        $modelBoots = $this->getModel("Buty");
        $bootsAll = $modelBoots->getAll();
        echo json_encode($bootsAll);
    }

    public function getAllSingle(){
        $modelBoots = $this->getModel("Buty");
        $bootsAll = $modelBoots->getAll(true);
        echo json_encode($bootsAll);
    }

    public function getOne($id){
        $model = $this->getModel('Buty');
        echo json_encode($model->getOne($id));
    }

    public function add()
    {
        $data = array();
        $dane = json_decode(file_get_contents('php://input'), true);

        $modelBoots = $this->getModel("Buty");
        $modelSprzet = $this->getModel("Sprzet");

        //sprawdzenie czy juz istnieje taki snowboard
        //echo json_encode($dane);
        $isBootsExist = $modelBoots->getOneType($dane['model'], $dane['rozmiar'], (int)$dane['kategoria'], (int)$dane['producent'], (int)$dane['plec']);

        if ($isBootsExist != null) {
            $data['error'] = "Sprzęt już istnieje w bazie danych";
            echo json_encode($data);
        } else {
            $idBoots = $modelBoots->add($dane['model'], $dane['rozmiar'], (int)$dane['kategoria'], (int)$dane['ilosc']);
            $data = $modelSprzet->addBoots($idBoots, (int)$dane['producent'], (int)$dane['plec'], (float)$dane['cena']);
            if($data['msg']) {
                $data['msg'] = 'Buty zostały dodane';
                echo json_encode($data);
            }
        }

    }

    public function edit(){
        $newBoots = json_decode(file_get_contents('php://input'), true);
        $modelBoots = $this->getModel("Buty");
        $modelSprzet = $this->getModel("Sprzet");
        //pobranie starych wartosci snowboardu, zeby sprawdzic ile bylo konkretnych snowboardow
        $oldBoots = $modelBoots->getOne($newBoots['id']);

        /*
         * pobranie indeksów tych samych snowboardow
         * $allTypeSnowboardId zawiera indeksy snowboardow w tabeli snowboard i indeksy z tabeli sprzet powiazanym z danym snowboardem
         */
        $allTypeBootsId = $modelBoots->getOneType($oldBoots[0]['Model'], $oldBoots[0]['Rozmiar'], $oldBoots[0]['IdKategoria'], $oldBoots[0]['IdProducent'], $oldBoots[0]['IdPlec']);
        //sprawdzenie ilosci > to dodajemy < to odejmujemy
        $difference = $newBoots['ilosc'] - sizeof($allTypeBootsId);
        if($difference > 0){
            $idBoots = $modelBoots->add($oldBoots[0]['Model'], (int)$oldBoots[0]['Rozmiar'], (float)$oldBoots[0]['IdKategoria'], $difference);
            $data = $modelSprzet->addBoots($idBoots, (int)$oldBoots[0]['IdProducent'], (int)$oldBoots[0]['IdPlec'], $oldBoots[0]['Cena']);
        }

        if($difference < 0){
            $ilosc = sizeof($allTypeBootsId)-1;
            for($i=$ilosc;$i>($ilosc-abs($difference));$i--){
                $modelBoots->delete($allTypeBootsId[$i]['IdButy']);
            }
        }

        //sprawdzamy jeszcze raz indeksy bo mogly sie zmienic
        $allTypeBootsId = $modelBoots->getOneType($oldBoots[0]['Model'], $oldBoots[0]['Rozmiar'], $oldBoots[0]['IdKategoria'], $oldBoots[0]['IdProducent'], $oldBoots[0]['IdPlec']);
        $resultBoots = $modelBoots->update($allTypeBootsId, $newBoots);
        $resultSprzet = $modelSprzet->updateSnowboard($allTypeBootsId, $newBoots);

        $data['msg'] = "Poprawnie zaktualizowano buty";

        echo json_encode($data);
    }

    public function delete($id){
        $data = array();
        //$data = json_decode(file_get_contents('php://input'), true);
        //$accessController = new \Controllers\Dostep();
        //$accessController->islogin();
        $modelBoots = $this->getModel("Buty");
        //$modelSprzet = $this->getModel("Sprzet");
        $boots = $modelBoots->getOne($id);
        //pobranie wszystkich id
        $allTypeBootsId = $modelBoots->getOneType($boots[0]['Model'], $boots[0]['Rozmiar'], $boots[0]['IdKategoria'], $boots[0]['IdProducent'], $boots[0]['IdPlec']);
        if (is_array($allTypeBootsId) || is_object($allTypeBootsId)) {
            foreach ($allTypeBootsId as $idBoots) {
                $modelBoots->delete($idBoots['IdButy']);
            }
        }
        $data['msg'] = "Poprawnie usunięto sprzęt";
        echo json_encode($data);
    }

    public function getByDate(){
        $dane = json_decode(file_get_contents('php://input'), true);

        $response = array();
        $bootsList = array();

        $dateFrom = date('Y-m-d', strtotime($dane['dataOd']));
        $dateTo = date('Y-m-d', strtotime($dane['dataDo']));

        $modelBoots = $this->getModel('Buty');
        $modelReservation = $this->getModel('Rezerwacja');
        $modelRental = $this->getModel('Wypozyczenie');

        $allBoots = $modelBoots->getAll(true);
        $response['test'] = $allBoots;
        foreach ($allBoots['boots'] as $item) { //po kazdym sprzecie
            $bootsReservation = $modelReservation->getOne($item['Id']); //wszystkie rezerwacje danego sprzetu

            if (!isset($bootsReservation['error'])) {
                foreach ($bootsReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $bootsList = $this->updateList($bootsList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $bootsList = $this->updateList($bootsList, $item, false);
                    }
                } // foreach rezerwacje
            } else {
                $bootsList = $this->updateList($bootsList, $item, true);
            }

            //sprawdzenie wypozyczen
            $bootsRental = $modelRental->getOne($item['Id']); //wszystkie wypozyczenia sprzetu
            if (!isset($bootsRental['error'])){
                foreach ($bootsRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                    $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                        $bootsList = $this->updateList($bootsList, $item, true);
                    } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                        $bootsList = $this->updateList($bootsList, $item, false);
                    }
                }
            }
        } // foreach sprzet
        //wybranie wolnego
        $tmp = array();
        $counter = 0;
        $response['snowb'] = $bootsList;
        foreach ($bootsList as $item) {
            if ($item['flaga'] == true) {
                $tmp[$counter] = $item['IdButy'];
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
                if ($item['IdButy'] == $idEquipment && $item['flaga']!= false) {
                    $contain = true;
                    $tableEquipment[$counter]['IdButy'] = $idEquipment;
                    $tableEquipment[$counter]['flaga'] = $option;
                    $counter=0;
                    break;
                }else if($item['IdButy'] == $idEquipment){
                    $contain = true;
                    $counter=0;
                    break;
                }
                $counter+=1;
            }
            if($contain == null){
                array_push($tableEquipment, ['IdButy' => $idEquipment, 'flaga' => $option]);
            }
        }else{
            $tableEquipment[] = ['IdButy' => $idEquipment, 'flaga' => $option];
        }

        return $tableEquipment;
    }
}