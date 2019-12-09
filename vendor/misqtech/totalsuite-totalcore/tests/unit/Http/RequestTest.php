<?php

namespace Http;

use TotalCore\Http\Request;

class RequestTest extends \Codeception\TestCase\WPTestCase {

	public function testMethodMethod() {
		$request = new Request( '', 'POST' );

		static::assertEquals( 'POST', $request->method() );
	}

	public function testUriMethod() {
		$request = new Request( '/test.php' );

		static::assertEquals( '/test.php', $request->uri() );
	}

	public function testHeaderMethod() {
		$request = new Request( '', 'GET', [], [], [], [], [ 'HTTP_USER_AGENT' => 'Google Chrome' ] );

		static::assertEquals( 'Google Chrome', $request->header( 'USER_AGENT' ) );
	}

	public function testServerMethod() {
		$request = new Request( '', 'GET', [], [], [], [], [ 'HTTP_USER_AGENT' => 'Google Chrome' ] );

		static::assertEquals( 'Google Chrome', $request->server( 'HTTP_USER_AGENT' ) );
	}

	public function testQueryMethod() {
		$request = new Request( '', 'GET', [ 'key' => 'value' ] );

		static::assertEquals( 'value', $request->query( 'key' ) );
	}

	public function testPostMethod() {
		$request = new Request( '', 'POST', [], [ 'key' => 'value' ] );

		static::assertEquals( 'value', $request->post( 'key' ) );
	}

	public function testCookieMethod() {
		$request = new Request( '', 'POST', [], [], [], [ 'cookie' => 'yummy' ] );

		static::assertEquals( 'yummy', $request->cookie( 'cookie' ) );
	}

	public function testFileMethod() {
		$request = new Request( '', 'POST', [], [], [ 'file' => [ 'name' => 'file.ext', 'type' => 'image/png', 'size' => 1, 'tmp_name' => 'php://temp/file.ext', 'error' => UPLOAD_ERR_OK ] ], [] );

		static::assertInstanceOf( '\TotalCore\Http\File', $request->file( 'file' ) );
		static::assertEquals( 'file.ext', $request->file( 'file' )->getFilename() );
	}

	public function testFilesMethod() {
		$request = new Request( '', 'POST', [], [], [ 'file' => [ 'name' => 'file.ext', 'type' => 'image/png', 'size' => 1, 'tmp_name' => 'php://temp/file.ext', 'error' => UPLOAD_ERR_OK ] ], [] );

		static::assertNotEmpty( $request->files() );
	}

	public function testRequestMethod() {
		$request = new Request( '', 'POST', [ 'key' => 'query' ], [ 'key' => 'post' ] );
		static::assertEquals( 'query', $request->request( 'key' ) );

		$request = new Request( '', 'POST', [], [ 'key' => 'post' ] );
		static::assertEquals( 'post', $request->request( 'key' ) );
	}

	public function testGetterMethod() {
		$request = new Request( '', 'POST', [ 'key' => 'query' ], [] );

		static::assertEquals( 'query', $request['key'] );
	}

	public function testSetterMethod() {
		$request = new Request( '', 'POST', [], [] );

		$request['key'] = 'query';
		static::assertEquals( 'query', $request['key'] );
	}

}