<?php
namespace Able\Bender;

use \Able\IO\File;
use \Able\IO\Reader;
use \Able\IO\ReadingStream;

use \Able\Helpers\Arr;

use \Able\Reglib\Regex;

use \Able\Bender\Interpreters\Register;
use \Able\Bender\Structures\SIndent;

use \Exception;
use \Generator;

class Combinator {

	/**
	 * @var ReadingStream
	 */
	private ReadingStream $Stream;

	/**
	 * @param File $Manifest
	 * @throws Exception
	 */
	public function __construct(File $Manifest) {
		$this->Stream = $Manifest->toReadingStream();
	}

	/**
	 * @throws Exception
	 */
	public function execute() {
		while (!is_null($line = $this->Stream->read())) {

			if (preg_match('/^\s+/', $line)) {
				throw new \Exception(sprintf('Invalid offset in line %d!', $this->Stream->getIndex()));
			}

			$this->intepretate(trim($line));
		}
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	protected function intepretate(string $line): void {
		echo sprintf("==>%s\n", $line);

		switch ($line) {
			case 'register';
				(new Register($this->Stream))->execute();
				break;

			default:
				if (preg_match('/^=[A-Za-z0-9_]+\.[A-Za-z0-9]{3,}/', $line)) {
					_dumpe($line);
				}
		}
	}
}
