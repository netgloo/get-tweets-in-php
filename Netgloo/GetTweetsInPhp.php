<?php

namespace Netgloo;

if (!class_exists('TwitterAPIExchange')) {
  require_once(GET_TWEETS_IN_PHP__PLUGIN_DIR . 'TwitterAPIExchange.php');
}

require_once('TwitterTextFormatter.php');

/**
 * Example usage:
 *   
 *   use Netgloo\GetTweetsInPhp
 *   
 *   $configs = [
 *     'consumer_key' => 'CONSUMER_KEY', 
 *     'consumer_secret' => 'CONSUMER_SECRET', 
 *     'screen_name' => 'netglooweb',
 *     'count' => '5'
 *   ];
 * 
 *   $tweets = GetTweetsInPhp::get_tweets($configs);
 * 
 *   foreach ($tweets as $tweet) {
 *     echo '<p>' . $tweet->n_html_text . '</p>';
 *   }
 */
class GetTweetsInPhp {

  // --------------------------------------------------------------------------

  /**
   * Returns the latest tweets.
   * 
   * This function gets an array of configurations:
   * 
   *   $configs = [
   * 
   *     // Required
   *     'consumer_key' => '...',
   *     'consumer_secret' => '...',
   *     'screen_name' => '...',
   *   
   *     // Optional
   *     'count' => 20,
   *     'include_rts' => true,
   *     'show_retweeted_by' => true,
   *     'cache_enabled' => false,
   *     'cache_expiration' => 60 // (seconds)
   *   ];
   * 
   * Each tweet is an object as returned by Twitter APIs, with the following
   * additional properties:
   *   - n_html_text (String)
   *   - n_is_retweeted (Boolean)
   *   - n_has_media_photo (Boolean)
   *   - n_media_photo_urls (Array)
   *   - n_media_photo_url (String)
   */
  public static function get_tweets($configs) {

    // Check required configs
    if (!isset($configs)) {
      throw new \Exception('The $configs parameter is required.');
    }
    self::check_required($configs, 'consumer_key');
    self::check_required($configs, 'consumer_secret');
    self::check_required($configs, 'screen_name');

    // Set default values
    self::set_default($configs, 'count', 20);
    self::set_default($configs, 'include_rts', true);
    self::set_default($configs, 'show_retweeted_by', true);
    self::set_default($configs, 'cache_enabled', true);
    self::set_default($configs, 'cache_expiration', 60);

    // Get the api's options
    $api_opts = self::get_only($configs, [
      'screen_name', 
      'count', 
      'include_rts'
    ]);

    // Get text formatter options
    $show_retweeted_by = $configs['show_retweeted_by'];

    // Retrive the user_timeline (using the cache if enabled)
    $user_timeline = self::retrive_user_timeline(
      $configs['consumer_key'], 
      $configs['consumer_secret'], 
      $api_opts, 
      $configs['cache_enabled'], 
      intval($configs['cache_expiration'])
    );

    // Iterates over all the tweets and build the new array
    $tweets = [];
    foreach ($user_timeline as $user_tweet) {

      $tweet = $user_tweet;

      // Set the 'n_html_text' attribute
      $tweet->n_html_text = TwitterTextFormatter::format_text(
        $user_tweet, 
        $show_retweeted_by
      );

      // Set 'n_is_retweeted'
      $tweet->n_is_retweeted = isset($tweet->retweeted_status);

      // Set the 'n_media_photo_urls' attribute
      $tweet->n_media_photo_urls = [];
      if (isset($user_tweet->entities->media)) {
        foreach ($user_tweet->entities->media as $media) {
          if ($media->type != 'photo') {
            continue;
          }
          $tweet->n_media_photo_urls[] = $media->media_url;
        }
      }

      // Set the 'n_media_photo_url' attribute
      if (!empty($tweet->n_media_photo_urls)) {
        $tweet->n_has_media_photo = true;
        $tweet->n_media_photo_url = $tweet->n_media_photo_urls[0];
      }
      else {
        $tweet->n_has_media_photo = false;
        $tweet->n_media_photo_url = null;
      }

      $tweets[] = $tweet;
    
    } // foreach $user_tweet

    return $tweets;
  }

  // --------------------------------------------------------------------------

  // ==========================================================================
  // PRIVATE PROPERTIES

  // --------------------------------------------------------------------------

  // Cache
  const CACHE_TRANSIENT = 'GETTWEETSINPHP_1_0';

  // --------------------------------------------------------------------------

  // ==========================================================================
  // PRIVATE METHODS

  // --------------------------------------------------------------------------

  /**
   * Check if the given key is setted in the array, if not throws an Exception.
   */
  private static function check_required($array, $key) {
    if (!isset($array[$key])) {
      throw new \Exception("The field $key is required");
    }
    return;
  }

  // --------------------------------------------------------------------------

  /**
   * Retrive the Twitter timeline using the TwitterAPIExchange object or 
   * looking at the cache (if it is enabled).
   */
  private static function retrive_user_timeline(
    $consumer_key, 
    $consumer_secret, 
    $api_opts, 
    $cache_enabled, 
    $cache_expiration
  ) {

    // Get settings for the TwitterAPIExchange 
    $settings = [
      'consumer_key' => $consumer_key,
      'consumer_secret' => $consumer_secret,
      'oauth_access_token' => '',
      'oauth_access_token_secret' => ''
    ];

    // Initialize the TwitterAPIExchange object
    $twitter_api_exchange = new \TwitterAPIExchange($settings);

    // Set the cache transient name
    $transient_name = self::CACHE_TRANSIENT . '__' . $api_opts['screen_name'];

    // Retrive the $user_timeline
    $user_timeline = null;

    // Look at the cache
    if ($cache_enabled) {

      $user_timeline = get_transient($transient_name);

      if ($user_timeline !== false) {
        return $user_timeline;
      }

    } // if

    // If there is no the cached values, get the latest tweets (the user's 
    // timeline) using the TwitterAPIExchange
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $requestMethod = 'GET';
    $get_fields = http_build_query($api_opts);

    $user_timeline = $twitter_api_exchange
      ->setGetfield('?' . $get_fields)
      ->buildOauth($url, $requestMethod)
      ->performRequest();

    $user_timeline = json_decode($user_timeline);

    // Save in the cache if enabled
    if ($cache_enabled) {
      set_transient($transient_name, $user_timeline, $cache_expiration);
    }

    // Clear the cache if disabled
    else {
      delete_transient($transient_name);
    }

    return $user_timeline;
  }

  // --------------------------------------------------------------------------

  /**
   * Set a default value for the given key.
   */
  private static function set_default(&$array, $key, $default) {
    if (!isset($array[$key])) {
      $array[$key] = $default;
    }
    return;
  }

  // --------------------------------------------------------------------------

  /**
   * Get only the given set of keys.
   */
  private static function get_only($array, $keys) {
    $res = [];
    foreach ($keys as $key) {
      if (isset($array[$key])) {
        $res[$key] = $array[$key];
      }
    }
    return $res;
  }

  // --------------------------------------------------------------------------

} // class
