<?php
namespace Able\Bender\Abstractions;

use \Able\IO\Abstractions\AStreamReader;

use \Able\Prototypes\TTraitable;
use \Able\Prototypes\IExecutable;

use \Able\Bender\Abstractions\TIndent;
use \Able\Bender\Abstractions\TOption;
use \Able\Bender\Abstractions\TRegistry;

use \Exception;

abstract class AExecutable
	extends AStreamReader

	implements IExecutable {

	use TTraitable;
	use TRegistry;
	use TIndent;

	use TOption;

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
	 * @return AExecutable
	 * @throws Exception
	 */
	public function execute(): AExecutable {
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
			 * the current line must be released back to the stream.
			 */
			if ($this->analizeIndention($line)) {
				$this->stream()->rollback();
				break;
			}

			/**
			 * Traits could extend the standard behavior
			 * via the special traitable interface.
			 */
			if ($this->parseTraits($line)) {
				continue;
			}

			/**
			 * Any line that passed through the conditions
			 * must be sent for further interpretation.
			 */
			$this->interpretate($line);
		}

		return $this;
	}

	/**
	 * @param string $line
	 * @return bool
	 *
	 * @throws Exception
	 */
	protected final function parseTraits(string $line): bool {
		foreach ($this->propagate('parse', $line) as $_ => $value) {
			if (!is_bool($value)) {
				throw new Exception(sprintf('Unsupported behavior: %s!', $_));
			}

			if ($value) {
				return true;
			}
		}

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
