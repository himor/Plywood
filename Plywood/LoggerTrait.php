<?php

trait LoggerTrait
{
	protected static $log = 'log.log';

	/**
	 * Write Log message to a file
	 *
	 * @param string | array $message
	 * @param string $filename
	 */
	public function Log($message, $filename = null)
	{
		if (!$filename) {
			$filename = static::$log;
		}

		if (is_array($message) || is_object($message)) {
			$message = print_r($message, true);
		}

		$filename = ROOT_DIR . 'Log/' . $filename;
		$now = new DateTime();
		$message  = $now->format('Y-m-d H:i:s') . ': ' . $message . "\n";

		if (file_exists($filename)) {
			file_put_contents($filename, $message, FILE_APPEND);
		} else {
			file_put_contents($filename, $message);
		}
	}

}