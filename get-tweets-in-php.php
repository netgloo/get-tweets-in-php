<?php
/**
 * Plugin Name: Get Tweets in PHP
 * Author: Netgloo
 * Version: 1.1
 * Author URI: http://netgloo.com
 * Description: Get tweets from a Twitter account with a couple of lines of PHP, and do anything you want with them.
 * License: GPLv2 or later
 */

/*  
Copyright 2016  Netgloo  (email : info@netgloo.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'GET_TWEETS_IN_PHP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( GET_TWEETS_IN_PHP__PLUGIN_DIR . 'Netgloo/GetTweetsInPhp.php' );
