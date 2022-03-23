<?php

namespace Views;


class Snowboard extends View
{
    public function getAll($data = null)
    {

        $modelSnowboard = $this->getModel("Snowboard");
        $dataSnowboard = $modelSnowboard->getAll();

        if (isset ($data['message']))
            $this->set("message", $data['message']);
        else if (isset ($data['error']))
            $this->set("error", $data['error']);

        $this->set("snowboard", $dataSnowboard);
        $this->render("SnowboardAll");
    }

    public function getAllSnowboard($data)
    {
        d($data);
        $modelSnowboard = $this->getModel("Snowboard");
        $dataSnowboard = $modelSnowboard->getAllSnowboard();
        $this->set("snowboard", $dataSnowboard);
        $this->render("SnowboardGetAllSnowboard");
    }

    public function addform()
    {
        $modelPrzeznaczenie = $this->getModel("SnowboardPrzeznaczenie");
        $przeznaczenie = $modelPrzeznaczenie->getAllForSelect();

        $modelProducent = $this->getModel("Producent");
        $producent = $modelProducent->getAllForSelect();

        $modelPlec = $this->getModel("Plec");
        $plec = $modelPlec->getAllForSelect();


        $this->set("przeznaczenie", $przeznaczenie);
        $this->set("producent", $producent);
        $this->set("plec", $plec);
        $this->render('SnowboardAddForm');
    }
}