<?php

namespace Wikimedia\Assert\Test;

use ArrayObject;
use LogicException;
use RuntimeException;
use stdClass;
use Wikimedia\Assert\Assert;
use Wikimedia\Assert\AssertionException;
use Wikimedia\Assert\InvariantException;
use Wikimedia\Assert\ParameterAssertionException;
use Wikimedia\Assert\ParameterElementTypeException;
use Wikimedia\Assert\ParameterKeyTypeException;
use Wikimedia\Assert\ParameterTypeException;
use Wikimedia\Assert\PostconditionException;
use Wikimedia\Assert\PreconditionException;

/**
 * @covers \Wikimedia\Assert\Assert
 *
 * @license MIT
 * @author Daniel Kinzler
 * @author Thiemo Kreuz
 * @copyright Wikimedia Deutschland e.V.
 */
class AssertTest extends \PHPUnit\Framework\TestCase {

	public function testPrecondition_pass() {
		Assert::precondition( true, 'test' );
		$this->addToAssertionCount( 1 );
	}

	/**
	 * @covers \Wikimedia\Assert\PreconditionException
	 */
	public function testPrecondition_fail() {
		$this->expectException( PreconditionException::class );
		Assert::precondition( false, 'test' );
	}

	public function testParameter_pass() {
		Assert::parameter( true, 'foo', 'test' );
		$this->addToAssertionCount( 1 );
	}

	/**
	 * @covers \Wikimedia\Assert\ParameterAssertionException
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
		return [
			'simple' => [ 'string', 'hello' ],
			'boolean' => [ 'boolean', true ],
			'integer' => [ 'integer', 1 ],
			'double' => [ 'double', 1.0 ],
			'object' => [ 'object', new stdClass() ],
			'class' => [ 'RuntimeException', new RuntimeException() ],
			'subclass' => [ 'Exception', new RuntimeException() ],
			'stdClass' => [ 'stdClass', new stdClass() ],
			'multi' => [ [ 'string', 'array', 'Closure' ], function () {
			} ],
			'multi (old)' => [ 'string|array|Closure', function () {
			} ],
			'null' => [ [ 'integer', 'null' ], null ],

			'callable' => [ [ 'null', 'callable' ], 'time' ],
			'static callable' => [ 'callable', 'Wikimedia\Assert\Assert::parameterType' ],
			'callable array' => [ 'callable', [ 'Wikimedia\Assert\Assert', 'parameterType' ] ],
			'callable $this' => [ 'callable', [ $this, 'validParameterTypeProvider' ] ],
			'Closure is callable' => [ 'callable', function () {
			} ],

			'Traversable' => [ 'Traversable', new ArrayObject() ],
			'Traversable array' => [ 'Traversable', [] ],
		];
	}

	/**
	 * @dataProvider validParameterTypeProvider
	 */
	public function testParameterType_pass( $type, $value ) {
		Assert::parameterType( $type, $value, 'test' );
		$this->addToAssertionCount( 1 );
	}

	public function invalidParameterTypeProvider() {
		return [
			'bool shortcut is not accepted' => [ 'bool', true ],
			'int shortcut is not accepted' => [ 'int', 1 ],
			'float alias is not accepted' => [ 'float', 1.0 ],
			'callback alias is not accepted' => [ 'callback', 'time' ],

			'simple' => [ 'string', 5 ],
			'integer is not boolean' => [ 'boolean', 1 ],
			'string is not boolean' => [ 'boolean', '0' ],
			'boolean is not integer' => [ 'integer', true ],
			'string is not integer' => [ 'integer', '0' ],
			'double is not integer' => [ 'integer', 1.0 ],
			'integer is not double' => [ 'double', 1 ],
			'class' => [ 'RuntimeException', new LogicException() ],
			'stdClass is no superclass' => [ 'stdClass', new LogicException() ],
			'multi' => [ 'string|integer|Closure', [] ],
			'null' => [ 'integer|string', null ],

			'callable' => [ 'null|callable', [] ],
			'callable is no Closure' => [ 'Closure', 'time' ],
			'object is not callable' => [ 'callable', new stdClass() ],

			'object is not Traversable' => [ 'Traversable', new stdClass() ],
			'Traversable is not Iterator' => [ 'Iterator', new ArrayObject() ],
		];
	}

	/**
	 * @dataProvider invalidParameterTypeProvider
	 * @covers \Wikimedia\Assert\ParameterTypeException
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
	 * @covers \Wikimedia\Assert\AssertionException
	 */
	public function testParameterType_catch() {
		$this->expectException( AssertionException::class );
		Assert::parameterType( 'string', 17, 'test' );
	}

	public function validParameterKeyTypeProvider() {
		return [
			[ 'integer', [] ],
			[ 'integer', [ 1 ] ],
			[ 'integer', [ 1 => 1 ] ],
			[ 'integer', [ 1.0 => 1 ] ],
			[ 'integer', [ '0' => 1 ] ],
			[ 'integer', [ false => 1 ] ],
			[ 'string', [] ],
			[ 'string', [ '' => 1 ] ],
			[ 'string', [ '0.0' => 1 ] ],
			[ 'string', [ 'string' => 1 ] ],
			[ 'string', [ null => 1 ] ],
		];
	}

	/**
	 * @dataProvider validParameterKeyTypeProvider
	 */
	public function testParameterKeyType_pass( $type, $value ) {
		Assert::parameterKeyType( $type, $value, 'test' );
		$this->addToAssertionCount( 1 );
	}

	public function invalidParameterKeyTypeProvider() {
		return [
			[ 'integer', [ 0, 'string' => 1 ] ],
			[ 'integer', [ 'string' => 0, 1 ] ],
			[ 'string', [ 0, 'string' => 1 ] ],
			[ 'string', [ 'string' => 0, 1 ] ],
		];
	}

	/**
	 * @dataProvider invalidParameterKeyTypeProvider
	 * @covers \Wikimedia\Assert\ParameterKeyTypeException
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
	 * @covers \Wikimedia\Assert\ParameterAssertionException
	 */
	public function testGivenUnsupportedType_ParameterKeyTypeFails() {
		$this->expectException( ParameterAssertionException::class );
		$this->expectExceptionMessage( 'Bad value for parameter type: must be "integer" or "string"' );
		Assert::parameterKeyType( 'integer|string', [], 'test' );
	}

	public function validParameterElementTypeProvider() {
		return [
			'empty' => [ 'string', [] ],
			'simple' => [ 'string', [ 'hello', 'world' ] ],
			'class' => [ 'RuntimeException', [ new RuntimeException() ] ],
			'multi' => [ 'string|array|Closure', [ [], function () {
			} ] ],
			'null' => [ 'integer|null', [ null, 3, null ] ],
		];
	}

	/**
	 * @dataProvider validParameterElementTypeProvider
	 */
	public function testParameterElementType_pass( $type, $value ) {
		Assert::parameterElementType( $type, $value, 'test' );
		$this->addToAssertionCount( 1 );
	}

	public function invalidParameterElementTypeProvider() {
		return [
			'simple' => [ 'string', [ 'hello', 5 ] ],
			'class' => [ 'RuntimeException', [ new LogicException() ] ],
			'multi' => [ 'string|array|Closure', [ [], function () {
			}, 5 ] ],
			'null' => [ 'integer|string', [ null, 3, null ] ],
		];
	}

	/**
	 * @dataProvider invalidParameterElementTypeProvider
	 * @covers \Wikimedia\Assert\ParameterElementTypeException
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
	 * @covers \Wikimedia\Assert\ParameterTypeException
	 */
	public function testParameterElementType_bad() {
		$this->expectException( ParameterTypeException::class );
		Assert::parameterElementType( 'string', 'foo', 'test' );
	}

	public function validNonEmptyStringProvider() {
		return [
			[ '0' ],
			[ '0.0' ],
			[ ' ' ],
			[ "\n" ],
			[ 'test' ],
		];
	}

	/**
	 * @dataProvider validNonEmptyStringProvider
	 */
	public function testNonEmptyString_pass( $value ) {
		Assert::nonEmptyString( $value, 'test' );
		$this->addToAssertionCount( 1 );
	}

	public function invalidNonEmptyStringProvider() {
		return [
			[ null ],
			[ false ],
			[ 0 ],
			[ 0.0 ],
			[ '' ],
		];
	}

	/**
	 * @dataProvider invalidNonEmptyStringProvider
	 * @covers \Wikimedia\Assert\ParameterTypeException
	 */
	public function testNonEmptyString_fail( $value ) {
		$this->expectException( ParameterTypeException::class );
		$this->expectExceptionMessage( 'Bad value for parameter test: must be a non-empty string' );
		Assert::nonEmptyString( $value, 'test' );
	}

	public function testInvariant_pass() {
		Assert::invariant( true, 'test' );
		$this->addToAssertionCount( 1 );
	}

	/**
	 * @covers \Wikimedia\Assert\InvariantException
	 */
	public function testInvariant_fail() {
		$this->expectException( InvariantException::class );
		Assert::invariant( false, 'test' );
	}

	public function testPostcondition_pass() {
		Assert::postcondition( true, 'test' );
		$this->addToAssertionCount( 1 );
	}

	/**
	 * @covers \Wikimedia\Assert\PostconditionException
	 */
	public function testPostcondition_fail() {
		$this->expectException( PostconditionException::class );
		Assert::postcondition( false, 'test' );
	}

}
