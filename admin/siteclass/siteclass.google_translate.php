<?php

/**
 * Google Translate PHP class
 */
class google_translate {

	private $api_key;
	protected $response;
	protected $error;

	public function __construct() {
		global $vars;
		$this->api_key = $vars["google_translation_api_key"];
	}

	public function translate_text($from_language, $to_language, $text) {
		$url = 'https://www.googleapis.com/language/translate/v2?key='
			. $this->api_key
			. '&q=' . rawurlencode($text)
			. '&source=' . $from_language
			. '&target=' . $to_language;

		$this->call_api($url);

		if(!isset($this->response["data"]["translations"][0]["translatedText"])) {
			$this->log_error('Could not translate text.'
				. ' from: ' . $from_language
				. ' to: ' . $to_language
				. ' text: ' . $text
			);
			return false;
		}
		return $this->response["data"]["translations"][0]["translatedText"];
	}

	public function detect_language($text) {
		$url = 'https://www.googleapis.com/language/translate/v2/detect?key='
			. $this->api_key
			. '&q=' . rawurlencode($text);
		$this->call_api($url);

		if(!isset($this->response["data"]["detections"][0][0]["language"])) {
			$this->log_error('Could not detect language.'
				. ' Text: ' . $text
			);
			return false;
		}
		return $this->response["data"]["detections"][0][0]["language"];
	}

	public function get_last_error()
	{
		return $this->error;
	}
	
	protected function call_api($url) {
		$try = 1;
		while ($try <= 3)
		{
			if ($try !== 1)
			{
				echo "is sleeping on: " . date('m/d/Y h:i:s a', time());
				$this->log_error("Sleeping: " . url);
				sleep(60);
			}
			$handle = fopen($url, 'r');
			$this->response = json_decode(stream_get_contents($handle), true);
			fclose($handle);
			if (is_array($this->response) && count($this->response))
			{
				return;
			}
			$try++;
		}
	}

	protected function check_response_for_errors()
	{
		if (isset($this->response["error"]))
		{
			$this->error = 'The Google API has returned an error: ' . var_export($this->response, true);
			return false;
		}
		return true;
	}

	protected function log_error($error_prefix = '')
	{
		$this->error = $error_prefix
			.'Could not read the retrieved response, it does not have the expected structure: '
			.var_export($this->response, true);
	}
}