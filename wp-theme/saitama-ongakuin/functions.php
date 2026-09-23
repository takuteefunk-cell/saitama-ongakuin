<?php
/**
 * 埼玉音楽院テーマ
 *
 * ページ本文の中では、リンクと画像を次の書き方にしておくと自動で正しいURLに変わります。
 *   <a href="?sao_page=courses">  … 料金・コースページへのリンク（キーは sao_pages() を参照）
 *   <img src="sao-img/abe-takuya.jpg"> … テーマ同梱の画像
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'primary' => 'メインメニュー' ) );
} );

// 絵文字を画像に置き換える WordPress の機能を止める（ロゴの 🎵 などがそのまま表示されるように）
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// 固定ページで抜粋を使えるようにする（meta description に使う）
add_action( 'init', function () {
	add_post_type_support( 'page', 'excerpt' );
} );

add_action( 'wp_enqueue_scripts', function () {
	$v   = wp_get_theme()->get( 'Version' );
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'sao-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&family=Zen+Maru+Gothic:wght@500;700;900&display=swap', array(), null );
	wp_enqueue_style( 'sao-site', $uri . '/assets/site.css', array( 'sao-fonts' ), $v );
	wp_enqueue_style( 'sao-wp', $uri . '/assets/wp.css', array( 'sao-site' ), $v );
	wp_enqueue_script( 'sao-script', $uri . '/assets/script.js', array(), $v, true );
} );

/**
 * サイト内のページ一覧。slug で探し、見つからなければ既存ページの ID を使う。
 */
function sao_pages() {
	return array(
		'home'        => array( 'label' => 'ホーム', 'slug' => '', 'id' => 0 ),
		'about'       => array( 'label' => '教室について', 'slug' => 'about', 'id' => 33 ),
		'instructors' => array( 'label' => '講師紹介', 'slug' => 'instructors', 'id' => 584 ),
		'courses'     => array( 'label' => '料金・コース', 'slug' => 'courses', 'id' => 459 ),
		'events'      => array( 'label' => '演奏会・イベント', 'slug' => 'events', 'id' => 29 ),
		'commission'  => array( 'label' => '制作・演奏依頼', 'slug' => 'commission', 'id' => 31 ),
		'access'      => array( 'label' => 'アクセス', 'slug' => 'access', 'id' => 11 ),
		'contact'     => array( 'label' => 'お問い合わせ', 'slug' => 'contact', 'id' => 38 ),
	);
}

function sao_page_id( $key ) {
	$pages = sao_pages();
	if ( ! isset( $pages[ $key ] ) ) {
		return 0;
	}
	if ( 'home' === $key ) {
		return (int) get_option( 'page_on_front' );
	}
	$p = $pages[ $key ];
	if ( $p['slug'] ) {
		$found = get_page_by_path( $p['slug'] );
		if ( $found && 'publish' === $found->post_status ) {
			return (int) $found->ID;
		}
	}
	if ( $p['id'] && 'publish' === get_post_status( $p['id'] ) ) {
		return (int) $p['id'];
	}
	return 0;
}

function sao_url( $key ) {
	if ( 'home' === $key ) {
		return home_url( '/' );
	}
	$id = sao_page_id( $key );
	return $id ? get_permalink( $id ) : home_url( '/' );
}

function sao_is_current( $key ) {
	if ( 'home' === $key ) {
		return is_front_page();
	}
	$id = sao_page_id( $key );
	return $id && is_page( $id );
}

/**
 * ヘッダー・フッターのメニュー。外観 > メニューで「メインメニュー」が設定されていればそちらを使う。
 */
function sao_nav( $context = 'header' ) {
	if ( 'header' === $context && has_nav_menu( 'primary' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'items_wrap'     => '<ul>%3$s</ul>',
			'depth'          => 1,
		) );
		return;
	}
	echo '<ul>';
	foreach ( sao_pages() as $key => $p ) {
		if ( 'footer' === $context && 'home' === $key ) {
			continue;
		}
		if ( 'home' !== $key && ! sao_page_id( $key ) ) {
			continue;
		}
		$classes = array();
		if ( 'header' === $context ) {
			if ( 'contact' === $key ) {
				$classes[] = 'cta';
			}
			if ( sao_is_current( $key ) ) {
				$classes[] = 'active';
			}
		}
		printf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( sao_url( $key ) ),
			$classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '',
			esc_html( $p['label'] )
		);
	}
	echo '</ul>';
}

/**
 * 本文中の ?sao_page=xxx と sao-img/ を実際のURLに置き換える。
 */
add_filter( 'the_content', function ( $content ) {
	$content = preg_replace_callback(
		'/(["\'])\?sao_page=([a-z]+)(#[^"\']*)?\1/',
		function ( $m ) {
			return $m[1] . esc_url( sao_url( $m[2] ) ) . ( isset( $m[3] ) ? $m[3] : '' ) . $m[1];
		},
		$content
	);
	return str_replace( 'sao-img/', trailingslashit( get_template_directory_uri() ) . 'assets/images/', $content );
}, 20 );

// 置き換えが効かない場所（抜粋・ウィジェット等）から ?sao_page= に来た場合の保険
add_action( 'template_redirect', function () {
	if ( isset( $_GET['sao_page'] ) ) {
		$key = sanitize_key( wp_unslash( $_GET['sao_page'] ) );
		if ( sao_page_id( $key ) || 'home' === $key ) {
			wp_safe_redirect( sao_url( $key ), 301 );
			exit;
		}
	}
} );

/**
 * 本文がテーマ用のHTML（hero / page-hero を含む）ならそのまま出す。
 * 普通に書かれた記事やページは見出し付きの枠で包む。
 */
function sao_is_designed_content( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return false;
	}
	return (bool) preg_match( '/class="(page-)?hero[" ]/', $post->post_content );
}

// meta description と構造化データ
add_action( 'wp_head', function () {
	if ( is_singular() ) {
		$post = get_post();
		if ( $post && has_excerpt( $post ) ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( get_the_excerpt( $post ) ) ) );
		}
	}
	if ( is_front_page() ) {
		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'MusicSchool',
			'name'        => '埼玉音楽院',
			'url'         => home_url( '/' ),
			'telephone'   => '+81-49-251-6969',
			'address'     => array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => '鶴瀬西2-1-21',
				'addressLocality' => '富士見市',
				'addressRegion'   => '埼玉県',
				'postalCode'      => '354-0026',
				'addressCountry'  => 'JP',
			),
			'description' => '埼玉県富士見市鶴瀬の音楽教室。開校40年以上、地域に愛される音楽教室。ピアノ、ボーカル、ドラム、ギターなど幅広いコースをご用意。',
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	}
}, 5 );
