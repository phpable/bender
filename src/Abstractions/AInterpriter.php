<?php
namespace Able\Bender\Abstractions;

use \Able\IO\ReadingStream;
use \Able\Reglib\Regex;

use \Able\Bender\Structures\SIndent;

use \Exception;

abstract class AInterpriter {

	/**
	 * @var ReadingStream
	 */
	private ReadingStream $Stream;

	/**
	 * @var SIndent
	 */
	protected SIndent $Indent;

	/**
	 * @param ReadingStream $Stream
	 */
	public function __construct(ReadingStream $Stream) {
		$this->Stream = $Stream;
		$this->Indent = new SIndent();
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function execute(): void {
		while (!is_null($line = $this->Stream->read())) {
			$this->Indent->analize(Regex::create('/^\s+/')
				->take($line));

			if ($this->Indent->level < 1) {
				$this->Stream->rollback();
				break;
			}

			$this->interpretate(trim($line));
		}
	}

	/**
	 * @param string $line
	 */
	abstract public function interpretate(string $line): void;
}
