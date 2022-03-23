<?php

namespace Controllers;


class Wypozyczenie extends Controller
{
    public function add(){
        $headers = getallheaders();
        $token = explode(" ", $headers['Authorization']);
        $accessModel = $this->getModel("Dostep");
        $auth = $accessModel->checkJWT($token[1]);
        $viewRental = $this->getView("Wypozyczenie");
        if($auth['msg'] != null && $auth['error'] == null) {
            $data = json_decode(file_get_contents('php://input'), true);
            $modelRental = $this->getModel("Wypozyczenie");
            $dateFrom = date('Y-m-d', strtotime($data['DataOd']));
            $dateTo = date('Y-m-d', strtotime($data['DataDo']));
            $modelReservation = $this->getModel('Rezerwacja');
            if ($data['idRezerwacja'] == true) {
                foreach ($data['idSprzet'] as $item) {
                    $response = $modelRental->add($item, $data['idKlient'], $data['idPracownik'], $data['cena'], date('Y-m-d',
                        strtotime($data['DataOd'])), date('Y-m-d', strtotime($data['DataDo'])));
                }
                $modelReservation->changeStatus($data['rezerwacja'][0], 4);
                $viewRental->add($response);
            } else if ($data['snowboard'] == true) {
                $possibleRent = null;
                $modelSnowboard = $this->getModel('Snowboard');
                $snowboard = $modelSnowboard->getOne($data['idSprzet']);
                $snowboardReservation = $modelReservation->getOne($snowboard[0]['Id']);
                if (!isset($snowboardReservation['error'])) {
                    foreach ($snowboardReservation['rezerwacja'] as $reservation) {
                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo);
                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {
                            $possibleRent = false;
                        }
                    }
                } else {
                    $possibleRent = true;
                }
                //sprawdzenie wypozyczen
                $snowboardRental = $modelRental->getOne($snowboard[0]['Id']);
                if (!isset($snowboardRental['error'])) {
                    foreach ($snowboardRental['wypozyczenie'] as $rental) {
                        $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo);
                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {
                            $possibleRent = false;
                        }
                    }
                }
                if ($possibleRent) {
                    $response = $modelRental->add($snowboard[0]['Id'], $data['klient'], $data['idPracownik'], $data['cena'], $dateFrom, $dateTo);
                    $viewRental->add($response);
                } else {
                    $response['error'] = "Sprzęt niedostępny";
                    $viewRental->add($response);
                }
            } else if ($data['skis'] == true) {   //koniec if snowboard, sprawdzenie nart
                $possibleRent = null;
                $modelSkis = $this->getModel('Narty');
                $modelReservation = $this->getModel('Rezerwacja');
                $skis = $modelSkis->getOne($data['idSprzet']);

                $skisReservation = $modelReservation->getOne($skis[0]['Id']); //wszystkie rezerwacje danego sprzetu
                if (!isset($skisReservation['error'])) {
                    foreach ($skisReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                            $possibleRent = false;
                        }
                    } // foreach rezerwacje
                } else {
                    $possibleRent = true;
                }

                //sprawdzenie wypozyczen
                $skisRental = $modelRental->getOne($skis[0]['Id']); //wszystkie wypozyczenia sprzetu
                if (!isset($skisRental['error'])) {
                    foreach ($skisRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                        $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                            $possibleRent = false;
                        }
                    }
                }
                if ($possibleRent) {
                    $response = $modelRental->add($skis[0]['Id'], $data['klient'], $data['idPracownik'], $data['cena'], $dateFrom, $dateTo);
                    $viewRental->add($response);
                } else {
                    $response['error'] = "Sprzęt niedostępny";
                    $viewRental->add($response);
                }
            } else if ($data['skiPoles'] == true) {   //koniec nart, sprawdzenie kijkow
                $possibleRent = null;
                $modelSkiPoles = $this->getModel('Kijki');
                $modelReservation = $this->getModel('Rezerwacja');
                $skiPoles = $modelSkiPoles->getOne($data['idSprzet']);

                $skiPolesReservation = $modelReservation->getOne($skiPoles[0]['Id']); //wszystkie rezerwacje danego sprzetu
                if (!isset($skiPolesReservation['error'])) {
                    foreach ($skiPolesReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                            $possibleRent = false;
                        }
                    } // foreach rezerwacje
                } else {
                    $possibleRent = true;
                }

                //sprawdzenie wypozyczen
                $skiPolesRental = $modelRental->getOne($skiPoles[0]['Id']); //wszystkie wypozyczenia sprzetu
                if (!isset($skiPolesRental['error'])) {
                    foreach ($skiPolesRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                        $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                            $possibleRent = false;
                        }
                    }
                }
                if ($possibleRent) {
                    $response = $modelRental->add($skiPoles[0]['Id'], $data['klient'], $data['idPracownik'], $data['cena'], $dateFrom, $dateTo);
                    $viewRental->add($response);
                } else {
                    $response['error'] = "Sprzęt niedostępny";
                    $viewRental->add($response);
                }
            } else if ($data['boots'] == true) {  //koniec kijkow, poczatek butow
                $possibleRent = null;
                $modelBoots = $this->getModel('Buty');
                $modelReservation = $this->getModel('Rezerwacja');
                $boots = $modelBoots->getOne($data['idSprzet']);

                $bootsReservation = $modelReservation->getOne($boots[0]['Id']); //wszystkie rezerwacje danego sprzetu
                if (!isset($bootsReservation['error'])) {
                    foreach ($bootsReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu
                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                            $possibleRent = false;
                        }
                    } // foreach rezerwacje
                } else {
                    $possibleRent = true;
                }

                //sprawdzenie wypozyczen
                $bootsRental = $modelRental->getOne($boots[0]['Id']); //wszystkie wypozyczenia sprzetu
                if (!isset($bootsRental['error'])) {
                    foreach ($bootsRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                        $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                            $possibleRent = true;
                        } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                            $possibleRent = false;
                        }
                    }
                }
                if ($possibleRent) {
                    $response = $modelRental->add($boots[0]['Id'], $data['klient'], $data['idPracownik'], $data['cena'], $dateFrom, $dateTo);
                    $viewRental->add($response);
                } else {
                    $response['error'] = "Sprzęt niedostępny";
                    $viewRental->add($response);
                }
            } else {
                $response['error'] = "Nie udało się wypożyczyć sprzętu";
                $viewRental->add($response);
            }
        } else {
            $response['error'] = $auth['error'];
            $viewRental->add($response);
        }
    }

    public function getAll(){

        $data = json_decode(file_get_contents('php://input'), true);
        $modelRental = $this->getModel("Wypozyczenie");
        echo json_encode($modelRental->getAll($data['status']));
    }


    public function addform(){
        $viewWypozyczenie = $this->getView("Wypozyczenie");
        $viewWypozyczenie->addform();
    }

    public function returnEquipment(){
        $response = array();
        $response['error'] = null;
        $response['msg'] = null;

        $today = date('Y-m-d');

        $id = json_decode(file_get_contents('php://input'),true);
        $modelRent = $this->getModel("Wypozyczenie");
        $modelEquipment = $this->getModel("Sprzet");

        if ($id['flag'] == false) {
            $oneRent = $modelRent->getOne($id['rent']);
            if (strtotime($today) > strtotime($oneRent['wypozyczenie'][0]['DataDo'])) {
                $response['error'] = "Spoźniony zwrot";
                $response['planowanaData'] = $oneRent['wypozyczenie'][0]['DataDo'];
            } else {
                $responseRent = $modelRent->changeStatus($id['rent'], $today);
                $response = $modelEquipment->changeToReturn($id['equipment']);
                if ($responseRent['error'] == null){
                    $responseEquipment = $modelEquipment->changeToReturn($id['equipment']);
                    if ($responseEquipment['error'] == null){
                        $response['msg'] = "Zwrócono sprzęt";
                    }else {
                        $response['error'] = $responseEquipment['error'];
                    }
                } else {
                    $response['error'] = $responseRent['error'];
                }
            }
            echo json_encode($response);
        }

        if ($id['flag'] == true) {
            $responseRent = $modelRent->changeStatus($id['rent'], $today);
            $response = $modelEquipment->changeToReturn($id['equipment']);
            if ($responseRent['error'] == null){
                $responseEquipment = $modelEquipment->changeToReturn($id['equipment']);
                if ($responseEquipment['error'] == null){
                    $response['msg'] = "Zwrócono sprzęt";
                }else {
                    $response['error'] = $responseEquipment['error'];
                }
            } else {
                $response['error'] = $responseRent['error'];
            }

            echo json_encode($response);

        }





    }

}