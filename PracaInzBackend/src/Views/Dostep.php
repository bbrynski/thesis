<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 05.06.2018
 * Time: 18:45
 */

namespace Views;


class Dostep extends View
{
    public function login($data) {
        echo json_encode($data);
    }

    public function logform($data = null){
        if(isset($data['message']))
            $this->set('message',$data['message']);
        if(isset($data['error']))
            $this->set('error',$data['error']);
        $this->render('LogForm');
    }
}