<?php

namespace Chalet;

/**
 * @author  Ibrahim Abdullah
 * @package Chalet
 */
class DeveloperNotifier
{
	/**
	 * @var \Exception
	 */
	private $exception;

	/**
	 * @var string
	 */
	private $subject;

	/**
	 * @var sting
	 */
	private $email;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @param \Exception $exception
	 */
	public function __construct(\Exception $exception)
	{
		$this->exception = $exception;
	}

	/**
	 * @param string $subject
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @todo refactor this method to use Mailer instead of verstuur_opmaakmail by implementing service container
	 *
	 * @param string $website
	 *
	 * @return bool
	 */
	public function send($website)
	{
		$html      = 'Gebruiker <u>{{ username }}</u> kreeg op URL <a href="{{ url }}">{{ url }}</a> een probleem met het afbeeldingen systeem. <br /><br />';
		$html     .= '<strong>Error:</strong> <br />';
		$html     .= 'Bestand: {{ filename }} <br />';
		$html     .= 'Regelnummer: {{ line }} <br />';
		$html     .= 'Bericht: {{ message }} <br /><br />';
		$html     .= '<strong>Stacktrace</strong> <br /><br />';
		$html     .= '{{ stacktrace }}';
		$html      = str_replace(['{{ username }}', '{{ url }}', '{{ filename }}', '{{ line }}', '{{ message }}', '{{ stacktrace }}'],
		                         [$this->username, $this->url, $this->exception->getFile(), $this->exception->getLine(), $this->exception->getMessage(), nl2br($this->exception->getTraceAsString())],
		                         $html);

		return verstuur_opmaakmail($website, $this->email, 'Developers', $this->subject, $html, []);
	}
}