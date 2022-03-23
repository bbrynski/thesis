<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 18.11.2018
 * Time: 22:42
 */

namespace Controllers;


class ButyKategoria extends Controller
{
    public function getAll(){
        $model = $this->getModel('ButyKategoria');
        echo json_encode($model->getAll());
    }

}