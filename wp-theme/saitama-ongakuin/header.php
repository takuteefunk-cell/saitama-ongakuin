<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="container">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand">
      <span class="mark">🎵</span>
      <span><?php bloginfo( 'name' ); ?><span class="sub">SAITAMA ONGAKUIN</span></span>
    </a>
    <button class="nav-toggle" aria-label="メニュー"><span></span></button>
    <nav class="main-nav">
      <?php sao_nav( 'header' ); ?>
    </nav>
  </div>
</header>
