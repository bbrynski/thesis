<?php
    namespace Controllers;

    class Producent extends Controller {

        public function getAll(){
            $model = $this->getModel('Producent');
            echo json_encode($model->getAll());
        }

        public function getOne($id){
            $model = $this->getModel('Producent');
            echo json_encode($model->getOne($id));
        }

        public function add()
        {
            $data = array();
            $dane = json_decode(file_get_contents('php://input'), true);

            $modelProducer = $this->getModel("Producent");

            //sprawdzenie czy juz istnieje taki snowboard
            //echo json_encode($dane);
            $isProducerExist = $modelProducer->getOneType($dane['nazwa']);

            if ($isProducerExist != null) {
                $data['error'] = "Producent już istnieje w bazie danych";
                echo json_encode($data);
            } else {
                $data = $modelProducer->add($dane['nazwa']);
                $data['msg'] = 'Producent został dodane';
                echo json_encode($data);

            }
        }

        public function edit(){
            $newProducer = json_decode(file_get_contents('php://input'), true);
            $modelProducer = $this->getModel("Producent");

            $resultProducer = $modelProducer->update($newProducer);

            $data['msg'] = "Poprawnie zaktualizowano producenta";

            echo json_encode($data);
        }

        public function delete($id){
            $data = array();
            //$data = json_decode(file_get_contents('php://input'), true);
            //$accessController = new \Controllers\Dostep();
            //$accessController->islogin();
            $modelProducer = $this->getModel("Producent");
            $producerAssignedToEquipment = $modelProducer->getAssignedToEquipment($id);
            //$skis = $modelSkis->getOne($id);
            //pobranie wszystkich id
            //$allTypeSnowboardId = $modelSkis->getOneType($skis[0]['Model'], $skis[0]['IdPrzeznaczenie'], $skis[0]['Dlugosc'], $skis[0]['IdProducent'], $skis[0]['IdPlec']);
            //if (is_array($allTypeSnowboardId) || is_object($allTypeSnowboardId)) {
             //   foreach ($allTypeSnowboardId as $idSkis) {
             //       $modelSkis->delete($idSkis['IdNarty']);
             //   }
           // }
            if($producerAssignedToEquipment != null){
                $data['error'] = "Nie można usunąc ponieważ producent jest przypisany do sprzętu";
                echo json_encode($data);
            }else{
                $data = $modelProducer->delete($id);
                $data['msg'] = 'Producent został usunięty';
                echo json_encode($data);
            }
        }
    }