<?php
namespace Able\Bender\Abstractions;

use \Able\IO\Path;
use \Able\IO\File;
use \Able\IO\Writer;
use \Able\IO\Reader;
use \Able\IO\Directory;

use \Able\IO\ReadingStream;
use \Able\IO\WritingBuffer;

use \Able\Reglib\Regex;

use \Able\Bender\Structures\SIndent;
use \Able\Bender\Utilities\Registry;

use \Able\Bender\Abstractions\TOption;
use \Able\Bender\Abstractions\TIndent;
use \Able\Bender\Abstractions\TRegistry;
use \Able\Bender\Abstractions\AInterpriter;

use \Able\Prototypes\IExecutable;

use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;
use \Generator;

abstract class AStreamable
	extends AInterpriter

	implements IExecutable {

	use TOption;

	/**
	 * @var Directory
	 */
	private Directory $Output;

	/**
	 * @return Directory
	 */
	protected final function point(): Directory {
		return $this->Output;
	}

	/**
	 * @var File[]
	 */
	private static array $Cache = [];

	/**
	 * @throws Exception
	 */
	public static final function clear(): void {
		foreach (self::$Cache as $File) {
			$File->remove();
		}
	}

	/**
	 * @param ReadingStream $Stream
	 * @param Directory $Output
	 *
	 * @throws Exception
	 */
	public function __construct(ReadingStream $Stream, Directory $Output) {
		parent::__construct($Stream);

		if (!is_writable($Output)) {
			throw new \Exception(sprintf('Pointed directory is not writable: %s!', $Output));
		}

		$this->Output = $Output;
	}

	/**
	 * @var File|null
	 */
	private ?File $Storage = null;

	/**
	 * @return File
	 * @throws Exception
	 */
	public final function storage(): File {
		if (is_null($this->Storage)) {
			$this->Storage = $this->point()->toPath()->appendRandom()->forceFile();

			/**
			 * Storing each created file in the cache
			 * is required for cleaning purposes.
			 */
			array_push(self::$Cache, $this->Storage);
		}

		return $this->Storage;
	}

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	public final function parseNested(string $line): bool {
		if (preg_match('/^([A-Za-z0-9_-]+):\s*$/', $line, $Parsed)) {

			if (class_exists($class = sprintf('%s\\Interpreters\\%s',
			 	Src::lns(AStreamable::class, 2), Src::tcm($Parsed[1])))) {

				$this->storage()->toWriter()
					->consume($this->process((new $class($this->stream(), $this->Output))->execute()->storage()));

				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	public final function analize(string $line): bool {
		return $this->parseOption($line) || $this->parseNested($line);
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	protected function interpretate(string $line): void {
		throw new \Exception(sprintf('Invalid instruction: %s!', $line));
	}

	/**
	 * @param File $File
	 * @return Reader
	 *
	 * @throws Exception
	 */
	protected function process(File $File): Reader {
		return $File->toReader();
	}

	/**
	 * @return File
	 * @throws Exception
	 */
	public final function toFile(): File {
		return $this->storage();
	}
}
