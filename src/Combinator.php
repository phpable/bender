<?php
namespace Able\Bender;

use Able\Bender\Abstractions\AStreamable;
use Able\Bender\Abstractions\AExecutable;

use \Able\Bender\Interpreters\Combine;
use \Able\Bender\Interpreters\Export;
use \Able\Bender\Interpreters\Composer;

use \Able\IO\Directory;
use \Able\IO\ReadingStream;

use \Able\Reglib\Regex;

use \Exception;

class Combinator {

	/**
	 * @var Directory
	 */
	private Directory $Source;

	/**
	 * @return Directory
	 */
	protected final function source(): Directory {
		return  $this->Source;
	}

	/**
	 * @param Directory $Source
	 *
	 * @throws Exception
	 */
	public function __construct(Directory $Source) {
		$this->Source = $Source;
	}

	/**
	 * @var ReadingStream|null
	 */
	private ?ReadingStream $Stream = null;

	/**
	 * @return ReadingStream
	 * @throws Exception
	 */
	protected final function stream(): ReadingStream {
		if (is_null($this->Stream)) {

			$this->Stream = $this->source()
				->toPath()->append('.bender')->toFile()->toReadingStream();
		}

		return $this->Stream;
 	}

	/**
	 * @var Directory|null
	 */
	private ?Directory $Output = null;

	/**
	 * @return Directory
	 * @throws Exception
	 */
	protected final function output(): Directory {
		return !is_null($this->Output) ? $this->Output : $this->source();
	}

	/**
	 * @param string $value
	 * @throws Exception
	 */
	protected final function configureOutput(string $value): void {
		$this->Output = $this->source()
			->toPath()->append($value)->forceDirectory();
	}

	/**
	 * @throws Exception
	 */
	public function execute(): void {
		while (!is_null($line = $this->stream()->read())) {

			if (empty($line)) {

				/**
				 * Empty lines are always ignored.
				 */
				continue;
			}

			if (preg_match('/^#+/', $line)) {

				/**
				 * Lines leading by a hash symbol are recognized
				 * like a single-line comment and always ignored.
				 */
				continue;
			}

			if (preg_match('/^%([A-Za-z0-9]+)\s+=\s+(.+)$/', $line, $Matches)) {

				/**
				 * Lines leading by a percent symbol are recognized as a part
				 * of the configuration and need to be cared for by a special logic.
				 */
				if (!method_exists($this, $name = sprintf('configure%s', ucfirst($Matches[1])))) {
					throw new \Exception(sprintf('Invalid directive: %s!', $Matches[1]));
				}

				$this->{$name}($Matches[2]);
				continue;
			}

			if (preg_match('/^=([A-Za-z0-9_]+\.([A-Za-z0-9]+))/', $line, $Matches)) {
				if (!in_array($Matches[2], ['css', 'js'])) {
					throw new Exception(sprintf('Invalid target type: %s', $Matches[2]));
				}

				AExecutable::useContentType($Matches[2]);
				echo sprintf("=> %s\n", $Matches[1]);

				/**
				 * Lines leading by an equal sign are recognized like targets declarations
				 * and need to be sent to the composer for further processing.
				 */
				$this->output()->toPath()->append($Matches[1])
					->forceFile()->purge()
					->toWriter()->write((new Combine($this->Stream))->execute());

				echo "\n";
				continue;
			}

			if (preg_match('/^(' . Regex::RE_KEYWORD . ')\s*:?\s*$/', $line, $Matches)) {
				switch ($Matches[1]) {
					case 'export';
						(new Export($this->Stream, $this->Source))
							->execute();

						break;
					default:
						throw new \Exception(sprintf('Invalid syntax: %s!', $line));
				}
			}
		}
	}
}
