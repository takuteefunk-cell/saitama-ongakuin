
<footer class="site-footer">
  <div class="container">
    <div class="foot-grid">
      <div>
        <div class="foot-brand"><?php bloginfo( 'name' ); ?></div>
        <div>〒354-0026 埼玉県富士見市鶴瀬西2-1-21</div>
        <div>TEL: <a href="tel:0492516969">049-251-6969</a></div>
      </div>
      <nav>
        <?php sao_nav( 'footer' ); ?>
      </nav>
    </div>
    <div class="copyright">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
