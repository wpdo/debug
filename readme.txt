=== Debug ===
Contributors: WPDO
Tags: debug
Requires at least: 4.0
Tested up to: 4.9.7
Stable tag: 2.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

An easy WordPress debug tool for plugin/theme developers.

== Description ==

Usage:

```
/**
 * Log a variable
 * @param  string|array $var
 */
defined( 'debug' ) && debug( $var ) ;

// Log only when advanced log mode is ON
defined( 'debug' ) && debug2( $var ) ;

// Log variable and also backtrace up to 4 levels
defined( 'debug' ) && debug( $var, 4 ) ;
```

== LSCWP Resources ==
* [Ask a question on WordPress forum](https://wordpress.org/support/plugin/debug/).
* [Help translate](https://translate.wordpress.org/projects/wp-plugins/debug).
* [GitHub repo](https://github.com/wpdo/debug).

== Installation ==

1. Install debug.
2. Go to `debug` -> `Setting`, turn on debug.
3. If you installed any plugin/theme which support debug, you will see them by:
    View your debug log by `debug` -> `View Logs`,
    Or SSH to your server WordPress path, run `tail -f wp-content/debug.log`.


== Frequently Asked Questions ==

= Is the Plugin for WordPress free? =

Yes, debug will always be free and open source.

== Changelog ==

= 1.0 - Jul 10 2018 =
* Initial Release.
