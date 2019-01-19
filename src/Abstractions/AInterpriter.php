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
	 * @const string
	 */
	public const CT_TEXT = 'text';

	/**
	 * @const string
	 */
	public const CT_JS = 'js';

	/**
	 * @const string
	 */
	public const CT_CSS = 'css';

	/**
	 * @var string
	 */
	private static string $type = self::CT_TEXT;

	/**
	 * @param string $type
	 * @throws Exception
	 */
	public static final function useContentType(string $type) {
		if (!in_array($type, [self::CT_TEXT, self::CT_CSS, self::CT_JS])) {
			throw new Exception(sprintf('Ivalid cotant type: %s', $type));
		}

		self::$type = $type;
	}

	/**
	 * @return string
	 */
	protected final function contentType(): string {
		return self::$type;
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
				continue;
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
