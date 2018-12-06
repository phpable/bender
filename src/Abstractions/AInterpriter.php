<?php
namespace Able\Bender\Abstractions;

use \Able\IO\ReadingStream;
use \Able\IO\WritingBuffer;

use \Able\IO\Abstractions\AStreamReader;

use \Able\Reglib\Regex;

use \Able\Bender\Structures\SIndent;
use \Able\Bender\Abstractions\TOption;

use \Able\Prototypes\IExecutable;

use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;
use \Generator;

abstract class AInterpriter
	extends AStreamReader

	implements IExecutable {
	use TOption;

	/**
	 * @var SIndent
	 */
	private SIndent $Indent;

	/**
	 * @return SIndent
	 */
	protected final function indent(): SIndent {
		return $this->Indent;
	}

	/**
	 * @var WritingBuffer
	 */
	private WritingBuffer $Output;

	/**
	 * @return WritingBuffer
	 */
	protected final function output(): WritingBuffer {
		return $this->Output;
	}

	/**
	 * @return Generator
	 * @throws Exception
	 */
	public final function read(): Generator {
		yield from $this->output()->toReadingBuffer()->read();
	}

	/**
	 * @param ReadingStream $Stream
	 */
	public function __construct(ReadingStream $Stream) {
		parent::__construct($Stream);

		$this->Indent = new SIndent();
		$this->Output = new WritingBuffer();
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

				$Nested = new $class($this->stream());
				$Nested->execute();

				$this->output()->write($Nested->output()->toReadingBuffer()->read());
				return true;
			}
		}

		return false;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public final function execute(): void {
		while (!is_null($line = $this->stream()->read())) {
			if (!empty($line)) {

				$this->indent()->analize(Regex::create('/^\s+/')
					->take($line));

				if ($this->indent()->level < 1) {
					$this->stream()->rollback();
					$this->finalize();
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
		}
	}

	/**
	 * @param string $line
	 * @return void
	 */
	abstract protected function interpretate(string $line): void;

	/**
	 * @return void
	 */
	abstract protected function finalize(): void;
}
