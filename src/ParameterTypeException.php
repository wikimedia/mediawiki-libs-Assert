<?php
declare( strict_types = 1 );

namespace Wikimedia\Assert;

/**
 * Exception indicating that a parameter type assertion failed.
 * This generally means a disagreement between the caller and the implementation of a function.
 *
 * @since 0.1.0
 *
 * @license MIT
 * @author Daniel Kinzler
 * @copyright Wikimedia Deutschland e.V.
 */
class ParameterTypeException extends ParameterAssertionException {

	private string $parameterType;

	/**
	 * @throws ParameterTypeException
	 */
	public function __construct( string $parameterName, string $parameterType ) {
		parent::__construct( $parameterName, "must be a $parameterType" );

		$this->parameterType = $parameterType;
	}

	public function getParameterType(): string {
		return $this->parameterType;
	}

}
