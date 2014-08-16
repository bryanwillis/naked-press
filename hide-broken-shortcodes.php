<?php
if ( ! class_exists( 'HideBrokenShortcodes' ) ) :

class HideBrokenShortcodes {

	public static function version() {
		return '1.6.1';
	}

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_filters' ) );
	}

	public static function register_filters() {
		$filters = (array) apply_filters( 'hide_broken_shortcodes_filters', array( 'the_content', 'widget_text' ) );
		foreach ( $filters as $filter )
			add_filter( $filter, array( __CLASS__, 'do_shortcode' ), 1001 );
	}

	public static function do_shortcode( $content ) {
		return preg_replace_callback( '/' . self::get_shortcode_regex() . '/s', array( __CLASS__, 'do_shortcode_tag' ), $content );
	}

	public static function get_shortcode_regex() {
		$tagregexp = '[a-zA-Z_\-][0-9a-zA-Z_\-\+]{2,}';

		return
			  '\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)"                     // 2: Shortcode name
			. '(?![\\w-])'                       // Not followed by word character or hyphen
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			.     '(?:'
			.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
			.     ')*?'
			. ')'
			. '(?:'
			.     '(\\/)'                        // 4: Self closing tag ...
			.     '\\]'                          // ... and closing bracket
			. '|'
			.     '\\]'                          // Closing bracket
			.     '(?:'
			.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			.             '[^\\[]*+'             // Not an opening bracket
			.             '(?:'
			.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			.                 '[^\\[]*+'         // Not an opening bracket
			.             ')*+'
			.         ')'
			.         '\\[\\/\\2\\]'             // Closing shortcode tag
			.     ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}

	public static function do_shortcode_tag( $m ) {

		if ( $m[1] == '[' && $m[6] == ']' )
			return substr( $m[0], 1, -1 );

		$default_display = ( isset( $m[5] ) ? self::do_shortcode( $m[5] ) : '' );

		return apply_filters( 'hide_broken_shortcode', $default_display, $m[2], $m );
	}

} 

HideBrokenShortcodes::init();

endif;
