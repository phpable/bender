<?php
namespace Able\Bender\Abstractions;

use \Able\IO\Path;
use \Able\IO\File;
use \Able\IO\Writer;
use \Able\IO\Reader;
use \Able\IO\Directory;

use \Able\IO\Abstractions\AStreamReader;

use \Able\IO\ReadingStream;
use \Able\IO\WritingBuffer;

use \Able\Reglib\Regex;

use \Able\Bender\Structures\SIndent;
use \Able\Bender\Utilities\Registry;

use \Able\Bender\Abstractions\TIndent;
use \Able\Bender\Abstractions\TRegistry;

use \Able\Prototypes\IExecutable;

use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;

abstract class AInterpriter
	extends AStreamReader

	implements IExecutable {

	use TIndent;
	use TRegistry;

	/**
	 * @var string[]
	 */
	private array $Arguments = [];

	/**
	 * @return string[]
	 */
	protected final function getArguments(): array {
		return $this->Arguments;
	}

	/**
	 * @param string $line
	 * @return AInterpriter
	 *
	 * @throws Exception
	 */
	public final function withArguments(string $line): AInterpriter {
		if (!preg_match('/^{([^}]+)}$/', $line, $Matches)) {
			throw new \Exception(sprintf('Invalid arguments: %s!', $line));
		}

		$this->Arguments = preg_split('/\s*,+\s*/',
			$Matches[1], -1 , PREG_SPLIT_NO_EMPTY);

		return $this;
	}

	/**
	 * @return AInterpriter
	 * @throws Exception
	 */
	public function execute(): AInterpriter {
		while (!is_null($line = $this->stream()->read())) {

			if (empty($line)) {

				/**
				 * Empty lines are always ignored.
				 */
				continue;
			}

			if (preg_match('/^\s*#+/', $line)) {

				/**
				 * Lines leading by a hash symbol are recognized
				 * like a single-line comment and always ignored.
				 */
				continue;
			}

			/**
			 * If the indentation was decreased,
			 * the current line has to be returned to the stream.
			 */
			if ($this->parseIndention($line)) {
				$this->stream()->rollback();
				break;
			}

			if ($this->analize($line)) {
				break;
			}

			$this->interpretate($line);
		}

		return $this;
	}

	/**
	 * @param string $line
	 * @return bool
	 */
	protected function analize(string $line): bool {
		return false;
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	protected function interpretate(string $line): void {
		throw new \Exception(sprintf('Invalid instruction: %s!', $line));
	}
}
