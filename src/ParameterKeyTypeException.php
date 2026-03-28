<?php
declare( strict_types = 1 );

namespace Wikimedia\Assert;

/**
 * Exception indicating that a parameter key type assertion failed.
 * This generally means a disagreement between the caller and the implementation of a function.
 *
 * @since 0.3.0
 *
 * @license MIT
 * @author Daniel Kinzler
 * @author Thiemo Kreuz
 * @copyright Wikimedia Deutschland e.V.
 */
class ParameterKeyTypeException extends ParameterAssertionException {

	private string $type;

	/**
	 * @throws ParameterTypeException
	 */
	public function __construct( string $parameterName, string $type ) {
		parent::__construct( $parameterName, "all elements must have $type keys" );

		$this->type = $type;
	}

	public function getType(): string {
		return $this->type;
	}

}
