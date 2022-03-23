<?php
    namespace Controllers;

    class Plec extends Controller {

        public function getAll(){
            $model = $this->getModel('Plec');
            echo json_encode($model->getAll());
        }
    }