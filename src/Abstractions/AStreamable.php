<?php
namespace Able\Bender\Abstractions;

use Able\IO\Abstractions\IReader;
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

use \Able\Bender\Abstractions\TNested;
use \Able\Bender\Abstractions\AExecutable;


use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;
use \Generator;

abstract class AStreamable
	extends AExecutable {

	use TNested;

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
			throw new \Exception(sprintf('Directory is not writable: %s!', $Output));
		}

		$this->Output = $Output;
	}

	/**
	 * @var File|null
	 */
	private ?File $Storage = null;

	/**
	 * @param IReader $Stream
	 * @throws Exception
	 */
	protected final function consume(IReader $Stream): void {
		if (is_null($this->Storage)) {
			$this->Storage = $this->point()->toPath()->appendRandom()->forceFile();

			/**
			 * Storing each created file in the cache
			 * is required for cleaning purposes.
			 */
			array_push(self::$Cache, $this->Storage);
		}

		$this->Storage->toWriter()->consume($Stream);
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
		return $this->Storage;
	}
}
