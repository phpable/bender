<?php
namespace Able\Bender\Abstractions;

use Able\Bender\Utilities\Registry;

trait TRegistry {

	/**
	 * @var Registry|null
	 */
	private static ?Registry $Registry = null;

	/**
	 * @return Registry
	 */
	protected final function registry(): Registry {
		if (is_null(static::$Registry)) {
			self::$Registry = new Registry();
		}

		return self::$Registry;
	}
}
