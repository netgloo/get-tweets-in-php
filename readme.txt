=== Get Tweets in PHP ===
Contributors: netgloo, azanelli, aboutnick
Tags: twitter, tweet, tweets, latest-tweets, developers
Requires at least: 4.0
Tested up to: 4.5
Stable tag: 1.2
License: GPLv2 or later

Get latest tweets from a Twitter account with a couple of lines of PHP, and do anything you want with them.

== Description ==

This plugin will add the PHP class `GetTweetsInPhp`. You can use this class as described below for retrieving **latest tweets** from a Twitter account, then handle the tweets as you want in your PHP code.

**Note**: you should create a Twitter app before using this plugin. You can do it from here: [http://apps.twitter.com](http://apps.twitter.com).


= Features =

* Get latest N tweets from a Twitter account.
* Get the tweet's text formatted as HTML (with links for each entities).
* Cache support.
* Made for developers.
* Really light and simple.
* Works with the v1.1 Twitter API.
* Trivial install/uninstall (only add/remove the plugin's files).
* No any data will be permanently stored in your database (only transient data 
  if the cache is enabled).
* Proudly coded by [Netgloo](http://netgloo.com/en).


= Example usage =

Get and show latest tweets from [@netglooweb](http://twitter.com/netglooweb):
    
    // Set configurations
    $configs = [
      // Set here tokens from your Twitter's app
      'consumer_key' => 'CONSUMER_KEY', 
      'consumer_secret' => 'CONSUMER_SECRET',

      // The Twitter account name
      'screen_name' => 'netglooweb',

      // The number of tweets
      'count' => 5,
    ];

    // Get latest tweets using the function get_tweets
    $tweets = \Netgloo\GetTweetsInPhp::get_tweets($configs);

    // ...

    // For each tweet show the HTML text and the attached image
    foreach ($tweets as $tweet) {

      echo "<p>";
      echo $tweet->n_html_text;

      if ($tweet->n_has_media_photo) {
        echo "<img src='{$tweet->n_media_photo_url}' width='100%' />";
      }

      echo "</p>";

    }

    // ...
    
That's all! Have fun!


= Configurations =

The `get_tweets()` function takes an array of configurations:

    $configs = [

      // --- Required ---
      
      // The tokens from your Twitter's app
      'consumer_key' => '...',
      'consumer_secret' => '...',

      // The Twitter account name
      'screen_name' => '...',


      // --- Optional ---

      // The number of tweets
      'count' => 20,

      // Include also the retweets
      'include_rts' => true,

      // In the HTML text will be showed "Retweeted by ..." if the tweet
      // is a retweet
      'show_retweeted_by' => true,

      // Enable the cache
      // It is recommended to activate the cache, when you put live 
      // your website, in order to avoid to reach the Twitter's api rate
      // limit of 300 requests / 15-min.
      'cache_enabled' => false,

      // Cache expiration (in seconds)
      // Increase the value to optimize the website's speed, decrease
      // the value if you want a more real-time behaviour (but not
      // less than 4 seconds to avoid to reach the rate limit).
      'cache_expiration' => 60,

      // Templates

      // Retweeted by text template
      'retweeted_by_template' => 
        '<em> Retweeted by {{user_name}}</em>',
  
      // Hash tag link template
      'hashtag_link_template' => 
        '<a href="{{hashtag_link}}" rel="nofollow" target="_blank">' .
        '#{{hashtag_text}}</a>',
  
      // Url link template
      'url_link_template' => 
        '<a href="{{url_link}}" rel="nofollow" target="_blank" ' .
        'title="{{url_title}}">{{url_text}}</a>',
  
      // User mention link template
      'user_mention_link_template' => 
        '<a href="{{user_mention_link}}" rel="nofollow" target="_blank" ' .
        'title="{{user_mention_title}}">@{{user_mention_text}}</a>',
  
      // Media link template
      'media_link_template' => 
        '<a href="{{media_link}}" rel="nofollow" target="_blank" ' .
        'title="{{media_title}}">{{media_text}}</a>'

    ];

    $tweets = \Netgloo\GetTweetsInPhp::get_tweets($configs);


= Returned values =

The `get_tweets()` function will return an Array of tweets. On each tweet object are available these properties:

* `n_html_text` (String) The tweet text formatted as HTML, with links on each entities.
* `n_is_retweeted` (Boolean) True if the curret tweet is a retweet.
* `n_has_media_photo` (Boolean) True if the current tweet has an attached photo.
* `n_media_photo_url` (String) The url of the tweet's attached photo.
* `n_media_photo_urls` (Array) If the tweet has more than one attached photos this properties contains all the urls.

Other available properties are those returned from the [user_timeline Twitter's API](https://dev.twitter.com/rest/reference/get/statuses/user_timeline).
These are some useful ones:

* `created_at`
* `retweet_count`
* `user->name`
* `user->screen_name`
* `user->profile_image_url`

If the properties `n_is_retweeted` is true the current tweet is a "re-tweet" and the `retweeted_status` object is available:

* `retweeted_status->user->name`
* `retweeted_status->user->screen_name`
* `retweeted_status->retweet_count`

**Example**

This code use some of the above properties:

    // ...

    $tweets = \Netgloo\GetTweetsInPhp::get_tweets($configs);

    foreach ($tweets as $tweet) {
      echo $tweet->created_at . "<br/>";
      echo $tweet->n_html_text . "<br/>";
      if ($tweet->n_has_media_photo) {
        echo $tweet->n_media_photo_url  . "<br/>";
      }
    }

    // ...


= Limitations =

Since we rely on the Twitter's `user_timeline` API, you should read the following docs for taking in account any API's limitation:

* https://dev.twitter.com/rest/reference/get/statuses/user_timeline
* https://dev.twitter.com/rest/public/timelines


= Contributing =

For patches, bug reports, suggestions, requests for features there is a Git repository on GitHub here:
https://github.com/netgloo/get-tweets-in-php


== Installation == 

= Requirements =

To work this plugin, following component need to be installed in your server.

* PHP version 5.3 or higher
* cURL
* WordPress 4.2.2 or higher

= Install =

Put the plugin to your Wordpress' plugins folder and activate it from the Admin Backend.

= Uninstall =

Just delete the plugin from Wordpress.


== Frequently Asked Questions ==

= Why the "n" before your custom tweet's properties? =

Our custom properties (i.e. properties not from the Twitter's API) are prefixed with an "n". The "n" is the first character in "Netgloo" ;).


== Changelog ==

= 1.2 =
* Custom HTML templates for links and "retweeted by" text.
* New hashtag link: https://twitter.com/hashtag.
* Get media urls with https.
* Cache disabled by default (as in the documentation).

= 1.1 =
* Bug fix setting transient cache name.

= 1.0 =
* First release.
