<?php

namespace Plywood\Plywood;

class Core
{
    public static $config;

    public function __construct()
    {
        self::$config = Config::get();
    }

    public function init()
    {
        $requestUri = '';
        if (!empty($_ENV['REQUEST_URI'])) {
            $requestUri = $_ENV['REQUEST_URI'];
        }
        if (!empty($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
        }

        $path = trim(str_replace(URL_BASE, '', $requestUri), '/');
        $uri  = '/' . $path;

        /**
         * processing $_GET[]
         */
        $_get_ = array();
        if (strpos($uri, '?')) {
            $_get_part = substr($uri, strpos($uri, '?') + 1);
            $uri       = substr($uri, 0, strpos($uri, '?'));
            $_t_get_   = explode('&', $_get_part);
            foreach ($_t_get_ as $gg)
                if (strpos($gg, '=')) {
                    $_ff            = explode('=', $gg);
                    $_get_[$_ff[0]] = $_ff[1];
                } else array_push($_get_, $gg);
        }

        foreach (self::$config['routing'] as $action => $rule) {
            $pattern = '/^' . str_replace('/', '\/', $rule["pattern"]) . '$/';
            if (preg_match($pattern, $uri, $params)) {
                $controllerName = $rule["controller"];
                /**
                 * Ajax Controller - special case
                 */
                if ($controllerName == 'Ajax') {
                    $controller = new \Plywood\Controller\AjaxController();
                    if (isset($params['function_name']) && $params['function_name'])
                        $function_name = $params['function_name'] . "Action";
                    else $function_name = "indexAction";
                } else {
                    /**
                     *    Initialize the controller
                     */
                    $controllerName = "\\Plywood\\Controller\\" .  $controllerName . "Controller";
                    $controller     = new $controllerName;
                    $function_name  = $action . "Action";
                }

                /**
                 *  Method inexistent
                 */
                if (!method_exists($controller, $function_name)) {
                    $function_name = 'indexAction';
                }

                $params['get'] = $_get_;

                /**
                 *    Execute proper function
                 */
                $result = $controller->$function_name($params);
                if ($controllerName == 'Ajax') {
                    exit();
                }

                /**
                 * Make sure parameters are set
                 */
                if (!isset($result['params']))
                    $result['params'] = [];

                /**
                 *    Action should return the layout name. Otherwise use 'default'
                 */
                $layout_name = isset($result['layout']) ? $result['layout'] : 'default';

                $content = $result;

                unset($result);
                unset($controller);

                /**
                 *    Include the layout
                 */
                ob_start();
                include(ROOT_SRC . 'View/' . $rule["controller"] . '/' . $layout_name . '.php');
                $output = ob_get_clean();
                //$output = preg_replace("/[\ \t\n]+/", " ", $output); // ZIPPING
                echo $output;
                exit();
            }
        }

        /* nothing is found so handle 404 error */
        include(ROOT_SRC . 'View/' . '404.php');
    }
}