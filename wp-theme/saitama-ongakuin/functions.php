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
		'home'        => array(
			'label' => 'ホーム', 'slug' => '', 'id' => 0,
			'desc'  => '埼玉県富士見市鶴瀬、東武東上線鶴瀬駅から徒歩8分。開校40年以上、地域に愛される音楽教室・埼玉音楽院。ピアノ、ボーカル、ドラム、ギター、ベース、管楽器、ハンドパン、幼児科など幅広いコースをご用意。無料見学・相談受付中。',
		),
		'about'       => array(
			'label' => '教室について', 'slug' => 'about', 'id' => 33,
			'desc'  => '埼玉県富士見市鶴瀬の音楽教室・埼玉音楽院について。開校40年以上、初心者から音大受験まで、プロ講師がマンツーマンで指導します。発表会やバンドフェスティバルも開催。',
		),
		'instructors' => array(
			'label' => '講師紹介', 'slug' => 'instructors', 'id' => 584,
			'desc'  => '埼玉音楽院（富士見市鶴瀬）の講師紹介。ピアノ、ドラム、ギター、ベース、トロンボーン、フルート、ハンドパンなど、各分野の専門講師が丁寧に指導します。',
		),
		'courses'     => array(
			'label' => '料金・コース', 'slug' => 'courses', 'id' => 459,
			'desc'  => '埼玉音楽院の料金・コース案内。月謝はピアノ・作曲・音楽理論・幼児科が7,000円、ボーカル・ギター・ベース・ドラム・管楽器・ハンドパンなどが11,000円（税込）。富士見市鶴瀬の音楽教室。',
		),
		'events'      => array(
			'label' => '演奏会・イベント', 'slug' => 'events', 'id' => 29,
			'desc'  => '埼玉音楽院（富士見市鶴瀬）の演奏会・イベント情報。生徒の発表会「楽院祭」や、世代を超えて楽しめるBAND FESTIVALなど。',
		),
		'commission'  => array(
			'label' => '制作・演奏依頼', 'slug' => 'commission', 'id' => 31,
			'desc'  => '埼玉音楽院では楽曲制作・編曲や出張演奏のご依頼を承っております。お祝い事、学校・施設イベント、企業式典など幅広く対応。',
		),
		'access'      => array(
			'label' => 'アクセス', 'slug' => 'access', 'id' => 11,
			'desc'  => '埼玉音楽院へのアクセス。〒354-0026 埼玉県富士見市鶴瀬西2-1-21。東武東上線鶴瀬駅から徒歩8分。TEL: 049-251-6969',
		),
		'contact'     => array(
			'label' => 'お問い合わせ', 'slug' => 'contact', 'id' => 38,
			'desc'  => '埼玉音楽院へのお問い合わせ。無料見学・体験レッスンのお申し込み、演奏・制作のご依頼はお電話またはフォームからお気軽にどうぞ。',
		),
	);
}

/**
 * 表示中のページが sao_pages() のどれにあたるか。該当しなければ空文字。
 */
function sao_current_key() {
	foreach ( array_keys( sao_pages() ) as $key ) {
		if ( sao_is_current( $key ) ) {
			return $key;
		}
	}
	return '';
}

/**
 * 検索結果・SNS 用の説明文。ページの「抜粋」が入っていればそれを優先する。
 */
function sao_description() {
	if ( is_singular() && has_excerpt() ) {
		return wp_strip_all_tags( get_the_excerpt() );
	}
	$key   = sao_current_key();
	$pages = sao_pages();
	if ( $key ) {
		return $pages[ $key ]['desc'];
	}
	return is_singular() ? wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', get_queried_object_id() ) ), 60, '…' ) : $pages['home']['desc'];
}

function sao_og_image() {
	return get_template_directory_uri() . '/assets/images/og-image.jpg';
}

// タイトル: 「講師紹介 | 埼玉音楽院（富士見市鶴瀬の音楽教室）」の形にする
add_filter( 'document_title_separator', function () {
	return '|';
} );
add_filter( 'document_title_parts', function ( $parts ) {
	if ( is_front_page() ) {
		return array( 'title' => '埼玉音楽院', 'tagline' => '富士見市鶴瀬の音楽教室（ピアノ・ボーカル・ドラム・ギター）' );
	}
	$parts['site'] = '埼玉音楽院（富士見市鶴瀬の音楽教室）';
	return $parts;
} );

// Jetpack が出す OGP を、説明文とシェア用画像つきに差し替える
add_filter( 'jetpack_open_graph_tags', function ( $tags ) {
	$tags['og:description'] = sao_description();
	$tags['og:image']       = sao_og_image();
	$tags['og:image:width'] = 1200;
	$tags['og:image:height'] = 630;
	$tags['og:image:alt']   = '埼玉音楽院｜富士見市鶴瀬の音楽教室';
	$tags['twitter:card']   = 'summary_large_image';
	if ( is_front_page() ) {
		$tags['og:title'] = '埼玉音楽院｜富士見市鶴瀬の音楽教室';
	}
	return $tags;
} );

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
	$desc = sao_description();
	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	// Jetpack の OGP が無効なときは自前で出す
	if ( ! has_action( 'wp_head', 'jetpack_og_tags' ) ) {
		$og = array(
			'og:type'        => is_front_page() ? 'website' : 'article',
			'og:site_name'   => '埼玉音楽院',
			'og:locale'      => 'ja_JP',
			'og:title'       => is_front_page() ? '埼玉音楽院｜富士見市鶴瀬の音楽教室' : wp_get_document_title(),
			'og:description' => $desc,
			'og:url'         => is_singular() ? get_permalink() : home_url( '/' ),
			'og:image'       => sao_og_image(),
		);
		foreach ( $og as $prop => $val ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $prop ), esc_attr( $val ) );
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	}
	if ( is_front_page() ) {
		$data = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'MusicSchool',
			'name'        => '埼玉音楽院',
			'url'         => home_url( '/' ),
			'image'       => sao_og_image(),
			'telephone'   => '+81-49-251-6969',
			'geo'         => array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => 35.848724,
				'longitude' => 139.533798,
			),
			'areaServed'  => array( '富士見市', 'ふじみ野市', '三芳町', '川越市', '志木市' ),
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
