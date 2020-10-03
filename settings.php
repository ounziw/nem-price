<?php

// 管理画面の設定
function crypt_menu() {
	add_options_page(
		'仮想通貨設定', // ページのタイトル
		'仮想通貨設定', // メニューのタイトル
		'manage_options', // このページを操作する権限
		'crypt', // ページ名(英数字でつける)
		'crypt_page_form' // 表示内容を決める関数。自分で関数を作る
	);
}

add_action( 'admin_menu', 'crypt_menu' );

// ページ全体
function crypt_page_form() {
	?>
    <div class="wrap">
        <form action="options.php" method="post">
			<?php do_settings_sections( 'crypt' ); // ページ名 ?>
			<?php settings_fields( 'crypt-group' ); // グループ名 ?>
			<?php submit_button(); ?>
        </form>
    </div>
	<?php
}


// セクション
function crypt_section_settings() {
	add_settings_section(
		'crypt_section', // セクション名
		'URL設定', // タイトル
		'crypt_section_message', // コールバック関数。この関数の実行結果が出力される
		'crypt' // このセクションを表示するページ名。do_settings_sectionsで設定
	);
}

add_action( 'admin_init', 'crypt_section_settings' );

function crypt_section_message() {
	echo '<p>接続先URL、キャッシュ時間の設定<br>https://api.zaif.jp/api/1/ticker/xem_jpy</p>';
}


// url入力フィールド
function crypt_settings_field() {

	// 表示するHTML、表示場所などの設定
	add_settings_field(
		'crypt_url', // フィールド名
		'URL', // タイトル
		'crypt_url_form', // コールバック関数。この関数の実行結果が出力される
		'crypt', // このフィールドを表示するページ名。do_settings_sectionsで設定
		'crypt_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
	);

	// 更新の設定
	register_setting(
		'crypt-group', // グループ名。settings_fieldsで設定
		'crypt_url', // オプション名
		'crypt_url_check' // 入力データをチェックする関数。
	);
}

add_action( 'admin_init', 'crypt_settings_field', 15 );

// フォーム項目を表示する
function crypt_url_form() {
	echo '<input name="crypt_url" id="crypt_url" type="text" value="';
	form_option( 'crypt_url' );
	echo '" size="120" />';
}

// 入力urlのチェック
function crypt_url_check( $input ) {
	if ( filter_var( $input, FILTER_VALIDATE_URL ) !== false ) { // URLかどうかを判定する
		return $input; // 入力データがOKだったら、データをそのまま返す
	} else {
		add_settings_error( // エラーを設定する関数
			'crypt', // ページ名
			'invalid_url', // html識別用のID
			'URLを正しく指定してください。' // エラーメッセージ
		);

		return get_option( 'crypt_url' ); // 入力データがダメだったら、元のデータのまま
	}
}

define( 'CRYPT_CACHE_MIN', 10 );
define( 'CRYPT_CACHE_MAX', 1440 );

// キャッシュ時間入力フィールド
function CRYPT_CACHE_settings_field() {

	add_settings_field(
		'cache', // フィールド名
		'キャッシュ時間(' . CRYPT_CACHE_MIN . ' - ' . CRYPT_CACHE_MAX . '分)', // タイトル
		'CRYPT_CACHE_callback_function', // コールバック関数。この関数の実行結果が出力される
		'crypt', // このフィールドを表示するページ名。do_settings_sectionsで設定
		'crypt_section' // このフィールドを表示するセクション名。add_settings_sectionで設定
	);

	register_setting(
		'crypt-group', // グループ名。settings_fieldsで設定
		'CRYPT_CACHEtime', // オプション名
		'CRYPT_CACHE_check' // 入力値をサニタイズする関数
	);
}

add_action( 'admin_init', 'CRYPT_CACHE_settings_field', 25 );


// 入力値「キャッシュ」を検証する
// 必要に応じてエラーメッセージを出す
function CRYPT_CACHE_check( $input ) {
	$filter_option = array(
		'options' => array(
			'min_range' => CRYPT_CACHE_MIN,
			'max_range' => CRYPT_CACHE_MAX,
		),
	);
	if ( filter_var( $input, FILTER_VALIDATE_INT, $filter_option ) ) {
		return $input;
	} else {
		add_settings_error(
			'CRYPT_CACHE_settings',
			'invalid_num',
			'キャッシュ時間: ' . CRYPT_CACHE_MIN . ' 以上 ' . CRYPT_CACHE_MAX . ' 以下の数字を指定してください。',
			'error'
		);

		return intval( get_option( 'CRYPT_CACHEtime' ) );
	}
}

// フォーム項目を表示する
function CRYPT_CACHE_callback_function() {
	echo '<input name="CRYPT_CACHEtime" id="CRYPT_CACHEtime" type="number" min="' . CRYPT_CACHE_MIN . '" max="' . CRYPT_CACHE_MAX . '" value="';
	form_option( 'CRYPT_CACHEtime' );
	echo '" />';
}