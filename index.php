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

class wpWich extends jsonWich {
    public function getFeedItem($service_name) {
        $service_object = self::getActiveService($service_name);
        $service_object->init();
        $data_array = $service_object->getProcessedData();
        //return get_class_methods($service_object);
        $data = $service_object->processDataItem($data_array[0]);
        return $data;
    }

    public function getFeeditems($service_name) {
        $service_object = self::getActiveService($service_name);
        $service_object->init();
        $data_array = $service_object->getProcessedData();
        $processed_array = array();
        foreach ($data_array as $data_item){
            $processed_array[] = $service_object->processDataItem($data_item);
        }
        return $processed_array;
    }
}


jsonWich::init();
$routing = new Routing;
$service = $routing->router();
$wpWich = new wpWich();
//echo jsonWich::getFeedCache('twitter_feed');

echo '<pre>';
print_r(jsonWich::listActiveServices());
echo '</pre>';

?>
