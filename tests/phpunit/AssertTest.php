<?php

namespace Wikimedia\Assert\Test;

use ArrayObject;
use LogicException;
use PHPUnit_Framework_TestCase;
use RuntimeException;
use stdClass;
use Wikimedia\Assert\Assert;
use Wikimedia\Assert\ParameterAssertionException;
use Wikimedia\Assert\ParameterElementTypeException;
use Wikimedia\Assert\ParameterKeyTypeException;
use Wikimedia\Assert\ParameterTypeException;

/**
 * @covers Wikimedia\Assert\Assert
 *
 * @license MIT
 * @author Daniel Kinzler
 * @author Thiemo Mättig
 * @copyright Wikimedia Deutschland e.V.
 */
class AssertTest extends PHPUnit_Framework_TestCase {

	public function testPrecondition_pass() {
		Assert::precondition( true, 'test' );
	}

	/**
	 * @covers Wikimedia\Assert\PreconditionException
	 */
	public function testPrecondition_fail() {
		$this->setExpectedException( 'Wikimedia\Assert\PreconditionException' );
		Assert::precondition( false, 'test' );
	}

	public function testParameter_pass() {
		Assert::parameter( true, 'foo', 'test' );
	}

	/**
	 * @covers Wikimedia\Assert\ParameterAssertionException
	 */
	public function testParameter_fail() {
		try {
			Assert::parameter( false, 'test', 'testing' );
			$this->fail( 'Expected ParameterAssertionException' );
		} catch ( ParameterAssertionException $ex ) {
			$this->assertSame( 'test', $ex->getParameterName() );
		}
	}

	public function validParameterTypeProvider() {
		return array(
			'simple' => array( 'string', 'hello' ),
			'boolean' => array( 'boolean', true ),
			'integer' => array( 'integer', 1 ),
			'double' => array( 'double', 1.0 ),
			'object' => array( 'object', new stdClass() ),
			'class' => array( 'RuntimeException', new RuntimeException() ),
			'subclass' => array( 'Exception', new RuntimeException() ),
			'stdClass' => array( 'stdClass', new stdClass() ),
			'multi' => array( 'string|array|Closure', function() {
			} ),
			'null' => array( 'integer|null', null ),

			'callable' => array( 'null|callable', 'time' ),
			'static callable' => array( 'callable', 'Wikimedia\Assert\Assert::parameterType' ),
			'callable array' => array( 'callable', array( 'Wikimedia\Assert\Assert', 'parameterType' ) ),
			'callable $this' => array( 'callable', array( $this, 'validParameterTypeProvider' ) ),
			'Closure is callable' => array( 'callable', function() {
			} ),

			'Traversable' => array( 'Traversable', new ArrayObject() ),
			'Traversable array' => array( 'Traversable', array() ),
		);
	}

	/**
	 * @dataProvider validParameterTypeProvider
	 */
	public function testParameterType_pass( $type, $value ) {
		Assert::parameterType( $type, $value, 'test' );
	}

	public function invalidParameterTypeProvider() {
		return array(
			'bool shortcut is not accepted' => array( 'bool', true ),
			'int shortcut is not accepted' => array( 'int', 1 ),
			'float alias is not accepted' => array( 'float', 1.0 ),
			'callback alias is not accepted' => array( 'callback', 'time' ),

			'simple' => array( 'string', 5 ),
			'integer is not boolean' => array( 'boolean', 1 ),
			'string is not boolean' => array( 'boolean', '0' ),
			'boolean is not integer' => array( 'integer', true ),
			'string is not integer' => array( 'integer', '0' ),
			'double is not integer' => array( 'integer', 1.0 ),
			'integer is not double' => array( 'double', 1 ),
			'class' => array( 'RuntimeException', new LogicException() ),
			'stdClass is no superclass' => array( 'stdClass', new LogicException() ),
			'multi' => array( 'string|integer|Closure', array() ),
			'null' => array( 'integer|string', null ),

			'callable' => array( 'null|callable', array() ),
			'callable is no Closure' => array( 'Closure', 'time' ),
			'object is not callable' => array( 'callable', new stdClass() ),

			'object is not Traversable' => array( 'Traversable', new stdClass() ),
			'Traversable is not Iterator' => array( 'Iterator', new ArrayObject() ),
		);
	}

	/**
	 * @dataProvider invalidParameterTypeProvider
	 * @covers Wikimedia\Assert\ParameterTypeException
	 */
	public function testParameterType_fail( $type, $value ) {
		try {
			Assert::parameterType( $type, $value, 'test' );
			$this->fail( 'Expected ParameterTypeException' );
		} catch ( ParameterTypeException $ex ) {
			$this->assertSame( $type, $ex->getParameterType() );
			$this->assertSame( 'test', $ex->getParameterName() );
		}
	}

	/**
	 * @covers Wikimedia\Assert\AssertionException
	 */
	public function testParameterType_catch() {
		$this->setExpectedException( 'Wikimedia\Assert\AssertionException' );
		Assert::parameterType( 'string', 17, 'test' );
	}

	public function validParameterKeyTypeProvider() {
		return array(
			array( 'integer', array() ),
			array( 'integer', array( 1 ) ),
			array( 'integer', array( 1 => 1 ) ),
			array( 'integer', array( 1.0 => 1 ) ),
			array( 'integer', array( '0' => 1 ) ),
			array( 'integer', array( false => 1 ) ),
			array( 'string', array() ),
			array( 'string', array( '' => 1 ) ),
			array( 'string', array( '0.0' => 1 ) ),
			array( 'string', array( 'string' => 1 ) ),
			array( 'string', array( null => 1 ) ),
		);
	}

	/**
	 * @dataProvider validParameterKeyTypeProvider
	 */
	public function testParameterKeyType_pass( $type, $value ) {
		Assert::parameterKeyType( $type, $value, 'test' );
	}

	public function invalidParameterKeyTypeProvider() {
		return array(
			array( 'integer', array( 0, 'string' => 1 ) ),
			array( 'integer', array( 'string' => 0, 1 ) ),
			array( 'string', array( 0, 'string' => 1 ) ),
			array( 'string', array( 'string' => 0, 1 ) ),
		);
	}

	/**
	 * @dataProvider invalidParameterKeyTypeProvider
	 * @covers Wikimedia\Assert\ParameterKeyTypeException
	 */
	public function testParameterKeyType_fail( $type, $value ) {
		try {
			Assert::parameterKeyType( $type, $value, 'test' );
			$this->fail( 'Expected ParameterKeyTypeException' );
		} catch ( ParameterKeyTypeException $ex ) {
			$this->assertSame( $type, $ex->getType() );
			$this->assertSame( 'test', $ex->getParameterName() );
		}
	}

	/**
	 * @covers Wikimedia\Assert\ParameterAssertionException
	 */
	public function testGivenUnsupportedType_ParameterKeyTypeFails() {
		$this->setExpectedException(
			'Wikimedia\Assert\ParameterAssertionException',
			'Bad value for parameter type: must be "integer" or "string"'
		);
		Assert::parameterKeyType( 'integer|string', array(), 'test' );
	}

	public function validParameterElementTypeProvider() {
		return array(
			'empty' => array( 'string', array() ),
			'simple' => array( 'string', array( 'hello', 'world' ) ),
			'class' => array( 'RuntimeException', array( new RuntimeException() ) ),
			'multi' => array( 'string|array|Closure', array( array(), function() {
			} ) ),
			'null' => array( 'integer|null', array( null, 3, null ) ),
		);
	}

	/**
	 * @dataProvider validParameterElementTypeProvider
	 */
	public function testParameterElementType_pass( $type, $value ) {
		Assert::parameterElementType( $type, $value, 'test' );
	}

	public function invalidParameterElementTypeProvider() {
		return array(
			'simple' => array( 'string', array( 'hello', 5 ) ),
			'class' => array( 'RuntimeException', array( new LogicException() ) ),
			'multi' => array( 'string|array|Closure', array( array(), function() {
			}, 5 ) ),
			'null' => array( 'integer|string', array( null, 3, null ) ),
		);
	}

	/**
	 * @dataProvider invalidParameterElementTypeProvider
	 * @covers Wikimedia\Assert\ParameterElementTypeException
	 */
	public function testParameterElementType_fail( $type, $value ) {
		try {
			Assert::parameterElementType( $type, $value, 'test' );
			$this->fail( 'Expected ParameterElementTypeException' );
		} catch ( ParameterElementTypeException $ex ) {
			$this->assertSame( $type, $ex->getElementType() );
			$this->assertSame( 'test', $ex->getParameterName() );
		}
	}

	/**
	 * @covers Wikimedia\Assert\ParameterTypeException
	 */
	public function testParameterElementType_bad() {
		$this->setExpectedException( 'Wikimedia\Assert\ParameterTypeException' );
		Assert::parameterElementType( 'string', 'foo', 'test' );
	}

	function validNonEmptyStringProvider() {
		return array(
			array( '0' ),
			array( '0.0' ),
			array( ' ' ),
			array( "\n" ),
			array( 'test' ),
		);
	}

	/**
	 * @dataProvider validNonEmptyStringProvider
	 */
	public function testNonEmptyString_pass( $value ) {
		Assert::nonEmptyString( $value, 'test' );
	}

	function invalidNonEmptyStringProvider() {
		return array(
			array( null ),
			array( false ),
			array( 0 ),
			array( 0.0 ),
			array( '' ),
		);
	}

	/**
	 * @dataProvider invalidNonEmptyStringProvider
	 * @covers Wikimedia\Assert\ParameterTypeException
	 */
	public function testNonEmptyString_fail( $value ) {
		$this->setExpectedException(
			'Wikimedia\Assert\ParameterTypeException',
			'Bad value for parameter test: must be a non-empty string'
		);
		Assert::nonEmptyString( $value, 'test' );
	}

	public function testInvariant_pass() {
		Assert::invariant( true, 'test' );
	}

	/**
	 * @covers Wikimedia\Assert\InvariantException
	 */
	public function testInvariant_fail() {
		$this->setExpectedException( 'Wikimedia\Assert\InvariantException' );
		Assert::invariant( false, 'test' );
	}

	public function testPostcondition_pass() {
		Assert::postcondition( true, 'test' );
	}

	/**
	 * @covers Wikimedia\Assert\PostconditionException
	 */
	public function testPostcondition_fail() {
		$this->setExpectedException( 'Wikimedia\Assert\PostconditionException' );
		Assert::postcondition( false, 'test' );
	}

}
