<?php
namespace Able\Bender\Interpreters;

use \Able\Bender\Abstractions\AInterpriter;

use \Able\Bender\Interpreters\Combine;
use \Able\Bender\Interpreters\Minimize;

use \Able\Bender\Utilities\Registry;

use \Able\IO\Path;
use \Able\IO\File;
use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \Able\Helpers\Src;
use \Able\Helpers\Arr;

use \Exception;

class Composer
	extends AInterpriter {

	/**
	 * @var File
	 */
	private File $Target;

	/**
	 * @param ReadingStream $Stream
	 * @param Directory $Point
	 * @param File $Target
	 *
	 * @throws Exception
	 */
	public final function __construct(ReadingStream $Stream, Directory $Point, File $Target) {
		parent::__construct($Stream, $Point);

		$this->Target = $Target;
		$this->Target->purge();
	}

	/**
	 * @throws Exception
	 */
	protected final function finalize(): void {
		$this->Target->toWriter()
			->consume($this->output());
	}
}
