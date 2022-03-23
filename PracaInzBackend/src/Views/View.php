<?php
namespace Views;
use \Smarty;
abstract class View {
    protected $smarty;
    public function  __construct() {
        $this->smarty = new Smarty();
        $this->set('subdir', '/'.\Config\Website\Config::$subdir);
        if(\Tools\Access::islogin() === true) {
            $this->set('login', true);
        }

        }

    //załadowanie modelu
    public function getModel($name){
        $name = 'Models\\'.$name;
        return new $name();
    }

    //za�adowanie i zrenderowanie szablonu
    public function render($name) {
        $path='templates'.DIRECTORY_SEPARATOR;
        $path.=$name.'.html.php';
        try {
            if(is_file($path)) {
                $this->smarty->display($path);
            } else {
                throw new \Exception('Nie można załączyć szablonu '.$name.' z: '.$path);
            }
        }
        catch(\Exception $e) {
            echo $e->getMessage().'<br />
					Plik: '.$e->getFile().'<br />
					Linia: '.$e->getLine().'<br />
					�lad: '.$e->getTraceAsString();
            exit;
        }
    }
    public function set($name, $value) {
        $this->smarty->assign($name, $value);
    }

}