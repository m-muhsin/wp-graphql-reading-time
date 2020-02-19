<?php
/**
 * @wordpress-plugin
 * Plugin Name:       WP GraphQL Reading Time
 * Plugin URI:        https://github.com/m-muhsin/wp-graphql-reading-time
 * Description:       Gives the post reading time in minutes as a GraphQL Field.
 * Version:           0.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Muhammad Muhsin
 * Author URI:        https://muhammad.dev
 * License:           GNU General Public License v2.0 / MIT License
 * Text Domain:       wp-graphql-reading-time
 * Domain Path:       /languages
 */

function graphql_register_types() {
	register_graphql_field(
		'post',
		'readingTime',
		[
			'type'        => 'String',
			'description' => __( 'Post reading time', 'wp-graphql' ),
			'resolve'     => function( $post ) {
				$reading_time = wpgql_rt_calculate_reading_time( $post->ID );
				return $reading_time;
			},
		]
	);
}
add_action( 'graphql_register_types', 'graphql_register_types' );

function wpgql_rt_calculate_reading_time( $wpgql_rt_post_id ) {

	$wpgql_rt_content = get_post_field( 'post_content', $wpgql_rt_post_id );
	$wpgql_rt_content = wp_strip_all_tags( $wpgql_rt_content );
	$word_count       = count( preg_split( '/\s+/', $wpgql_rt_content ) );

	// Hardcoding words / min value.
	$wpm = 300;

	$reading_time = $word_count / $wpm;

	// If the reading time is 0 then return it as < 1 instead of 0.
	if ( 1 > $reading_time ) {
		$reading_time = __( '< 1', 'reading-time-wp' );
	} else {
		$reading_time = ceil( $reading_time );
	}

	return $reading_time;

}
