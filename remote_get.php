<?php

// 為替情報を取得する関数
// まずは、自分のURL。
function get_crypt_data_from_api() {

	$url = get_option( 'crypt_url' );
	if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
		error_log( 'API接続先が指定されていません' );

		return false;
	}

	// キャッシュデータがあれば、それを返して終了
	$data = get_transient( 'crypt_data' );
	if ( $data ) {
		return $data;
	}

	$response = wp_remote_get( $url ); // 指定したURLのデータを取得
	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		error_log( 'error' . $error_message );
		$data = false;
	} else if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
		error_log( 'response code: ' . wp_remote_retrieve_response_code( $response ) );
		$data = false;
	} else {
		$json            = wp_remote_retrieve_body( $response ); // データの本体を取得
		$data            = json_decode( $json );
		$crypt_cachetime = get_option( 'crypt_cachetime', 60 );
		set_transient( 'crypt_data', $data, $crypt_cachetime * MINUTE_IN_SECONDS );
	}

	return $data;
}

