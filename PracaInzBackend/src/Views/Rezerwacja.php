<?php
/**
 * Created by PhpStorm.
 * User: bartek
 * Date: 03.06.2018
 * Time: 21:07
 */

namespace Views;


class Rezerwacja extends View
{
    public function reservationClient($data = null){

        $modelRezerwacja = $this->getModel("Rezerwacja");
        $rezerwacje=$modelRezerwacja->getAll();


        $modelSnowboard = $this->getModel("Snowboard");
        $snowboardy = $modelSnowboard-> getAllSnowboard();


        if(isset ($data['zajete']))
            $this->set('zajete',$data['zajete']);


        $this->set('snowboardy',$snowboardy);
        $this->set('rezerwacje',$rezerwacje['reservations']);





        $snowboard = array();
        if(\Tools\Session::is('IdSnowboard')){


            $IdSnowboard = \Tools\Session::get('IdSnowboard');
            foreach ($IdSnowboard as $item){
                $snowboard[] = $modelSnowboard->getOne($item['Id']);
            }
        }

        //foreach ($snowboard as $item){
       //     d($item[0]);
       // }
        //d(\Tools\Access::get('IdSnowboard'));
        $this->set('snowboard',$snowboard);
        $this->render("ReservationClient");

    }

}