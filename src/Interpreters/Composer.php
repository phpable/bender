<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use \Able\Bender\Interpreters\Combine;
use \Able\Bender\Interpreters\Minimize;

use Able\Bender\Utilities\Registry;
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
	 * @param Registry $Registry
	 * @param File $File
	 *
	 * @throws Exception
	 */
	public final function __construct(ReadingStream $Stream, Registry $Registry, File $File) {
		parent::__construct($Stream, $Registry);

		$this->File = $File;
		$this->File->purge();
	}

	/**
	 * @throws Exception
	 */
	protected final function finalize(): void {
		$this->File->toWriter()
			->write($this->output()->toReadingBuffer()->read());
	}
}
