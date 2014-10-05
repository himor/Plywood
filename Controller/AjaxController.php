<?php

/**
 * Class AjaxController - process ajax requests
 */
class AjaxController extends Controller {

	protected static $log = 'ajax.log';

	public function returnJson($json) {
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');
		echo json_encode($json);

		return;
	}

	public function indexAction($params) {
		return $this->returnJson([
			'status' => true
		]);
	}

}