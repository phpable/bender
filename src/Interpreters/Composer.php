<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use \Able\Bender\Interpreters\Combine;
use \Able\Bender\Interpreters\Minimize;

use \Able\IO\Path;
use \Able\IO\File;
use \Able\IO\ReadingStream;

use \Able\Helpers\Src;
use \Able\Helpers\Arr;

use \Exception;

class Composer
	extends AInterpriter {

	/**
	 * @var File
	 */
	private File $File;

	/**
	 * @param ReadingStream $Stream
	 * @param File $File
	 *
	 * @throws Exception
	 */
	public final function __construct(ReadingStream $Stream, File $File) {
		parent::__construct($Stream);

		$this->File = $File;
		$this->File->purge();
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	protected final function interpretate(string $line): void {
		throw new \Exception(sprintf('Invalid instruction: %s!', $line));
	}

	/**
	 * @throws Exception
	 */
	protected final function finalize(): void {
		$this->File->toWriter()
			->write($this->output()->toReadingBuffer()->read());
	}
}
