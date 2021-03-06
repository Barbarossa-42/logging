<?php declare(strict_types = 1);

namespace Contributte\Logging\Mailer;

use Tracy\Helpers;
use Tracy\Logger;

/**
 * @author Milan Felix Sulc <sulcmil@gmail.com>
 */
class FileMailer implements IMailer
{

	/** @var string */
	private $directory;

	public function __construct(string $directory)
	{
		$this->directory = $directory;
	}

	/**
	 * @param mixed $message
	 */
	public function send($message): void
	{
		/** @var string $host */
		$host = preg_replace('#[^\w.-]+#', '', $_SERVER['HTTP_HOST'] ?? php_uname('n'));
		$parts = str_replace(
			["\r\n", "\n"],
			["\n", PHP_EOL],
			[
				'headers' => implode("\n", [
						'From: file@mailer',
						'X-Mailer: Tracy',
						'Content-Type: text/plain; charset=UTF-8',
						'Content-Transfer-Encoding: 8bit',
					]) . "\n",
				'subject' => 'PHP: An error occurred on the server ' . $host,
				'body' => Logger::formatMessage($message) . "\n\nsource: " . Helpers::getSource(),
			]
		);

		@file_put_contents($this->directory . '/tracy-mail-' . time() . '.txt', implode("\n\n", $parts));
	}

}
