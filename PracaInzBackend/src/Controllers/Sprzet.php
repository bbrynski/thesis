<?php

namespace Controllers;

use \Exception;

class Sprzet extends Controller
{

    //dodanie snowboardu do bazy
    public function add(){
        $data = array();
        $dane = json_decode(file_get_contents('php://input'),true);

        /* zawarte instrukcje pokazują działanie -> działa prawidłowo
        $arr = array ('a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5);
        $zmienna = json_decode(file_get_contents('php://input'),true);
        $arr2 = array ('a'=>(int)$zmienna["przeznaczenie"],'b'=>2,'c'=>3,'d'=>4,'e'=>5);
        echo json_encode($arr2);
        */


        /*
        try {
                throw new Exception('Test error', 123);

        } catch (Exception $e) {
            echo json_encode(array(
                'error' => array(
                    'msg' => $e->getMessage(),
                    'code' => $e->getCode(),
                ),
            ));
        }
        */
        //echo file_get_contents('php://input');
        //d(json_decode(file_get_contents('php://input')));
        //return file_get_contents('php://input');

        $modelSnowboard = $this->getModel("Snowboard");
        $modelSprzet = $this->getModel("Sprzet");

        //sprawdzenie czy juz istnieje taki snowboard
        $isSnowboardExist = $modelSprzet->isSnowboardExist($dane['model'], (int)$dane['przeznaczenie'], (float)$dane['dlugosc'], (int)$dane['producent'], (int)$dane['plec']);
        if ($isSnowboardExist){
            $data['msg'] = "Sprzęt już istnieje w bazie danych";
            echo json_encode($data);
        }else {

            $idSnowboard = $modelSnowboard->add($dane['model'], (int)$dane['przeznaczenie'], (float)$dane['dlugosc'], (int)$dane['ilosc']);
            //idSnowboard -> tabela z indeksami jesli ilosc wiecej niz 1
            //d($idSnowboard);


            $data = $modelSprzet->add($idSnowboard, (int)$dane['producent'], (int)$dane['plec'], (float)$dane['cena']);

            $arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
            $arr2 = array('a' => (int)$dane["przeznaczenie"], 'idSnowboard' => $idSnowboard, 'c' => 3, 'd' => 4, 'e' => 5);
            echo json_encode($data);
        }
        }


        public function changeToReturn(){
            $idEquipment = json_decode(file_get_contents('php://input'),true);
            $modelEquipment = $this->getModel("Sprzet");
            $response = $modelEquipment->changeToReturn($idEquipment);

            echo json_encode($response);

        }

        public function getAllToService() {
            $modelEquipment = $this->getModel("Sprzet");
            $response = $modelEquipment->getAllToService();
            echo json_encode($response);
        }

        public function getStatistics(){
            $response = array();


            $data = json_decode(file_get_contents('php://input'),true);
            $modelEquipment = $this->getModel("Sprzet");

            $response['equipmentType'] = $data['equipmentType'];

            if ($data['equipmentType'] == 'all'){
                $response['snowboard'] = 0;
                $response['skis'] = 0;
                $response['skiPoles'] = 0;
                $response['boots'] = 0;

                $responseSnowboard = $modelEquipment->getStatisticsSnowboard(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d',strtotime($data['dateTo'])));
                if ($responseSnowboard['snowboard'] != null){
                    $response['snowboard'] = sizeof($responseSnowboard['snowboard']);
                }
                $responseSkis = $modelEquipment->getStatisticsSkis(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d',strtotime($data['dateTo'])));
                if ($responseSkis['skis'] != null){
                    $response['skis'] = sizeof($responseSkis['skis']);
                }
                $responseSkiPoles = $modelEquipment->getStatisticsSkiPoles(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d',strtotime($data['dateTo'])));
                if ($responseSkiPoles['skiPoles'] != null){
                    $response['skiPoles'] = sizeof($responseSkiPoles['skiPoles']);
                }
                $responseBoots = $modelEquipment->getStatisticsBoots(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d',strtotime($data['dateTo'])));
                if ($responseBoots['boots'] != null){
                    $response['boots'] = sizeof($responseBoots['boots']);
                }
                //jesli snowboard
            } else if ($data['equipmentType'] == 'snowboard') {
                $response['snowboard'] = 0;
                $response['options'] = $data['options'];
                $response['gender'] = $data['gender'];
                $responseSnowboard = $modelEquipment->getStatisticsSnowboard(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d', strtotime($data['dateTo'])), $data['options'],$data['gender']);
                if ($responseSnowboard['snowboard'] != null && $data['options'] == 'model') {
                    $response['model'] = $responseSnowboard['snowboard'];
                }
                if ($responseSnowboard['snowboard'] != null && $data['options'] == 'length') {
                    $response['dlugosc'] = $responseSnowboard['snowboard'];
                }
                if ($responseSnowboard['snowboard'] != null && $data['options'] == 'gender') {
                    $response['damska'] =0;
                    $response['meska'] =0;
                    foreach ($responseSnowboard['snowboard'] as $item){
                        if ($item['NazwaPlec'] == 'Damska'){
                            $response['damska'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPlec'] == 'Męska'){
                            $response['meska'] = $item['Ilosc'];
                        }
                    }
                }
                if ($responseSnowboard['snowboard'] != null && $data['options'] == 'type') {
                    $response['all-mountain'] =0;
                    $response['all-round'] =0;
                    $response['freeride'] =0;
                    $response['freestyle'] =0;
                    foreach ($responseSnowboard['snowboard'] as $item){
                        if ($item['NazwaPrzeznaczenie'] == 'All-mountain'){
                            $response['all-mountain'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPrzeznaczenie'] == 'All-round'){
                            $response['all-round'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPrzeznaczenie'] == 'Freeride'){
                            $response['freeride'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPrzeznaczenie'] == 'Freestyle'){
                            $response['freestyle'] = $item['Ilosc'];
                        }
                    }
                }

                //jesli narty
            }else if ($data['equipmentType'] == 'skis') {
                $response['skis'] = 0;
                $response['options'] = $data['options'];
                $response['gender'] = $data['gender'];
                $responseSkis = $modelEquipment->getStatisticsSkis(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d', strtotime($data['dateTo'])), $data['options'], $data['gender']);
                if ($responseSkis['skis'] != null && $data['options'] == 'model') {
                    $response['model'] = $responseSkis['skis'];
                }

                if ($responseSkis['skis'] != null && $data['options'] == 'length') {
                    $response['dlugosc'] = $responseSkis['skis'];
                }
                if ($responseSkis['skis'] != null && $data['options'] == 'gender') {
                    $response['damska'] =0;
                    $response['meska'] =0;
                    foreach ($responseSkis['skis'] as $item){
                        if ($item['NazwaPlec'] == 'Damska'){
                            $response['damska'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPlec'] == 'Męska'){
                            $response['meska'] = $item['Ilosc'];
                        }
                    }
                }
                if ($responseSkis['skis'] != null && $data['options'] == 'type') {
                    $response['all-mountain'] =0;
                    $response['all-round'] =0;
                    $response['freeride'] =0;
                    $response['freestyle'] =0;
                    foreach ($responseSkis['skis'] as $item){
                        if ($item['NazwaPrzeznaczenie'] == 'All-mountain'){
                            $response['all-mountain'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPrzeznaczenie'] == 'All-round'){
                            $response['all-round'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPrzeznaczenie'] == 'Freeride'){
                            $response['freeride'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPrzeznaczenie'] == 'Freestyle'){
                            $response['freestyle'] = $item['Ilosc'];
                        }
                    }
                }

                //jesli kijki
            }else if ($data['equipmentType'] == 'skiPoles') {
                $response['skiPoles'] = 0;
                $response['options'] = $data['options'];
                $response['gender'] = $data['gender'];
                $responseSkiPoles = $modelEquipment->getStatisticsSkiPoles(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d', strtotime($data['dateTo'])), $data['options'], $data['gender']);
                if ($responseSkiPoles['skiPoles'] != null && $data['options'] == 'model') {
                    $response['model'] = $responseSkiPoles['skiPoles'];
                }

                if ($responseSkiPoles['skiPoles'] != null && $data['options'] == 'length') {
                    $response['dlugosc'] = $responseSkiPoles['skiPoles'];
                }
                if ($responseSkiPoles['skiPoles'] != null && $data['options'] == 'gender') {
                    $response['damska'] =0;
                    $response['meska'] =0;
                    foreach ($responseSkiPoles['skiPoles'] as $item){
                        if ($item['NazwaPlec'] == 'Damska'){
                            $response['damska'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPlec'] == 'Męska'){
                            $response['meska'] = $item['Ilosc'];
                        }
                    }
                }
                //jesli buty
            }else if ($data['equipmentType'] == 'boots') {
                $response['boots'] = 0;
                $response['options'] = $data['options'];
                $response['gender'] = $data['gender'];
                $responseBoots = $modelEquipment->getStatisticsBoots(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d', strtotime($data['dateTo'])), $data['options'], $data['gender']);
                if ($responseBoots['boots'] != null && $data['options'] == 'model') {
                    $response['model'] = $responseBoots['boots'];
                }

                if ($responseBoots['boots'] != null && $data['options'] == 'size') {
                    $response['rozmiar'] = $responseBoots['boots'];
                }
                if ($responseBoots['boots'] != null && $data['options'] == 'gender') {
                    $response['damska'] =0;
                    $response['meska'] =0;
                    foreach ($responseBoots['boots'] as $item){
                        if ($item['NazwaPlec'] == 'Damska'){
                            $response['damska'] = $item['Ilosc'];
                        }
                        if ($item['NazwaPlec'] == 'Męska'){
                            $response['meska'] = $item['Ilosc'];
                        }
                    }
                }
            } else if ($data['equipmentType'] == 'producer'){
                $response['equipmentType'] = $data['equipmentType'];
                $responseEquipment = $modelEquipment->getStatisticsProducer(date('Y-m-d', strtotime($data['dateFrom'])), date('Y-m-d',strtotime($data['dateTo'])));
                $response['test'] = $responseEquipment;
                if ($responseEquipment['equipment'] != null) {
                    $response['producent'] = $responseEquipment['equipment'];

                }
            }
            echo json_encode($response);
        }

}