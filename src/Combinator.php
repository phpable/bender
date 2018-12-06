<?php
namespace Able\Bender;

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
	 * @return Directory
	 */
	protected final function getPoint(): Directory {
		return $this->Point;
	}

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
	 * @throws Exception
	 */
	public function execute() {
		while (!is_null($line = $this->Stream->read())) {

			if (preg_match('/^\s+/', $line)) {
				throw new \Exception(sprintf('Invalid offset in line %d!', $this->Stream->getIndex()));
			}

			$this->intepretate(trim($line));
		}
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
		return !is_null($this->Output) ? $this->Output : (new Path(__DIR__))->toDirectory();
	}

	/**
	 * @param string $value
	 * @throws Exception
	 */
	protected final function configureOutput(string $value): void {
		$this->Output = $this->getPoint()->toPath()->append($value)->forceDirectory();
	}

	/**
	 * @param string $line
	 * @throws Exception
	 */
	protected function intepretate(string $line): void {
		echo sprintf("~==>0:%s\n", $line);

		if (preg_match('/^%([A-Za-z0-9]+)\s+=\s+(.+)$/', $line, $Matches)) {
			if (!method_exists($this, $name = sprintf('configure%s', ucfirst($Matches[1])))) {
				throw new \Exception(sprintf('Invalid directive: %s!', $Matches[1]));
			}

			$this->{$name}($Matches[2]);
		} else {
			switch ($line) {
				case 'register';
					(new Register($this->Stream))->execute();
					break;
				default:
					if (preg_match('/^=([A-Za-z0-9_]+\.[A-Za-z0-9]+)/', $line, $Matches)) {
						(new Composer($this->Stream,
							$this->output()->toPath()->append($Matches[1])->forceFile()))->execute();
					}
			}
		}
	}
}
