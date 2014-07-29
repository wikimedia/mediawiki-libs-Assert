<?php
namespace Wikimedia\Assert;

use InvalidArgumentException;
use LogicException;
use RuntimeException;

/**
 * Assert provides functions for assorting preconditions and postconditions.
 *
 * @license MIT
 * @author Daniel Kinzler
 * @copyright Wikimedia Deutschland e.V.
 */
class Assert {

	/**
	 * Checks a precondition, that is, throws a RuntimeException if $condition is false.
	 * For checking call arguments, use Assert::argument() instead.
	 *
	 * @param bool $condition
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws RuntimeException
	 */
	public static function precondition( $condition, $description ) {
		if ( !$condition ) {
			throw new RuntimeException( "Precondition failed: $description" );
		}
	}

	/**
	 * Checks an argument, that is, throws a InvalidArgumentException if $condition is false.
	 * This is really a special case of Assert::precondition().
	 *
	 * @param bool $condition
	 * @param string $argname The name of the argument that was checked.
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws InvalidArgumentException
	 */
	public static function argument( $condition, $argname, $description ) {
		if ( !$condition ) {
			throw new InvalidArgumentException( "Invalid value given for argument \$$argname: $description" );
		}
	}

	/**
	 * Checks a postcondition, that is, throws a LogicException if $condition is false.
	 * This is very similar Assert::invariant() but is intended for use only after a computation is complete.
	 *
	 * @param bool $condition
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws LogicException
	 */
	public static function postcondition( $condition, $description ) {
		if ( !$condition ) {
			throw new LogicException( "Postcondition failed: $description" );
		}
	}

	/**
	 * Checks an invariant, that is, throws a LogicException if $condition is false.
	 * This is very similar Assert::postcondition() but is intended for use throughout the code.
	 *
	 * @param bool $condition
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws LogicException
	 */
	public static function invariant( $condition, $description ) {
		if ( !$condition ) {
			throw new LogicException( "Invariant failed: $description" );
		}
	}

}