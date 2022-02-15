<?php
/**
 * Template Name: Thing Template
 * Template Post Type: thing,page,post
 *
 * Template for displaying an ad.
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<!-- content-thing.php -->

<?php while ( have_posts() ) : the_post(); ?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>" data-postid="<?php the_ID(); ?>">

	<div class="entry-content thing-content 
		<?php if ( get_field('ad_clickable') == 1 ) echo "clickable" ?>
		<?php if ( ! empty ( get_field('ad_image_hover') ) ) echo "animated" ?>
		">

		<div class="image">
			<div class="image-inner">
			
				<?php $ad_image = get_field('ad_image'); ?>
				<?php $ad_image_hover = get_field('ad_image_hover'); ?>
				<?php $ad_image_size = get_field('image_size'); // (eg. large) ?>

				<?php if ( ! empty ( $ad_image ) ): ?>
					<?php if ( empty ( $ad_image_hover ) ): ?>
						<?php echo wp_get_attachment_image( $ad_image['id'], $ad_image_size, false, "" ); ?>
					<?php else: ?>
						<?php $mouseover_attr = array(
							"data-plain" => $ad_image['url'],
							"data-hover" => $ad_image_hover['url'] 
						); ?>
						<?php echo wp_get_attachment_image( $ad_image['id'], full, false, $mouseover_attr ); ?>
					<?php endif; ?>
					<!-- <img 
					src="<?php //echo $ad_image['url']; ?>" 
					alt="<?php //echo $ad_image['alt']; ?>" /> -->
				<?php else: ?>
						<?php $ad_content = get_field('ad_content'); ?>
						<?php echo $ad_content; ?>
				<?php endif; ?>
			</div> <!-- .image-inner -->
		</div> <!-- .image -->

		<?php if ( get_field('ad_clickable') == 1 ): ?>
			<div class="metadata">
				<div class="tail"></div>
				<div class="close">x</div>
				<div class="title">
					<h2><?php echo get_the_title() ?></h2>
				</div><!-- .title -->
				<div class="info">
					<?php $ad_info = get_field('ad_info'); ?>
					<?php if ( ! empty ( $ad_info ) ): ?>
						<p><?php echo $ad_info; ?></p>
					<?php endif; ?>
				</div><!-- .info -->
				<div class="contact">
					<?php $ad_call_to_action = 
					get_field('ad_call_to_action'); ?>
					<?php if ( ! empty ( $ad_call_to_action ) ): ?>
						<h3><?php echo $ad_call_to_action; ?></h3>
					<?php endif; ?>
					<?php $ad_phone_contact = get_field('ad_phone_contact'); ?>
					<?php if ( ! empty ( $ad_phone_contact ) ): ?>
						<div class="phone">
							<?php do_shortcode( '[mycred_link href=”tel:' . $ad_phone_contact . '; target=”_blank”]' . $ad_phone_contact . '[/mycred_link]'"' ); ?>
							// <a href="tel:<?php echo $ad_phone_contact; ?>">
								// <?php echo $ad_phone_contact; ?></a>
						</div><!-- .phone -->
					<?php endif; ?>
					<?php $ad_email_contact = get_field('ad_email_contact'); ?>
					<?php if ( ! empty ( $ad_email_contact ) ): ?>
						<div class="email">
							<a href="mailto:<?php echo $ad_email_contact; ?>">
								<?php echo $ad_email_contact; ?></a>
						</div><!-- .email -->
					<?php endif; ?>
					<?php $ad_website = get_field('ad_website'); ?>
					<?php if ( ! empty ( $ad_website ) ): ?>
						<div class="website">
							<a href="<?php echo $ad_website; ?>"
								target="_contact">
								<?php echo $ad_website; ?></a>
						</div><!-- .website -->
					<?php endif; ?>
				</div> <!-- .contact -->
				<div class="contributor">
					<?php 
						// get the CPT Thing's author id
						$author_id = get_the_author_meta('ID');
						// get Ultimate Member data
						$um_user_data = um_fetch_user( $author_id );
						$user_profile_link = um_user_profile_url();
						// first, try to get the contributor from the form (if supplied)
						$contributor = get_field('ad_contributor');
						// if ( class_exists( 'PC' ) ) PC::debug("$contributor", print_r($contributor, True));
						// second, if not supplied, we use the one from the profile
						if ( empty( $contributor ) ) {
							// majority of users will be signed in through Ultimate Member
							// so get the name from there
							$contributor = um_user('display_name');
							// if ( class_exxists( 'PC' ) ) PC::debug("$contributor", print_r($contributor, True));
							// third, if we still don't have it, use the default wp
							if ( empty( $contributor ) ) {
								$contributor = get_the_author_meta( 'display_name' , $author_id );
								// if ( class_exists( 'PC' ) ) PC::debug("$contributor", print_r($contributor, True));
							}
						}
						// if ( class_exists( 'PC' ) ) PC::debug("final $contributor", print_r($contributor, True));
					?>
					<span class="contributed-by">Contributed by</span>
					<span class="contributor">
						<a href="<?php echo $user_profile_link; ?>" target="_profile"><?php echo $contributor; ?></a>
					</span>
				</div><!-- .contributor -->
				<div class="upvote">
					<?php 
					// TODO: Can we append the outer post-id to the CPT post-id to get 
					// a uniquelike coun8t for ech CPT? Yes, but how do we get the outer 
					// post-id here?
					echo do_shortcode(
						'[wp_ulike for="thing" id="' . get_the_ID() . '" style="wpulike-twitter"]'); ?>
				</div><!-- .upvote -->

			</div><!-- .metadata -->
		<?php endif; ?>

	</div><!-- .entry-content.thing-content -->

</article><!-- #post-## -->

<?php endwhile; // end of the loop. ?>
