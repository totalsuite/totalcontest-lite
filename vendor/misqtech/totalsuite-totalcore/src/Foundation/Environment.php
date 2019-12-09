<?php

namespace TotalContestVendors\TotalCore\Foundation;

use TotalContestVendors\TotalCore\Helpers\Arrays;

/**
 * Class Environment
 * @package TotalContestVendors\TotalCore\Foundation
 */
class Environment implements \TotalContestVendors\TotalCore\Contracts\Foundation\Environment {
	/**
	 * @var array $items
	 */
	protected $items;

	/**
	 * Environment constructor.
	 *
	 * @param $items
	 */
	public function __construct( $items ) {
		$this->items = is_array( $items ) ? $items : [];
	}

	/**
	 * Get items as array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->items;
	}

	/**
	 * @param mixed $offset
	 *
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return (bool) $this->get( $offset );
	}

	/**
	 * Get item.
	 *
	 * @param      $key
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Arrays::getDotNotation( $this->items, $key, $default );
	}

	/**
	 * @param mixed $offset
	 *
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}

	/**
	 * Set item.
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set( $key, $value ) {
		return Arrays::setDotNotation( $this->items, $key, $value );
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset( $offset ) {
		$this->set( $offset, null );
	}

	/**
	 * @return mixed|string|void
	 */
	public function serialize() {
		return json_encode( $this->items );
	}

	/**
	 * @param string $serialized
	 */
	public function unserialize( $serialized ) {
		$this->items = json_decode( $serialized, true );
	}

	/**
	 * @return array|mixed
	 */
	public function jsonSerialize() {
		return $this->items;
	}

	/**
	 * @param $key
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key, null );
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __set( $key, $value ) {
		return $this->set( $key, $value );
	}

	/**
	 * @param $key
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return (bool) $this->get( $key );
	}
}