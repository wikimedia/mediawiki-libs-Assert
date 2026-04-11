<?php
declare( strict_types = 1 );

namespace Wikimedia\Assert;

use InvalidArgumentException;

/**
 * Exception indicating that an parameter assertion failed.
 * This generally means a disagreement between the caller and the implementation of a function.
 *
 * @since 0.1.0
 *
 * @license MIT
 * @author Daniel Kinzler
 * @copyright Wikimedia Deutschland e.V.
 */
class ParameterAssertionException extends InvalidArgumentException implements AssertionException {

	private string $parameterName;

	public function __construct( string $parameterName, string $description ) {
		parent::__construct( "Bad value for parameter $parameterName: $description" );

		$this->parameterName = $parameterName;
	}

	public function getParameterName(): string {
		return $this->parameterName;
	}

}
