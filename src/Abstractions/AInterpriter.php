<?php
namespace Able\Bender\Abstractions;

use \Able\IO\ReadingStream;
use \Able\IO\Abstractions\AStreamReader;

use \Able\Reglib\Regex;

use \Able\Bender\Structures\SIndent;

use \Exception;

abstract class AInterpriter
	extends AStreamReader {

	/**
	 * @var SIndent
	 */
	protected SIndent $Indent;

	/**
	 * @param ReadingStream $Stream
	 */
	public function __construct(ReadingStream $Stream) {
		parent::__construct($Stream);
		$this->Indent = new SIndent();
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function execute(): void {
		while (!is_null($line = $this->stream()->read())) {
			if (!empty($line)) {

				$this->Indent->analize(Regex::create('/^\s+/')
					->take($line));

				if ($this->Indent->level < 1) {
					$this->stream()->rollback();
					$this->finalize();
					break;
				}

				$this->interpretate(trim($line));
			}
		}
	}

	/**
	 * @param string $line
	 */
	abstract protected function interpretate(string $line): void;

	/**
	 * @return void
	 */
	abstract protected function finalize(): void;
}
