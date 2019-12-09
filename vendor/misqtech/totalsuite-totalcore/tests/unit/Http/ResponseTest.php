<?php
namespace Http;

use TotalCore\Http\Response;

class ResponseTest extends \Codeception\TestCase\WPTestCase {

	public function testSendMethod() {
		$response = new Response( 'Hello world!', 200 );

		ob_start();
		$response->send();
		$output = ob_get_clean();

		self::assertEquals( 'Hello world!', $output );
	}

}