<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
define( 'PUBWICH', 1 );

require( 'app/core/jsonWich.php' );

class Routing {
    private $routes = array();
    public function router(){
        if(isset($_GET['url'])) {
            $url = $_GET['url'];
            $url = rtrim($url,'/');
            $url = explode('/', $url);
            foreach($url as $key => $value) {
                $this->routes[$key] = $value;
            }
        } else {
            //echo $_SERVER['REQUEST_URI'];
            //echo $_GET['url'];
        }
        if(isset($this->routes['0'])) {
            if(array_search($this->routes['0'], jsonWich::listActiveServices())){
                return $this->routes['0'];
            } else {
                return 'facebook_page';
            }
        } else {
            return 'instagram';
        }
    }
}

jsonWich::init();
$routing = new Routing;
$service = $routing->router();
echo jsonWich::getFeedCache($service);

?>
