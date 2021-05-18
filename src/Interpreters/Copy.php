<?php
namespace Able\Bender\Interpreters;

use \Able\IO\Path;
use \Able\IO\Directory;

use \Able\IO\ReadingStream;
use \Able\Bender\Abstractions\AExecutable;
use \Able\Bender\Abstractions\TInterpretatable;

use \Generator;
use \Exception;

class Copy extends AExecutable {
	use TInterpretatable;

	private Directory $Destination;

	/**
	 * @param ReadingStream $Stream
	 * @param Directory $Prefix
	 * @param Directory $Destination
	 *
	 * @throws Exception
	 */
	public final function __construct(ReadingStream $Stream, Directory $Prefix, Directory $Destination) {
		$this->Prefix = $Prefix;
		$this->Destination = $Destination;
		parent::__construct($Stream);
	}

	/**
	 * @var array
	 */
	private array $Targets = [];

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	protected final function parseInterpretatable(string $line): bool {
		echo sprintf("copy %s => %s\n", $line, $this->Destination);

		$this->Prefix->toPath()->append($line)->try(function (Path $Path){
			throw new Exception(sprintf('Target path is not exists or not a directory: %s!', $Path));
		}, Path::TIF_NOT_DIRECTORY)->toDirectory()->copy($this->Destination, true);

		return true;
	}

}
