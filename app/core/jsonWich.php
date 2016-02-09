<?php
    defined('PUBWICH') or die('No direct access allowed.');

    /**
     * @classname jsonWich
     */
    
    require( 'Pubwich.php' );

    class jsonWich extends Pubwich {
        /**
         * init is a copy of the existing Pubwich init function but doesn't output html.
         * @return [type] [description]
         */
        static public function init() {
                        // Let’s modify the `include_path`
                $path_app = realpath(dirname(__FILE__).'/../');
                $path_core = $path_app . '/core/';
                // $path_services = $path_core.'../services/';
                $path_libs = $path_app . '/vendor/';
                $path_pear_core = $path_libs . '/pear-core/src/';
                $path_pear_cache_lite = $path_libs . '/pear-cache-lite/';
                $path_user = $path_app . '/../usr/';
                set_include_path(
                    realpath($path_user) . PATH_SEPARATOR
                    . realpath($path_core) . PATH_SEPARATOR
                    . realpath($path_app) . PATH_SEPARATOR
                    . realpath($path_libs) . PATH_SEPARATOR
                    . realpath($path_pear_core) . PATH_SEPARATOR
                    . realpath($path_pear_cache_lite) . PATH_SEPARATOR
                    . realpath($path_user) . PATH_SEPARATOR
                    . get_include_path()
                );

                require_once( 'PEAR.php' );

                // Exception class
                require_once( 'core/PubwichError.php' );

                // Configuration files
                if ( !file_exists( $path_user . 'configuration/config.php' ) ) {
                    throw new PubwichError( 'You must rename <code>usr/configuration/config.sample.php</code> to <code>usr/configuration/config.php</code> and edit the Web service configuration details.' );
                } else {
                    require_once( 'configuration/config.php' );
                }

                // Application URL
                if (!defined('PUBWICH_URL'))
                {
                    // protocol
                    $prot = 'http://';
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off')
                    {
                        $prot = 'https://';
                    }
                    
                    // server name
                    $server = trim($_SERVER['SERVER_NAME'], '/');
                    
                    // server path
                    $path = trim(str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(__DIR__ . '/../../')), '/');
                    //$path = trim(str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', $_SERVER['SCRIPT_NAME']), '/');
                    
                    define('PUBWICH_URL', $prot . $server . '/' . $path . '/');
                }

                // Internationalization class
                if ( defined('PUBWICH_LANG') && PUBWICH_LANG != '' ) {
                    require_once( 'php-gettext/streams.php' );
                    require_once( 'php-gettext/gettext.php' );
                    self::$gettext = @new gettext_reader( new FileReader( dirname(__FILE__).'/../lang/'.PUBWICH_LANG.'/pubwich-'.PUBWICH_LANG.'.mo' ) );
                }

                // Events logger (and first message)
                require_once('core/PubwichLog.php');
                PubwichLog::init();
                PubwichLog::log( 1, Pubwich::_("Pubwich object initialization") );

                // Caching
                if (!defined('CACHE_LOCATION')) {
                    define('CACHE_LOCATION', $path_user . 'cache/');
                }
                
                require_once( 'Cache/Lite.php' );
                
                // Other classes
                require_once( 'FileFetcher.php' );
                

                if ( !defined( 'PUBWICH_CRON' ) ) {
                    require_once( 'mustache.php/src/Mustache/Autoloader.php' );
                    Mustache_Autoloader::register();
                }

                // JSON support
                if ( !function_exists( 'json_decode' ) ) {
                    throw new PubwichError('PHP version with json_decode support is required: http://php.net/manual/en/json.installation.php');
                }
                require_once( 'PubwichTemplate.php' );
                // PHP objects creation
                self::setClasses();
            }

            static public function getFeedCache($service_name){
                $service_object = self::getActiveService($service_name);
                $output_cache = self::getOutputCacheObject();
                return $output_cache->get($service_object->cache_id);
            }
    }