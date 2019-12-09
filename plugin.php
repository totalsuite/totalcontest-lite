<?php
/*
 * Plugin Name: TotalContest – Lite
 * Plugin URI: https://totalsuite.net/products/totalcontest/
 * Description: Yet another powerful contest plugin for WordPress.
 * Version: 2.0.2
 * Author: TotalSuite
 * Author URI: https://totalsuite.net/
 * Text Domain: totalcontest
 * Domain Path: languages
 *
 * @package TotalContest
 * @category Core
 * @author TotalSuite
 * @version 2.0.2
 */

// Root plugin file name
define( 'TOTALCONTEST_ROOT', __FILE__ );

// TotalContest environment
$environment = require dirname( __FILE__ ) . '/env.php';

// Include plugin setup
include_once dirname( __FILE__ ) . '/setup.php';

// Setup
new TotalContestSetup( $environment );

// Oh yeah, we're up and running!
