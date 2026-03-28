<?php
declare( strict_types = 1 );

namespace Wikimedia\Assert;

/**
 * Exception indicating that a parameter element type assertion failed.
 * This generally means a disagreement between the caller and the implementation of a function.
 *
 * @since 0.1.0
 *
 * @license MIT
 * @author Daniel Kinzler
 * @copyright Wikimedia Deutschland e.V.
 */
class ParameterElementTypeException extends ParameterAssertionException {

	private string $elementType;

	/**
	 * @throws ParameterTypeException
	 */
	public function __construct( string $parameterName, string $elementType ) {
		parent::__construct( $parameterName, "all elements must be $elementType" );

		$this->elementType = $elementType;
	}

	public function getElementType(): string {
		return $this->elementType;
	}

}
