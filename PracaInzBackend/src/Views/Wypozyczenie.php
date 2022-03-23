<?php

namespace Views;

class Wypozyczenie extends View
{

    public function add($data){
        echo json_encode($data);
    }
    
    public function getAll($data=null){

        $modelWypozyczenie = $this->getModel("Wypozyczenie");
        $dataWypozyczenie = $modelWypozyczenie->getAll();
        $this->render("Wypozyczenia");
    }

    public function addform(){

        $modelKlient = $this->getModel("Klient");
        $klient = $modelKlient->getAllForSelect();

        $modelSnowboard = $this->getModel("Snowboard");
        $snowboard = $modelSnowboard->getAll();


        $this->set("snowboard", $snowboard);
        $this->set("klient", $klient);
        $this->render("WypozyczenieAddForm");
    }



}