<?php

function crypt_price_register_block() {

	$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php' );

	wp_register_script(
		'crypt-price',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version']
	);


	register_block_type( 'crypt/price-block', array(
		'editor_script'   => 'crypt-price',
		'render_callback' => 'crypt_usdjpy',
		'attributes'      => [
			'contentBefore' => [
				'type' => 'string',
			],
			'contentAfter'  => [
				'type' => 'string',
			],
			'option'        => [
				'type'    => 'string',
				'default' => 'last'
			],
			'floatnum'      => [
				'type'    => 'integer',
				'default' => 2
			],
		],
	) );
}

add_action( 'init', 'crypt_price_register_block' );

function crypt_usdjpy( $attributes ) {
	$price = get_crypt_data_from_api();

	if ( $attributes['option'] == 'last' ) {
		$priceval = $price->last;
	} else if ( $attributes['option'] == 'vwap' ) {
		$priceval = $price->vwap;
	}

	$options  = [
		'min_range' => 0,
		'max_range' => 9,
		'default'   => 2,
	];
	$floatnum = filter_var( $attributes['floatnum'], FILTER_VALIDATE_INT, $options );

	$output = $attributes['contentBefore'] . number_format( $priceval, $floatnum ) . $attributes['contentAfter'];

	return '<div>' . esc_html( $output ) . '</div>';
}
