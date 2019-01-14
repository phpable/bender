<?php
namespace Able\Bender;

use Able\Bender\Interpreters\Combine;
use Able\Bender\Utilities\Registry;
use Able\IO\Directory;
use \Able\IO\Path;
use \Able\IO\File;
use \Able\IO\Reader;
use \Able\IO\ReadingStream;

use \Able\Helpers\Arr;

use \Able\Reglib\Regex;

use \Able\Bender\Interpreters\Register;
use \Able\Bender\Interpreters\Composer;

use \Able\Bender\Structures\SIndent;

use \Exception;
use \Generator;

class Combinator {

	/**
	 * @var ReadingStream
	 */
	private ReadingStream $Stream;

	/**
	 * @var Directory
	 */
	private Directory $Point;

	/**
	 * @param File $Manifest
	 * @param Directory $Point
	 *
	 * @throws Exception
	 */
	public function __construct(File $Manifest, Directory $Point) {
		$this->Stream = $Manifest->toReadingStream();

		if (!$Point->isWritable()) {
			throw new \Exception(sprintf('The target directory is not writable: %s!', $Point->toString()));
		}

		$this->Point = $Point;
	}

	/**
	 * @var Directory|null
	 */
	private ?Directory $Output = null;

	/**
	 * @return Directory
	 * @throws Exception
	 */
	public final function output(): Directory {
		return !is_null($this->Output) ? $this->Output : (new Path(__DIR__))->toDirectory();
	}

	/**
	 * @param string $value
	 * @throws Exception
	 */
	protected final function configureOutput(string $value): void {
		$this->Output = $this->Point->toPath()->append($value)->forceDirectory();
	}

	/**
	 * @var Directory|null
	 */
	private ?Directory $Temporary = null;

	/**
	 * @return Directory
	 * @throws Exception
	 */
	public final function teporary(): Directory {
		return !is_null($this->Temporary) ? $this->Temporary : (new Path(__DIR__))->toDirectory();
	}

	/**
	 * @param string $value
	 * @throws Exception
	 */
	protected final function configureTemporary(string $value): void {
		$this->Temporary = $this->Point->toPath()->append($value)->forceDirectory();
	}


	/**
	 * @throws Exception
	 */
	public function execute() {
		while (!is_null($line = $this->Stream->read())) {
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

			if (preg_match('/^=([A-Za-z0-9_]+\.[A-Za-z0-9]+)/', $line, $Matches)) {

				/**
				 * Lines leading by an equal sign are recognized like targets declarations
				 * and need to be sent to the composer for further processing.
				 */

//				$this->output()->toPath()->append($Matches[1])->forceFile()->purge()
//					->toWriter()->consume(
//

				(new Combine($this->Stream, $this->teporary()))->execute();

				continue;
			}

			switch ($line) {
				case 'register';
					(new Register($this->Stream, $this->Point))->execute();
					break;
				default:
					throw new \Exception('Invalid syntax!');
			}
		}
	}
}
