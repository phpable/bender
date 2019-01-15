<?php
namespace Able\Bender\Abstractions;

use \Able\IO\Path;
use \Able\IO\File;
use \Able\IO\Writer;
use \Able\IO\Reader;
use \Able\IO\Directory;

use \Able\IO\ReadingStream;
use \Able\IO\WritingBuffer;

use \Able\IO\Abstractions\AStreamReader;

use \Able\Reglib\Regex;

use \Able\Bender\Structures\SIndent;
use \Able\Bender\Utilities\Registry;

use \Able\Bender\Abstractions\TOption;
use \Able\Bender\Abstractions\TIndent;
use \Able\Bender\Abstractions\TRegistry;

use \Able\Prototypes\IExecutable;

use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;
use \Generator;

abstract class AInterpriter
	extends AStreamReader

	implements IExecutable {

	use TOption;
	use TIndent;
	use TRegistry;

	/**
	 * @var Directory
	 */
	private Directory $Point;

	/**
	 * @return Directory
	 */
	protected final function point(): Directory {
		return $this->Point;
	}

	/**
	 * @param ReadingStream $Stream
	 * @param Directory $Point
	 *
	 * @throws Exception
	 */
	public function __construct(ReadingStream $Stream, Directory $Point) {
		parent::__construct($Stream);

		if (!is_writable($Point)) {
			throw new \Exception(sprintf('Pointed directory is not writable: %s!', $Point));
		}

		$this->Point = $Point;
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
		}

		return $this->Storage;
	}

	/**
	 * @return Writer
	 * @throws Exception
	 */
	protected final function input(): Writer {
		return $this->storage()->toWriter();
	}

	/**
	 * @return Reader
	 * @throws Exception
	 */
	protected final function output(): Reader {
		return $this->storage()->toReader();
	}

	/**
	 * @throws Exception
	 */
	public function __destruct() {
		if (!is_null($this->Storage)) {
			$this->storage()->remove();
		}
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
			 	Src::lns(AInterpriter::class, 2), Src::tcm($Parsed[1])))) {

				$Nested = new $class($this->stream(), $this->Point);
				$Nested->execute();

				$this->input()->consume($Nested->output());
				return true;
			}
		}

		return false;
	}

	/**
	 * @return AInterpriter
	 * @throws Exception
	 */
	public final function execute(): AInterpriter {
		while (!is_null($line = $this->stream()->read())) {
			if (empty($line)) {

				/**
				 * Empty lines are always ignored.
				 */
				continue;
			}

			$this->indent()->analize(Regex::create('/^\s+/')
				->take($line));

			if ($this->indent()->level < 1) {
				$this->stream()->rollback();
				break;
			}

			if ($this->parseOption($line = trim($line))) {
				continue;
			}

			if ($this->parseNested($line)) {
				continue;
			}

			$this->interpretate($line);
		}

		return $this;
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	protected function interpretate(string $line): void {
		throw new \Exception(sprintf('Invalid instruction: %s!', $line));
	}
}
