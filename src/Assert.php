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
	 * Checks a precondition, that is, throws a PreconditionException if $condition is false.
	 * For checking call parameters, use Assert::parameter() instead.
	 *
	 * @param bool $condition
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws RuntimeException
	 */
	public static function precondition( $condition, $description ) {
		if ( !$condition ) {
			throw new PreconditionException( "Precondition failed: $description" );
		}
	}

	/**
	 * Checks a parameter, that is, throws a ParameterAssertionException if $condition is false.
	 * This is similar to Assert::precondition().
	 *
	 * @param bool $condition
	 * @param string $argname The name of the parameter that was checked.
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws InvalidArgumentException
	 */
	public static function parameter( $condition, $argname, $description ) {
		if ( !$condition ) {
			throw new ParameterAssertionException( $argname, $description );
		}
	}

	/**
	 * Checks an parameter's type, that is, throws a InvalidArgumentException if $condition is false.
	 * This is really a special case of Assert::precondition().
	 *
	 * @param string $type The parameter's expected type. Can be the name of a native type or a
	 *        class or interface. If multiple types are allowed, they can be given separated by
	 *        a pipe character ("|").
	 * @param mixed $value The parameter's actual value.
	 * @param string $argname The name of the parameter that was checked.
	 *
	 * @throws ParameterTypeException
	 */
	public static function parameterType( $type, $value, $argname ) {
		if ( !self::hasType( $value, explode( '|', $type ) ) ) {
			throw new ParameterTypeException( $argname, $type );
		}
	}

	/**
	 * Checks the type of all elements of an parameter, assuming the parameter is an array, 
	 * that is, throws a ParameterElementTypeException if $value
	 *
	 * @param string $type The elements' expected type. Can be the name of a native type or a
	 *        class or interface. If multiple types are allowed, they can be given separated by
	 *        a pipe character ("|").
	 * @param mixed $value The parameter's actual value.
	 * @param string $argname The name of the parameter that was checked.
	 *
	 * @throws ParameterTypeException If the parameter is not an array.
	 * @throws ParameterElementTypeException If an element has the wrong type
	 */
	public static function parameterElementType( $type, $value, $argname ) {
		$allowedTypes = explode( '|', $type );

		self::parameterType( 'array', $value, $argname );

		foreach ( $value as $element ) {
			if ( !self::hasType( $element, $allowedTypes ) ) {
				throw new ParameterElementTypeException( $argname, $type );
			}
		}
	}

	/**
	 * Checks a postcondition, that is, throws a PostconditionException if $condition is false.
	 * This is very similar Assert::invariant() but is intended for use only after a computation is complete.
	 *
	 * @param bool $condition
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws LogicException
	 */
	public static function postcondition( $condition, $description ) {
		if ( !$condition ) {
			throw new PostconditionException( "Postcondition failed: $description" );
		}
	}

	/**
	 * Checks an invariant, that is, throws a InvariantException if $condition is false.
	 * This is very similar Assert::postcondition() but is intended for use throughout the code.
	 *
	 * @param bool $condition
	 * @param string $description The message to include in the exception if the condition fails.
	 *
	 * @throws LogicException
	 */
	public static function invariant( $condition, $description ) {
		if ( !$condition ) {
			throw new InvariantException( "Invariant failed: $description" );
		}
	}

	/**
	 * @param mixed $value
	 * @param array $allowedTypes
	 *
	 * @return bool
	 */
	private static function hasType( $value, array $allowedTypes ) {
		$type = strtolower( gettype( $value ) );

		if ( in_array( $type, $allowedTypes ) ) {
			return true;
		}

		if ( is_object( $value ) && self::isInstanceOf( $value, $allowedTypes ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param mixed $value
	 * @param array $allowedTypes
	 *
	 * @return bool
	 */
	private static function isInstanceOf( $value, array $allowedTypes ) {
		foreach ( $allowedTypes as $type ) {
			if ( $value instanceof $type ) {
				return true;
			}
		}

		return false;
	}
}