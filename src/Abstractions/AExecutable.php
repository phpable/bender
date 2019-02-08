<?php
namespace Able\Bender\Abstractions;

use \Able\IO\Abstractions\AStreamReader;

use \Able\Prototypes\TTraitable;
use \Able\Prototypes\IExecutable;

use \Able\Bender\Abstractions\TIndent;
use \Able\Bender\Abstractions\TOption;
use \Able\Bender\Abstractions\TRegistry;

use \Exception;
use \Generator;

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
	 * @var Generator[];
	 */
	private array $Content = [];

	/**
	 * @return Generator
	 * @throws Exception
	 */
	public final function execute(): \Generator {
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
			yield from $this->parseTraits($line);
		}
	}

	/**
	 * @param string $line
	 * @return Generator|null
	 *
	 * @throws Exception
	 */
	protected final function parseTraits(string $line): ?Generator {
		foreach ($this->propagate('parse', $line) as $_ => $value) {
			if (is_bool($value) && $value) {
				break;
			}

			if ($value instanceof Generator) {
				yield from $this->process($value);
			}
		}
	}

	/**
	 * @param Generator $Stream
	 * @return Generator
	 */
	protected function process(Generator $Stream): Generator {
		yield from $Stream;
	}
}
