<?php

namespace Wikimedia\Assert;

/**
 * Exception indicating that a parameter type assertion failed.
 * This generally means a disagreement between the caller and the implementation of a function.
 *
 * @license MIT
 * @author Daniel Kinzler
 * @copyright Wikimedia Deutschland e.V.
 */
class ParameterTypeException extends ParameterAssertionException {

	/**
	 * @var string
	 */
	private $parameterType;

	/**
	 * @param string $parameterName
	 * @param string $parameterType
	 * @param mixed $parameterValue
	 *
	 * @throws ParameterTypeException
	 */
	public function __construct( $parameterName, $parameterType, $parameterValue ) {
		if ( !is_string( $parameterType ) ) {
			throw new ParameterTypeException( 'parameterType', 'string', $parameterType );
		}

		$export = var_export( $parameterValue, true );
		parent::__construct( $parameterName, "must be a $parameterType, got $export instead" );

		$this->parameterType = $parameterType;
	}

	/**
	 * @return string
	 */
	public function getParameterType() {
		return $this->parameterType;
	}

}
