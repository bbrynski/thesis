<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 03.06.2018
 * Time: 22:21
 */

namespace Controllers;


class Rezerwacja extends Controller
{
    public function add()
    {
        $snowboardReservation = true;
        $skisReservation = true;
        $bootsReservation = true;
        $skiPolesReservation = true;
        $reservationAmount = 3;
        $idCustomer = null;

        $idEquipmentSnowboard = 0;
        $idEquipmentSkis = 1;
        $idEquipmentBoots = 2;
        $idEquipmentSkiPoles = 3;

        $snowboardList = array();
        $skisList = array();
        $skiPolesList = array();
        $bootsList = array();

        $ReservationAmount = 0;
        $availableDates = array();
        $reservationData = json_decode(file_get_contents('php://input'), true);

        if (isset($reservationData['skisOptions'])) {
            $skisReservation = false;
        }
        if (isset($reservationData['snowboardOptions'])) {
            $snowboardReservation = false;
        }
        if (isset($reservationData['boots'])) {
            $bootsReservation = false;
        }
        if (isset($reservationData['skiPoles'])) {
            $skiPolesReservation = false;
        }

        $response = array();
        $idEquipment = array(); //id sprzętów do rezerwacji
        $modelReservation = $this->getModel('Rezerwacja');
        $modelRental = $this->getModel('Wypozyczenie');

        $dateFrom = date('Y-m-d', strtotime($reservationData['dateFrom']));
        $dateTo = date('Y-m-d', strtotime($reservationData['dateTo']));

        $response['snowboard']['available'] = array();
        $response['skis']['available'] = array();
        $response['skiPoles']['available'] = array();
        $response['boots']['available'] = array();

        /**************************************************************************************************
         * Klient sprawdzenie ilosci rezerwacji
         */
        $modelCustomer = $this->getModel('Klient');
        $responseCheckCustomer = $modelCustomer->check($reservationData['customer']['imie'], $reservationData['customer']['nazwisko'], $reservationData['customer']['telefon'], $reservationData['customer']['email']);
        if (isset($responseCheckCustomer["Klient"])) {
            $idCustomer = $responseCheckCustomer["Klient"][0]["Id"];
            $amountReservationCustomer = $responseCheckCustomer["Klient"][0]["IloscRezerwacji"];
        }

        if (isset($amountReservationCustomer) && $amountReservationCustomer >= $reservationAmount) {
            $response['error'] = "Przekroczono maksymalną liczbę rezerwacji, która wynosi 3";
            echo json_encode($response);
        } else {


            /**************************************************************************************************
             * Obsluga snowboard
             */
            //jesli ustawiony snowboard
            if (isset($reservationData['snowboardOptions']) && $reservationData['snowboardOptions'] != null) {
                $modelSnowboard = $this->getModel('Snowboard');
                $snowboard = $modelSnowboard->getOne($reservationData['snowboardOptions']['idSnowboard']);

                $snowboardOneType = $modelSnowboard->getOneType($snowboard[0]);
                foreach ($snowboardOneType as $item) {  //foreach po kazdym sprzecie
                    $snowboardOneTypeReservation = $modelReservation->getOne($item['IdSprzet']); //wszystkie rezerwacje danego sprzetu
                    $snowboardOneTypeRental = $modelRental->getOne($item['IdSprzet']); //wszystkie wypozyczenia sprzetu

                    //sprawdzenie czy sa rezerwacje
                    if (!isset($snowboardOneTypeReservation['error'])) {

                        foreach ($snowboardOneTypeReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu

                            $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {

                                $snowboardList = $this->updateEquipmentToReservationList($snowboardList, $item['IdSprzet'], true);

                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach

                                $snowboardList = $this->updateEquipmentToReservationList($snowboardList, $item['IdSprzet'], false);

                                if (!isset($response['snowboard']['occupied'])) {
                                    $response['snowboard']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['snowboard']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['snowboard']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }

                                $ReservationAmount += 1;
                                $defaultRange = 7;  //zakres sprawdzania wolnych terminów -7 i +7
                                $amountDays = round((strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24), 0); //ilosc dni
                                //jeśli ilość dni zarezerwowanych jest równa lub większa 7 to zakres zostaje powiększony o 7
                                while ($amountDays >= $defaultRange) {
                                    $defaultRange = $defaultRange + 7;
                                }
                                //sprawdzenie -7
                                $newDateFrom = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateFrom))); //-7
                                $newDateTo = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateTo))); // -7
                                for ($i = 0; $i < $defaultRange; $i++) {
                                    if (strtotime($newDateFrom) > strtotime(date("Y-m-d"))) {
                                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                            if (!isset($availableDates['snowboard']['available'])) {
                                                $response['snowboard']['available'] = array();
                                                $availableDates['snowboard']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                            } else {
                                                if (!$this->ifContains($availableDates['snowboard']['available'], $newDateFrom, $newDateTo)) {
                                                    array_push($availableDates['snowboard']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                                } else {
                                                    $availableDates['snowboard']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'snowboard');
                                                }
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("1 day", strtotime($newDateFrom))); //zwiekszenie o 1
                                    $newDateTo = date('Y-m-d', strtotime("1 day", strtotime($newDateTo))); // zwiekszenie o 1
                                }

                                // sprawdzenie +7
                                $newDateFrom = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateFrom))); //+7
                                $newDateTo = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateTo))); // +7
                                for ($i = 0; $i < $defaultRange; $i++) {
                                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                    //$response[$item['IdSprzet']] = $responseCheckDate;
                                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                        if (!isset($availableDates['snowboard']['available'])) {
                                            $response['snowboard']['available'] = array();
                                            $availableDates['snowboard']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];

                                        } else {
                                            if (!$this->ifContains($availableDates['snowboard']['available'], $newDateFrom, $newDateTo)) {
                                                array_push($availableDates['snowboard']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                            } else {
                                                $availableDates['snowboard']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'snowboard');
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("-1 day", strtotime($newDateFrom)));
                                    $newDateTo = date('Y-m-d', strtotime("-1 day", strtotime($newDateTo)));
                                }
                            }

                        } //foreach po rezerwacjach sprzetu
                        $response['testtest24'] = $availableDates;

                        //sprawdzenie czy mozna zarezerwowac na dana date
                        if (isset($availableDates['snowboard']['available'])) {
                            foreach ($availableDates['snowboard']['available'] as $available) {
                                if ($available['Ilosc'] == $ReservationAmount) {
                                    array_push($response['snowboard']['available'], $available);
                                }
                            }

                            $ReservationAmount = 0;
                            $availableDates['snowboard']['available'] = array();
                        }

                    } else { //koniec czy ma rezerwacje sprzet -> else nie jest w rezerwacjach mozna dodac
                        $snowboardList = $this->updateEquipmentToReservationList($snowboardList, $item['IdSprzet'], true);
                    }


                    //sprawdzenie wypozyczen

                    if (!isset($snowboardOneTypeRental['error'])){

                        foreach ($snowboardOneTypeRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu


                            $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności snowboardu

                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                $snowboardList = $this->updateEquipmentToReservationList($snowboardList, $item['IdSprzet'], true);
                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                                $snowboardList = $this->updateEquipmentToReservationList($snowboardList, $item['IdSprzet'], false);
                                if (!isset($response['snowboard']['occupied'])) {
                                    $response['snowboard']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['snowboard']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['snowboard']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }

                                /*
                                $count = 0;
                                $tmp=array();
                                foreach ($response['snowboard']['available'] as $availableDate){
                                    $responseCheckAvailableDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $availableDate['DataOd'], $availableDate['DataOd']);
                                    if($item['IdSprzet'] == 82) {
                                        $response['test'] = $responseCheckAvailableDate;
                                    }
                                    if(isset($responseCheckAvailableDate['msg']) && $responseCheckAvailableDate['msg'] == true){
                                        $tmp[$count] = $availableDate;
                                        $count +=1;
                                    }
                                }

                               $response['snowboard']['available'] = $tmp;
*/
                            }



                        }

                    }




                } //foreach po sprzecie */

                //wybranie wolnego
                foreach ($snowboardList as $item) {
                    if ($item['flaga'] == true) {
                        $snowboardAvailable = true;
                        $snowboardReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                        $response['snowboard']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                        $idEquipment[$idEquipmentSnowboard]['snowboard'] = array('id' => $item['IdSprzet'], 'options' => null); //dodanie id snowboardu do tabeli zbiorczej rezerwacji
                        break;
                    }
                }

                //sprawdzenie wolnych
                $response['testtest25'] = $response['snowboard']['available'];
                if (isset($response['snowboard']['occupied'])) {
                    $response['snowboard']['available'] = $this->checkAvailableDatesFromRentalAndReservation($response['snowboard']['available'], $response['snowboard']['occupied']);
                }
                /*$tmp = array();
                $count = 0;
                foreach ($response['snowboard']['occupied'] as $occ){
                    foreach ($response['snowboard']['available'] as $ava){

                        $responseCheckAvailableDate = $modelReservation->checkDate($ava['DataOd'], $ava['DataDo'], $occ['DataOd'], $occ['DataOd']);

                        if($responseCheckAvailableDate['msg'] == false){
                            $response['test2'] = $responseCheckAvailableDate;
                            $tmp[$count] = $ava;
                            $count +=1;
                        }
                    }
                }
                $tmp2 = array();
                $count = 0;
                foreach ($response['snowboard']['available'] as $ava){
                    foreach ($tmp as $t){
                        if($ava['DataOd'] == $t['DataOd'] && $ava['DataDo'] == $t['DataDo']){

                        } else {
                            $tmp2[$count] = $ava;
                            $count +=1;
                        }
                    }
                }
*/




                if (!isset($snowboardAvailable)) {
                    $response['snowboard']['error'] = 'Niedostępny';
                }


            }
            /**************************************************************************************************
             * Koniec snowboard
             *
             * Początek nart
             */
            //jesli ustawione narty
            if (isset($reservationData['skisOptions']) && $reservationData['skisOptions'] != null) {
                $ReservationAmount = 0;
                $modelSkis = $this->getModel('Narty');
                $skis = $modelSkis->getOne($reservationData['skisOptions']['idNarty']);
                $skisOneType = $modelSkis->getOneType($skis[0]['Model'], $skis[0]['IdPrzeznaczenie'], $skis[0]['Dlugosc'], $skis[0]['IdProducent'], $skis[0]['IdPlec']);

                foreach ($skisOneType as $item) {
                    $skisOneTypeReservation = $modelReservation->getOne($item['IdSprzet']); //wszystkie rezerwacje danego sprzetu
                    $skisOneTypeRental = $modelRental->getOne($item['IdSprzet']); //wszystkie wypozyczenia sprzetu
                    //sprawdzenie czy sa rezerwacje
                    if (!isset($skisOneTypeReservation['error'])) {

                        foreach ($skisOneTypeReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                            $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat


                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {

                                $skisList = $this->updateEquipmentToReservationList($skisList, $item['IdSprzet'], true);
                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach

                                $skisList = $this->updateEquipmentToReservationList($skisList, $item['IdSprzet'], false);

                                if (!isset($response['skis']['occupied'])) {
                                    $response['skis']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['skis']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['skis']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }

                                $ReservationAmount += 1;
                                $defaultRange = 7;  //zakres sprawdzania wolnych terminów -7 i +7
                                $amountDays = round((strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24), 0); //ilosc dni
                                //jeśli ilość dni zarezerwowanych jest równa lub większa 7 to zakres zostaje powiększony o 7
                                while ($amountDays >= $defaultRange) {
                                    $defaultRange = $defaultRange + 7;
                                }
                                //sprawdzenie -7
                                $newDateFrom = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateFrom))); //-7
                                $newDateTo = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateTo))); // -7

                                for ($i = 0; $i < $defaultRange; $i++) {
                                    if (strtotime($newDateFrom) > strtotime(date("Y-m-d"))) {
                                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                            if (!isset($availableDates['skis']['available'])) {
                                                $response['skis']['available'] = array();
                                                $availableDates['skis']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                            } else {
                                                if (!$this->ifContains($availableDates['skis']['available'], $newDateFrom, $newDateTo)) {
                                                    array_push($availableDates['skis']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                                } else {
                                                    $availableDates['skis']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'skis');
                                                }
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("1 day", strtotime($newDateFrom))); //zwiekszenie o 1
                                    $newDateTo = date('Y-m-d', strtotime("1 day", strtotime($newDateTo))); // zwiekszenie o 1
                                }

                                // sprawdzenie +7
                                $newDateFrom = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateFrom))); //+7
                                $newDateTo = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateTo))); // +7

                                for ($i = 0; $i < $defaultRange; $i++) {
                                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                        if (!isset($availableDates['skis']['available'])) {
                                            $response['skis']['available'] = array();
                                            $availableDates['skis']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                        } else {
                                            if (!$this->ifContains($availableDates['skis']['available'], $newDateFrom, $newDateTo)) {
                                                array_push($availableDates['skis']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                            } else {
                                                $availableDates['skis']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'skis');
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("-1 day", strtotime($newDateFrom)));
                                    $newDateTo = date('Y-m-d', strtotime("-1 day", strtotime($newDateTo)));
                                }
                            }
                        }//foreach po rezerwacjach danego sprzetu
                        //sprawdzenie czy mozna zarezerwowac na dana date
                        if (isset($availableDates['skis']['available'])) {
                            foreach ($availableDates['skis']['available'] as $available) {
                                if ($available['Ilosc'] == $ReservationAmount) {
                                    array_push($response['skis']['available'], $available);
                                }
                            }
                            $ReservationAmount = 0;
                            $availableDates['skis']['available'] = array();
                        }
                    } else { //koniec czy ma rezerwacje sprzet -> else nie jest w rezerwacjach mozna dodac
                        $skisList = $this->updateEquipmentToReservationList($skisList, $item['IdSprzet'], true);
                    }

                    //sprawdzenie wypozyczen
                    if (!isset($skisOneTypeRental['error'])){

                        foreach ($skisOneTypeRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu


                            $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat


                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {

                                $skisList = $this->updateEquipmentToReservationList($skisList, $item['IdSprzet'], true);
                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach

                                $skisList = $this->updateEquipmentToReservationList($skisList, $item['IdSprzet'], false);

                                if (!isset($response['skis']['occupied'])) {
                                    $response['skis']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['skis']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['skis']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }
                            }
                        }
                    }//if narty wypozyczenie


                }//foreach po sprzet

                //wybranie wolnego - pierwszy wolne biere
                foreach ($skisList as $item) {
                    if ($item['flaga'] == true) {
                        $skisAvailable = true;
                        $skisReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                        $response['skis']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                        $idEquipment[$idEquipmentSkis]['skis'] = array('id' => $item['IdSprzet'], 'options' => null);
                        break;
                    }
                }

                //sprawdzenie wolnych
                if (isset($response['skis']['occupied'])) {
                    $response['skis']['available'] = $this->checkAvailableDatesFromRentalAndReservation($response['skis']['available'], $response['skis']['occupied']);
                }
                if (!isset($skisAvailable)) {
                    $response['skis']['error'] = 'Niedostępny';
                }
            }

            /**************************************************************************************************
             * Koniec nart
             *
             * Poczatek kijków
             */

            //jesli ustawione kijki
            if (isset($reservationData['skiPoles']) && $reservationData['skiPoles'] != null) {
                $ReservationAmount = 0;
                $modelSkiPoles = $this->getModel('Kijki');
                $skiPoles = $modelSkiPoles->getOne($reservationData['skiPoles']);
                $skiPolesOneType = $modelSkiPoles->getOneType($skiPoles[0]['Model'], $skiPoles[0]['Dlugosc'], $skiPoles[0]['IdProducent'], $skiPoles[0]['IdPlec']);

                foreach ($skiPolesOneType as $item) {

                    $skiPolesOneTypeReservation = $modelReservation->getOne($item['IdSprzet']); //wszystkie rezerwacje danego sprzetu
                    $skiPolesOneTypeRental = $modelRental->getOne($item['IdSprzet']); //wszystkie wypozyczenia sprzetu

                    //sprawdzenie czy sa rezerwacje
                    if (!isset($skiPolesOneTypeReservation['error'])) {

                        foreach ($skiPolesOneTypeReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                            $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności

                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {

                                $skiPolesList = $this->updateEquipmentToReservationList($skiPolesList, $item['IdSprzet'], true);
                                //$skiPolesReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                                //$response['skiPoles']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                                //$idEquipment[] = $item['IdSprzet']; //dodanie id snowboardu do tabeli zbiorczej rezerwacji

                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                                $skiPolesList = $this->updateEquipmentToReservationList($skiPolesList, $item['IdSprzet'], false);
                                //$response['skiPoles']['error'] = 'Niedostępny'; //informacja, że niedostępny

                                if (!isset($response['skiPoles']['occupied'])) {
                                    $response['skiPoles']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['skiPoles']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['skiPoles']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }

                                $ReservationAmount += 1;
                                $defaultRange = 7;  //zakres sprawdzania wolnych terminów -7 i +7
                                $amountDays = round((strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24), 0); //ilosc dni
                                //jeśli ilość dni zarezerwowanych jest równa lub większa 7 to zakres zostaje powiększony o 7
                                while ($amountDays >= $defaultRange) {
                                    $defaultRange = $defaultRange + 7;
                                }
                                //sprawdzenie -7
                                $newDateFrom = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateFrom))); //-7
                                $newDateTo = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateTo))); // -7

                                for ($i = 0; $i < $defaultRange; $i++) {
                                    if (strtotime($newDateFrom) > strtotime(date("Y-m-d"))) {
                                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                            if (!isset($availableDates['skiPoles']['available'])) {
                                                $response['skiPoles']['available'] = array();
                                                $availableDates['skiPoles']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                            } else {
                                                if (!$this->ifContains($availableDates['skiPoles']['available'], $newDateFrom, $newDateTo)) {
                                                    array_push($availableDates['skiPoles']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                                } else {
                                                    $availableDates['skiPoles']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'skiPoles');
                                                }
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("1 day", strtotime($newDateFrom))); //zwiekszenie o 1
                                    $newDateTo = date('Y-m-d', strtotime("1 day", strtotime($newDateTo))); // zwiekszenie o 1
                                }

                                // sprawdzenie +7
                                $newDateFrom = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateFrom))); //+7
                                $newDateTo = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateTo))); // +7

                                for ($i = 0; $i < $defaultRange; $i++) {
                                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                        if (!isset($availableDates['skiPoles']['available'])) {
                                            $response['skiPoles']['available'] = array();
                                            $availableDates['skiPoles']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                        } else {
                                            if (!$this->ifContains($availableDates['skiPoles']['available'], $newDateFrom, $newDateTo)) {
                                                array_push($availableDates['skiPoles']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                            } else {
                                                $availableDates['skiPoles']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'skiPoles');
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("-1 day", strtotime($newDateFrom)));
                                    $newDateTo = date('Y-m-d', strtotime("-1 day", strtotime($newDateTo)));
                                }
                            }
                        }
                        //sprawdzenie czy mozna zarezerwowac na dana date
                        if (isset($availableDates['skiPoles']['available'])) {
                            foreach ($availableDates['skiPoles']['available'] as $available) {
                                if ($available['Ilosc'] == $ReservationAmount) {
                                    array_push($response['skiPoles']['available'], $available);
                                }
                            }
                            $ReservationAmount = 0;
                            $availableDates['skiPoles']['available'] = array();
                        }
                    } else { //koniec czy ma rezerwacje sprzet -> else nie jest w rezerwacjach mozna dodac
                        $skiPolesList = $this->updateEquipmentToReservationList($skiPolesList, $item['IdSprzet'], true);
                    }

                    //sprawdzenie wypozyczen

                    if (!isset($skiPolesOneTypeRental['error'])){
                        foreach ($skiPolesOneTypeRental['wypozyczenie'] as $rental) {
                            $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności

                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {

                                $skiPolesList = $this->updateEquipmentToReservationList($skiPolesList, $item['IdSprzet'], true);
                                //$skiPolesReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                                //$response['skiPoles']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                                //$idEquipment[] = $item['IdSprzet']; //dodanie id snowboardu do tabeli zbiorczej rezerwacji

                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                                $skiPolesList = $this->updateEquipmentToReservationList($skiPolesList, $item['IdSprzet'], false);
                                //$response['skiPoles']['error'] = 'Niedostępny'; //informacja, że niedostępny

                                if (!isset($response['skiPoles']['occupied'])) {
                                    $response['skiPoles']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['skiPoles']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['skiPoles']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }
                            }
                        }
                    }


                }//foreach po sprzecie
                //wybranie wolnego - pierwszy wolne biere
                foreach ($skiPolesList as $item) {
                    if ($item['flaga'] == true) {
                        $skiPolesAvailable = true;
                        $skiPolesReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                        $response['skiPoles']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                        $idEquipment[$idEquipmentSkiPoles]['skiPoles'] = $item['IdSprzet'];
                        break;
                    }
                }

                //sprawdzenie wolnych
                if (isset($response['skiPoles']['occupied'])) {
                    $response['skiPoles']['available'] = $this->checkAvailableDatesFromRentalAndReservation($response['skiPoles']['available'], $response['skiPoles']['occupied']);
                }
                if (!isset($skiPolesAvailable)) {
                    $response['skiPoles']['error'] = 'Niedostępny';
                }
            }

            /**************************************************************************************************
             * Koniec kijków
             *
             * Poczatek buty
             */

            //jesli ustawione buty
            if (isset($reservationData['boots']) && $reservationData['boots'] != null) {
                $ReservationAmount = 0;
                $modelBoots = $this->getModel('Buty');
                $boots = $modelBoots->getOne($reservationData['boots']);
                $bootsOneType = $modelBoots->getOneType($boots[0]['Model'], $boots[0]['Rozmiar'], $boots[0]['IdKategoria'], $boots[0]['IdProducent'], $boots[0]['IdPlec']);
                //$response['test'] = $bootsOneType;

                foreach ($bootsOneType as $item) {

                    $bootsOneTypeReservation = $modelReservation->getOne($item['IdSprzet']); //wszystkie rezerwacje danego sprzetu
                    $bootsOneTypeRental = $modelRental->getOne($item['IdSprzet']); //wszystkie wypozyczenia sprzetu

                    //sprawdzenie czy sa rezerwacje
                    if (!isset($bootsOneTypeReservation['error'])) {

                        foreach ($bootsOneTypeReservation['rezerwacja'] as $reservation) { //po kazdej rezerwacji tego sprzetu
                            $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności

                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                $bootsList = $this->updateEquipmentToReservationList($bootsList, $item['IdSprzet'], true);
                                //$bootsReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                                //$response['boots']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                                //$idEquipment[] = $item['IdSprzet']; //dodanie id snowboardu do tabeli zbiorczej rezerwacji
                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                                $bootsList = $this->updateEquipmentToReservationList($bootsList, $item['IdSprzet'], false);
                                //$response['boots']['error'] = 'Niedostępny'; //informacja, że niedostępny
                                if (!isset($response['boots']['occupied'])) {
                                    $response['boots']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['boots']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['boots']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }

                                $ReservationAmount += 1;
                                $defaultRange = 7;  //zakres sprawdzania wolnych terminów -7 i +7
                                $amountDays = round((strtotime($dateTo) - strtotime($dateFrom)) / (60 * 60 * 24), 0); //ilosc dni
                                //jeśli ilość dni zarezerwowanych jest równa lub większa 7 to zakres zostaje powiększony o 7
                                while ($amountDays >= $defaultRange) {
                                    $defaultRange = $defaultRange + 7;
                                }
                                //sprawdzenie -7
                                $newDateFrom = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateFrom))); //-7
                                $newDateTo = date('Y-m-d', strtotime("-" . $defaultRange . " day", strtotime($dateTo))); // -7

                                for ($i = 0; $i < $defaultRange; $i++) {
                                    if (strtotime($newDateFrom) > strtotime(date("Y-m-d"))) {
                                        $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                        if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                            if (!isset($availableDates['boots']['available'])) {
                                                $response['boots']['available'] = array();
                                                $availableDates['boots']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                            } else {
                                                if (!$this->ifContains($availableDates['boots']['available'], $newDateFrom, $newDateTo)) {
                                                    array_push($availableDates['boots']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                                } else {
                                                    $availableDates['boots']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'boots');
                                                }
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("1 day", strtotime($newDateFrom))); //zwiekszenie o 1
                                    $newDateTo = date('Y-m-d', strtotime("1 day", strtotime($newDateTo))); // zwiekszenie o 1
                                }

                                // sprawdzenie +7
                                $newDateFrom = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateFrom))); //+7
                                $newDateTo = date('Y-m-d', strtotime($defaultRange . " day", strtotime($dateTo))); // +7

                                for ($i = 0; $i < $defaultRange; $i++) {
                                    $responseCheckDate = $modelReservation->checkDate($reservation['DataOd'], $reservation['DataDo'], $newDateFrom, $newDateTo);
                                    if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                        if (!isset($availableDates['boots']['available'])) {
                                            $response['boots']['available'] = array();
                                            $availableDates['boots']['available'][] = ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1];
                                        } else {
                                            if (!$this->ifContains($availableDates['boots']['available'], $newDateFrom, $newDateTo)) {
                                                array_push($availableDates['boots']['available'], ['DataOd' => $newDateFrom, 'DataDo' => $newDateTo, 'Ilosc' => 1]);
                                            } else {
                                                $availableDates['boots']['available'] = $this->updateAvailableDates($availableDates, $newDateFrom, $newDateTo, 'boots');
                                            }
                                        }
                                    }
                                    $newDateFrom = date('Y-m-d', strtotime("-1 day", strtotime($newDateFrom)));
                                    $newDateTo = date('Y-m-d', strtotime("-1 day", strtotime($newDateTo)));
                                }
                            }
                        }
                        //sprawdzenie czy mozna zarezerwowac na dana date
                        if (isset($availableDates['boots']['available'])) {
                            foreach ($availableDates['boots']['available'] as $available) {
                                if ($available['Ilosc'] == $ReservationAmount) {
                                    array_push($response['boots']['available'], $available);
                                }
                            }
                            $ReservationAmount = 0;
                            $availableDates['boots']['available'] = array();
                        }
                    } else { //koniec czy ma rezerwacje sprzet -> else nie jest w rezerwacjach mozna dodac
                        $bootsList = $this->updateEquipmentToReservationList($bootsList, $item['IdSprzet'], true);
                    }

                    //sprawdzenie wypozyczen

                    if (!isset($bootsOneTypeRental['error'])) {

                        foreach ($bootsOneTypeRental['wypozyczenie'] as $rental) { //po kazdej rezerwacji tego sprzetu
                            $responseCheckDate = $modelReservation->checkDate($rental['DataOd'], $rental['DataDo'], $dateFrom, $dateTo); //sprawdzenie dat dostępności

                            if (isset($responseCheckDate['msg']) && $responseCheckDate['msg'] == true) {
                                $bootsList = $this->updateEquipmentToReservationList($bootsList, $item['IdSprzet'], true);
                                //$bootsReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                                //$response['boots']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                                //$idEquipment[] = $item['IdSprzet']; //dodanie id snowboardu do tabeli zbiorczej rezerwacji
                            } else if (isset($responseCheckDate['zajete'])) {   //jeśli sprzęt nie jest wolny to zapisujemy o info o zajętych i wolnych terminach
                                $bootsList = $this->updateEquipmentToReservationList($bootsList, $item['IdSprzet'], false);
                                //$response['boots']['error'] = 'Niedostępny'; //informacja, że niedostępny
                                if (!isset($response['boots']['occupied'])) {
                                    $response['boots']['occupied'] = ($responseCheckDate['zajete']);
                                } else {
                                    if (!$this->ifContains($response['boots']['occupied'], $responseCheckDate['zajete'][0]['DataOd'], $responseCheckDate['zajete'][0]['DataDo'])) {
                                        array_push($response['boots']['occupied'], $responseCheckDate['zajete'][0]);
                                    }
                                }
                            }
                        }
                    }


                }// foreach po sprzecie
                //wybranie wolnego - pierwszy wolne biere
                foreach ($bootsList as $item) {
                    if ($item['flaga'] == true) {
                        $bootsAvailable = true;
                        $bootsReservation = true;  //zmiana na true -> sprzęt dostępny można rezerwować
                        $response['boots']['msg'] = 'Dostępny'; //informacja zwrotna czy dostępny
                        $idEquipment[$idEquipmentBoots]['boots'] = $item['IdSprzet'];
                        break;
                    }
                }

                //sprawdzenie wolnych
                if (isset($response['boots']['occupied'])) {
                    $response['boots']['available'] = $this->checkAvailableDatesFromRentalAndReservation($response['boots']['available'], $response['boots']['occupied']);
                }
                if (!isset($bootsAvailable)) {
                    $response['boots']['error'] = 'Niedostępny';
                }
            }

            /**************************************************************************************************
             * Koniec buty
             *
             * Rezerwacja
             */
            //sprawdzenie czy mozliwa rezerwacja wtedy sprawdzamy klienta w bazie
            if ($snowboardReservation && $skisReservation && $bootsReservation && $skiPolesReservation) {
                if ($idEquipment != null) {

                    //jesli mozna rezerwowac to dodajemy ustawienia do bazy
                    if (isset($reservationData['snowboardOptions'])) {
                        $modelSnowboardOptions = $this->getModel('SnowboardOpcje');
                        $respondeSnowboardOptions = $modelSnowboardOptions->check($reservationData['snowboardOptions']['ustawienie'], $reservationData['snowboardOptions']['katL'], $reservationData['snowboardOptions']['katP']);
                        if (isset($respondeSnowboardOptions['snowboardOptions'])) {
                            $idEquipment[$idEquipmentSnowboard]['snowboard']['options'] = $respondeSnowboardOptions['snowboardOptions'][0]['Id'];
                            //$modelSnowboard->updateOptions($item['IdSnowboard'], $respondeSnowboardOptions['snowboardOptions'][0]['Id']);
                        } else {
                            $idSnowboardOptions = $modelSnowboardOptions->add($reservationData['snowboardOptions']['ustawienie'], $reservationData['snowboardOptions']['katL'], $reservationData['snowboardOptions']['katP']);
                            $idEquipment[$idEquipmentSnowboard]['snowboard']['options'] = $idSnowboardOptions;
                            //$modelSnowboard->updateOptions($item['IdSnowboard'], $idSnowboardOptions);  //aktualizacja danego snowboardu o dodatkowe opcje
                        }
                    }

                    //dodatkowe opcje narty
                    if (isset($reservationData['skisOptions'])) {
                        $modelSkisOptions = $this->getModel('NartyOpcje');
                        $respondeSkisOptions = $modelSkisOptions->check($reservationData['skisOptions']['waga'], $reservationData['skisOptions']['rozmiarButa']);
                        if (isset($respondeSkisOptions['skisOptions'])) {
                            $idEquipment[$idEquipmentSkis]['skis']['options'] = $respondeSkisOptions['skisOptions'][0]['Id'];
                            //$modelSkis->updateOptions($item['IdNarty'], $respondeSkisOptions['skisOptions'][0]['Id']);
                        } else {
                            $idSkisOptions = $modelSkisOptions->add($reservationData['skisOptions']['waga'], $reservationData['skisOptions']['rozmiarButa']);
                            $idEquipment[$idEquipmentSkis]['skis']['options'] = $idSkisOptions;
                            //$modelSkis->updateOptions($item['IdNarty'], $idSkisOptions);  //aktualizacja danego snowboardu o dodatkowe opcje
                        }
                    }

                    //$modelCustomer = $this->getModel('Klient');
                    //$responseCheckCustomer = $modelCustomer->check($reservationData['customer']['imie'], $reservationData['customer']['nazwisko'], $reservationData['customer']['telefon'], $reservationData['customer']['email']);
                    //czy jest klient w bazie jesli tak to rezerwujemy, jesli nie to dodajemy do bazy i dopiero rezerwujemy
                    //if (isset($responseCheckCustomer["Klient"])) {
                    if ($idCustomer != null) {
                        //$idCustomer = $responseCheckCustomer["Klient"][0]["Id"];
                        $respondeReservation = $modelReservation->add($idCustomer, $dateFrom, $dateTo, $idEquipment);
                        if (!isset($respondeReservation['error'])) {
                            $modelCustomer->updateReservationAmount($idCustomer, ($amountReservationCustomer+1));
                            $response['msg'] = "Poprawnie zarezerwowano sprzet";
                            echo json_encode($response);
                        } else {
                            $response['error'] = "Nie udało się zarezerwować sprzętu";
                        }

                    } else {
                        $idCustomer = $modelCustomer->add($reservationData['customer']['imie'], $reservationData['customer']['nazwisko'], $reservationData['customer']['telefon'], $reservationData['customer']['email']);
                        $respondeReservation = $modelReservation->add($idCustomer, $dateFrom, $dateTo, $idEquipment);
                        if (!isset($respondeReservation['error'])) {
                            $modelCustomer->updateReservationAmount($idCustomer, 1);
                            $response['msg'] = "Poprawnie zarezerwowano sprzet";
                            echo json_encode($response);
                        } else {
                            $response['error'] = "Nie udało się zarezerwować sprzętu";
                        }
                    }
                } else {
                    //zwrócenie informacji
                    echo json_encode($response);
                }
            } else {
                //zwrocenie
                $response['error'] = "Nie udało się zarezerwować sprzętu";
                echo json_encode($response);
            }
            //}
        }
    }

    public function checkAvailableDatesFromRentalAndReservation($availableDates, $occupiedDates){
        $modelReservation = $this->getModel('Rezerwacja');
        $invalidDates = array();
        $counter = 0;

        foreach ($occupiedDates as $occ){
            foreach ($availableDates as $ava){
                $responseCheckAvailableDate = $modelReservation->checkDate($ava['DataOd'], $ava['DataDo'], $occ['DataOd'], $occ['DataOd']);
                if($responseCheckAvailableDate['msg'] == false){
                    $response['test2'] = $responseCheckAvailableDate;
                    $invalidDates[$counter] = $ava;
                    $counter +=1;
                }
            }
        }

        if (empty($invalidDates)) {
        return $availableDates;
        } else {
            $correctDates = array();
            $counter = 0;
            foreach ($availableDates as $ava) {
                foreach ($invalidDates as $invalid) {
                    if ($ava['DataOd'] == $invalid['DataOd'] && $ava['DataDo'] == $invalid['DataDo']) {

                    } else {
                        $correctDates[$counter] = $ava;
                        $counter += 1;
                    }
                }
            }

            return $correctDates;
        }
    }

    public function ifContains($table, $dateFrom, $dateTo){
        foreach ($table as $item){
            if($item['DataOd'] == $dateFrom && $item['DataDo'] == $dateTo){
                return true;
            }
        }

        return false;
    }

    //sprawdzenie czy zawiera rezerwacje i uaktualnia stan
    public function updateEquipmentToReservationList($tableEquipment,$idEquipment, $option){
        if(isset($tableEquipment[0])) {
            $counter = 0;
            foreach ($tableEquipment as $item) {

                $contain = null;
                if ($item['IdSprzet'] == $idEquipment && $item['flaga']!= false) {
                    $contain = true;
                    $tableEquipment[$counter]['IdSprzet'] = $idEquipment;
                    $tableEquipment[$counter]['flaga'] = $option;
                    $counter=0;
                    break;
                }else if($item['IdSprzet'] == $idEquipment){
                    $contain = true;
                    $counter=0;
                    break;
                }
                $counter+=1;
            }
            if($contain == null){
                array_push($tableEquipment, ['IdSprzet' => $idEquipment, 'flaga' => $option]);
            }
        }else{
            $tableEquipment[] = ['IdSprzet' => $idEquipment, 'flaga' => $option];
        }

        return $tableEquipment;
    }

    public function updateAvailableDates($tableDates, $dateFrom, $dateTo, $equipment){
        $counter =0;
        foreach ($tableDates[$equipment]['available'] as $item){
            if($item['DataOd'] == $dateFrom && $item['DataDo'] == $dateTo){
                $tableDates[$equipment]['available'][$counter]['Ilosc'] = $tableDates[$equipment]['available'][$counter]['Ilosc'] + 1;
            }
            $counter +=1;
        }

        return $tableDates[$equipment]['available'];
    }

    public function getAll(){

        $data = json_decode(file_get_contents('php://input'), true);
        $date = date('Y-m-d');
        $reservationModel = $this->getModel("Rezerwacja");
        if($data['period'] === 'today'){
            $reservationAll = $reservationModel->getAll($date,$date,$data['status']);
            $reservationAll['test'] = "today";
            echo json_encode($reservationAll);
        } else if ($data['period'] === 'tomorrow') {
            $tomorrow = date('Y-m-d', strtotime("+1 day", strtotime($date)));
            $reservationAll = $reservationModel->getAll($tomorrow,$tomorrow,$data['status']);
            echo json_encode($reservationAll);
        } else {
            $reservationAll = $reservationModel->getAll(null, null,$data['status']);
            echo json_encode($reservationAll);
        }


    }

    public function changeStatus(){
        $reservationData = json_decode(file_get_contents('php://input'), true);
        $modelRezerwacja = $this->getModel("Rezerwacja");

        $response = $modelRezerwacja->changeStatus($reservationData['idReservation'], $reservationData['idStatus']);
        if(!isset($response['error'])){
            $data['msg'] = 'Zaktualizowano status';
            echo json_encode($data);
        }else {
            $data['error'] = 'Nie udało się zaktualizować statusu';
            echo json_encode($data);
        }


}

    public function delete($id){

        $modelReservation = $this->getModel("Rezerwacja");
        $response = $modelReservation->delete($id);

        echo json_encode($response);
    }

    public function getOne($id ){
        $response['id'] = $id;

        $modelReservation = $this->getModel("Rezerwacja");
        $response = $modelReservation->getOne($id);
        $response['id'] = $id;
        echo json_encode($response);
    }

    public function getOneByIdReservation($id){
        $modelReservation = $this->getModel("Rezerwacja");
        $response = $modelReservation->getOneByIdReservation($id);
        echo json_encode($response);
    }

    public function getSkisOptions($id){
        $modelReservation = $this->getModel("Rezerwacja");
        $response = $modelReservation->getSkisOptions($id);

        echo json_encode($response);
    }

    public function getSnowboardOptions($id){
        $modelReservation = $this->getModel("Rezerwacja");
        $response = $modelReservation->getSnowboardOptions($id);

        echo json_encode($response);
    }

/*********************************************************************************************************************/
    public function clearList(){
        \Tools\Access::clearAll();
        $this->redirect('Rezerwacja');
    }

    public function reservationClient(){

        $viewReservation = $this->getView("Rezerwacja");
        $viewReservation->reservationClient();
    }

    public function add2(){

        //pobieram id wszystkich snowboardow zeby ewentualnie zaktualizowac ilosc sztuk wypozyczenia danego sprzetu
        $IdSnowboard = \Tools\Session::get("IdSnowboard");


        //pobieram dane snowboardow do wypozyczenia
        $IdSprzet = array();
        $Zarezerwowane = array();


        $modelSnowboard = $this->getModel("Snowboard");
        $modelReservation = $this->getModel("Rezerwacja");
        $modelKlient = $this->getModel("Klient");

        $i=0;

        foreach ($IdSnowboard as $item){
            if(isset($_POST[$item['Id']])) {


                $IdSnowboard[$i]["Ilosc"] = $_POST[$item['Id']];

                $snowboard = $modelSnowboard->getOne($item['Id']);
                $snowboardType = $modelSnowboard->getOneType($snowboard[0]);

                $flaga = true;
                for($k=0;$k<$IdSnowboard[$i]["Ilosc"];$k++){

                    foreach ($snowboardType as $snowboard) {


                        $data = $modelReservation->checkDate($snowboard['Id'], $_POST['dataOd'], $_POST['dataDo']);

                        if (isset($data['msg']) && $data['msg'] == true && $flaga == true) {
                            $IdSprzet[] = $snowboard['Id'];

                            $flaga=false;

                        }else if(isset($data['zajete'])){
                            $Zarezerwowane['zajete'][] = $data['zajete'];

                        }

                    }
                }
            }

            $i=$i+1;
        }

        //d($IdSprzet);

        \Tools\Session::set("IdSnowboard",$IdSnowboard);


        if( sizeof($IdSprzet) > 0) {
            $IdKlient = $modelKlient->check($_POST['imie'], $_POST['nazwisko'], $_POST['telefon'], $_POST['email']);

            //sprawdza czy klient jest juz w bazie
           if (isset($IdKlient["Klient"]))
                $IdKlient = $IdKlient["Klient"][0]["Id"];
            else
                $IdKlient = $modelKlient->add($_POST['imie'], $_POST['nazwisko'], $_POST['telefon'], $_POST['email']);


           $modelReservation->add($IdKlient, $_POST['dataOd'], $_POST['dataDo'], $IdSprzet);
            \Tools\Session::clearAll();

           $this->redirect("Snowboard");
        }else{
           $viewReservation = $this->getView("Rezerwacja");
           $viewReservation->reservationClient($Zarezerwowane);
        }


        //$viewSnowboard = $this->getView("Snowboard");
        //$viewSnowboard->getAll($data);
        //$this->redirect("Snowboard");

        }
}