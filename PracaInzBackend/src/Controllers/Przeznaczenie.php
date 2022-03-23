<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 19.10.2018
 * Time: 15:55
 */

namespace Controllers;


class Przeznaczenie extends Controller
{
    public function getAll(){
        $model = $this->getModel('Przeznaczenie');
        echo json_encode($model->getAll());
    }

}