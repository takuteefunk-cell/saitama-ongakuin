<?php
get_header();

if ( is_singular() && have_posts() ) :
	the_post();
	if ( sao_is_designed_content() ) :
		the_content();
	else :
		?>
<div class="page-hero">
  <div class="container">
    <h1><?php the_title(); ?></h1>
    <?php if ( is_single() ) : ?>
      <p class="lead"><?php echo esc_html( get_the_date() ); ?></p>
    <?php endif; ?>
  </div>
</div>
<section>
  <div class="container entry-content">
    <?php the_content(); ?>
  </div>
</section>
		<?php
	endif;
else :
	?>
<div class="page-hero">
  <div class="container">
    <h1><?php
	if ( is_404() ) {
		echo 'ページが見つかりません';
	} elseif ( is_search() ) {
		printf( '「%s」の検索結果', esc_html( get_search_query() ) );
	} elseif ( is_archive() ) {
		echo esc_html( wp_strip_all_tags( get_the_archive_title() ) );
	} else {
		echo 'お知らせ';
	}
	?></h1>
  </div>
</div>
<section>
  <div class="container entry-content">
	<?php if ( have_posts() ) : ?>
      <ul class="post-list">
		<?php while ( have_posts() ) : the_post(); ?>
        <li>
          <span class="post-date"><?php echo esc_html( get_the_date() ); ?></span>
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </li>
		<?php endwhile; ?>
      </ul>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
      <p>お探しのページは見つかりませんでした。</p>
      <p><a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">ホームへ戻る</a></p>
	<?php endif; ?>
  </div>
</section>
	<?php
endif;

get_footer();
