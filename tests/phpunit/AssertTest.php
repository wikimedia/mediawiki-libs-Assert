<?php
namespace Wikimedia\Assert\Test;

use PHPUnit_Framework_TestCase;
use Wikimedia\Assert\Assert;

/**
 * @covers Wikimedia\Assert\Assert
 *
 * @license MIT
 * @author Daniel Kinzler
 * @copyright Wikimedia Deutschland e.V.
 */

class AssertTest extends PHPUnit_Framework_TestCase {

	public function testPrecondition_true() {
		Assert::precondition( true, "test" );
	}

	public function testPrecondition_false() {
		$this->setExpectedException( 'RuntimeException' );
		Assert::precondition( false, "test" );
	}

	public function testArgument_true() {
		Assert::argument( true, "foo", "test" );
	}

	public function testArgument_false() {
		$this->setExpectedException( 'InvalidArgumentException' );
		Assert::argument( false, "foo", "test" );
	}

	public function testInvariant_true() {
		Assert::invariant( true, "test" );
	}

	public function testInvariant_false() {
		$this->setExpectedException( 'LogicException' );
		Assert::invariant( false, "test" );
	}

	public function testPostcondition_true() {
		Assert::postcondition( true, "test" );
	}

	public function testPostcondition_false() {
		$this->setExpectedException( 'LogicException' );
		Assert::postcondition( false, "test" );
	}

}