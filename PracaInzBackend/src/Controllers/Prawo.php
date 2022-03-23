<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 22.11.2018
 * Time: 20:51
 */

namespace Controllers;


class Prawo extends Controller
{
    public function getAll(){
        $model = $this->getModel('Prawo');
        echo json_encode($model->getAll());
    }

}