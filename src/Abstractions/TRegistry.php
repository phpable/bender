<?php
namespace Able\Bender\Abstractions;

use \Able\Bender\Utilities\Registry;
use \Able\IO\Directory;

use \Exception;

trait TRegistry {

	/**
	 * @var Directory|null
	 */
	protected ?Directory $Prefix = null;

	/**
	 * @var Registry|null
	 */
	private static ?Registry $Registry = null;

	/**
	 * @return Registry
	 * @throws Exception
	 */
	protected final function registry(): Registry {
		if (is_null(static::$Registry)) {
			self::$Registry = new Registry($this->Prefix);
		}

		return self::$Registry;
	}
}
