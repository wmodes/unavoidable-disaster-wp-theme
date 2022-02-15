<?php
/**
 * Single post partial template
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<!-- archive-posts.php -->

<section <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<div class="row">

		<div class="col-sm-9">

			<header class="archive-header">

				<a class="title-link" href="<?php the_permalink() ?>">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</a>

			</header><!-- .archive-header -->

			<div class="archive-excerpt">

				<?php echo apply_filters('the_excerpt', get_post_field('post_excerpt', $post_id)); ?>

			</div><!-- .archive-excerpt -->

		</div>

		<div class="col-sm-3">

			<div class="archive-thumbnail">

				<a class="thumbnail-link" href="<?php the_permalink() ?>">
					<?php the_post_thumbnail(); ?>
				</a>

			</div><!-- .archive-thumbnail -->

		</div>

	</div>

</section><!-- #post-## -->
