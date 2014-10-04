<?php

/**
 * Super Light Framework
 * For PHP + MySQL (PDO)
 * @author Michael Gordo /October 2014/
 */
class Plywood
{
	public static $config;

	public function __construct()
	{
		define ('ROOT_SRC', dirname(__FILE__) . '/');
		define ('ROOT_DIR', dirname(__FILE__) . '/../');
		define ('URL_BASE', '/testing/');
		$this->autoload();
		self::$config = Config::get();
	}

	public function init()
	{
		$path = trim( str_replace( URL_BASE, '', $_SERVER['REQUEST_URI'] ), '/' );
		$uri  = '/' . $path;

		/**
		 * processing $_GET[]
		 */
		$_get_ = array();
		if (strpos($uri, '?')) {
			$_get_part = substr($uri, strpos($uri, '?') + 1);
			$uri = substr($uri, 0, strpos($uri, '?'));
			$_t_get_ = explode('&',$_get_part);
			foreach ($_t_get_ as $gg)
			if (strpos($gg, '=')) {
				$_ff = explode('=', $gg);
				$_get_[$_ff[0]] = $_ff[1];
			} else array_push ($_get_, $gg);
		}

		foreach (self::$config['routing'] as $action => $rule) {
			$pattern = '/^'.str_replace( '/', '\/', $rule["pattern"] ).'$/';
			if (preg_match($pattern, $uri, $params)) {
				$controllerName = $rule["controller"];
				/**
				 * Ajax Controller - special case
				 */
				if ($controllerName == 'Ajax') {
					include_once ROOT_DIR . 'Controller/AjaxController.php';
					$controller = new AjaxController();
					if (isset($params['function_name']) && $params['function_name'])
						$function_name = $params['function_name']."Action";
					else $function_name = "indexAction";
				} else {
					/**
					 *	Initialize the controller
					 */
					$controllerName = $controllerName . "Controller";
					include_once ROOT_DIR . 'Controller/' . $controllerName . '.php';
					$controller = new $controllerName;
					$function_name = $action . "Action";
				}

				/**
				 *  Method inexistent
				 */
				if (!method_exists($controller, $function_name)) {
					$function_name = 'indexAction';
				}

				$params['get'] = $_get_;

				/**
				 * 	Execute proper function
				 */
				$result = $controller->$function_name($params);
				if ($controllerName == 'Ajax') {
					exit();
				}

				/**
				 * Make sure papameters are set
				 */
				if (!isset($result['params']))
					$result['params'] = [];

				/**
				 *	Action should return the layout name. Otherwise use 'default'
				 */
				$layout_name = isset($result['layout']) ? $result['layout'] : 'default';

				$content = $result;

				unset($result);
				unset($controller);

				/**
				 * 	Include the layout
				*/
				ob_start();
				include(ROOT_DIR . 'View/' . $rule["controller"] . '/' . $layout_name . '.php');
				$output = ob_get_clean();
				//$output = preg_replace("/[\ \t\n]+/", " ", $output); // ZIPPING
				echo $output;
				exit();
			}
		}

		/* nothing is found so handle 404 error */
		include( ROOT_DIR . 'View/' . '404.php' );
	}

	/**
	 * Load Base Classes
	 */
	private function autoload()
	{
		$files = [
		ROOT_SRC . 'Config.php',
		ROOT_SRC . 'LoggerTrait.php',
		ROOT_SRC . 'BaseController.php',
		ROOT_SRC . 'DbTrait.php',
		ROOT_DIR . 'Entity/Entity.php',
		ROOT_DIR . 'Manager/Manager.php',
		ROOT_DIR . 'Service/Service.php',
		ROOT_DIR . 'Repository/Repository.php',
		];

		foreach ($files as $file) {
			if (file_exists($file)) {
				include_once $file;
			} else {
				throw new Exception('File ' . $file . ' not found.');
			}
		}
	}

}