<?php
namespace Http;

use TotalCore\Http\Headers;

class HeadersTest extends \Codeception\TestCase\WPTestCase {

	public function testGetterMethod() {
		$headers = new Headers( [ 'USER_AGENT' => 'Google Chrome' ] );

		self::assertEquals( 'Google Chrome', $headers['USER_AGENT'] );

	}

	public function testSetterMethod() {
		$headers = new Headers();

		$headers['user_agent'] = 'Google Chrome';
		self::assertEquals( 'Google Chrome', $headers['USER_AGENT'] );

	}

	public function testStatusMethod() {
		$headers = new Headers( [], 404 );

		self::assertEquals( 404, $headers->status() );
	}

}