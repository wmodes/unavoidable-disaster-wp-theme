<?php
/**
 * Template Name: Single Thing
 * Template Post Type: thing,page,post
 *
 * Page for displaying a single ad.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header('thing');
$container = get_theme_mod( 'understrap_container_type' );
?>

<!-- content.php -->

<div class="wrapper" id="single-wrapper"

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<main class="site-main" id="main">

				<?php // while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'loop-templates/content-thing', 'single' ); ?>

					<?php //understrap_post_nav(); ?>

				<?php //endwhile; // end of the loop. ?>

			</main><!-- #main.site-main -->

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #single-wrapper -->

<?php get_footer();
