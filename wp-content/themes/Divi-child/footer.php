
    <?php if ( 'on' == et_get_option( 'divi_back_to_top', 'false' ) ) : ?>

	<span class="et_pb_scroll_top et-pb-icon"></span>

<?php endif;

if ( ! is_page_template( 'page-template-blank.php' ) ) : ?>

			<footer id="main-footer">

				<div id="footer-bottom">
					<div class="container clearfix">
				<?php
          if ( has_nav_menu( 'footer-menu' ) ) :
            wp_nav_menu( array(
              'theme_location' => 'footer-menu',
              'depth'          => '1',
              'menu_class'     => 'bottom-nav',
              'container'      => '',
              'fallback_cb'    => '',
              'menu_class'     => 'et-social-icons',
              'menu_id'        => 'policyNav',#
            ) );
          endif;
        ?>

						<p id="footer-info">Copyright &copy; <?php echo date("Y") ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a><!-- | <em>Website by <a target="_blank" title="Napa Web Designer" href="http://designsbytierney.com">David Tierney</a></em>--></p>
					</div>	<!-- .container -->
				</div>
			</footer> <!-- #main-footer -->
		</div> <!-- #et-main-area -->

<?php endif; // ! is_page_template( 'page-template-blank.php' ) ?>

	</div> <!-- #page-container -->

	<?php wp_footer(); ?>
</body>
</html>
