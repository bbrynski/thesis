<?php
    namespace Controllers;

    class SnowboardPrzeznaczenie extends Controller {

        public function getAll(){
            $model = $this->getModel('SnowboardPrzeznaczenie');
            echo json_encode($model->getAll());
        }
    }