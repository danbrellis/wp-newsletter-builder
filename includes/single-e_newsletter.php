<?php 
global $acb_newsletter;

get_header(); ?>

<div id="content" class="acb_newsletter newsletter">

	<?php if (have_posts()) : ?>
	
	<?php while (have_posts()) : the_post(); ?>
    	<?php the_excerpt(); ?>
        <div class="featured_image_meta"><?php //add featured image ?>
            <?php echo the_post_thumbnail('medium'); ?>
        </div>
        <?php //generate newsletter dynamically
		//check if the newsletter has an items
		if($acb_newsletter->set_up_newsletter_items()): ?>
			<div class="newsletter-frame-wrapper" id="dynamic-newsletter-wrapper">
				<?php $ACBNEWS->get_template_part('content-newsletter'); ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>
        <div class="newsletter-navbar">
            <div id="newsletter-navbar-wrapper">
                <a href="<?php echo get_post_type_archive_link( 'e_newsletter' ); ?>" id="newsletter-archives">&nbsp;&nbsp;&nbsp;<?php _e('Back to the<br />Newsletter Archive', 'acb_nwsltr'); ?></a>
                <form class="vr_opt_in_form" method="post" action="http://oi.vresp.com?fid=fa85e8e356" target="vr_optin_popup" onsubmit="window.open( 'http://www.verticalresponse.com', 'vr_optin_popup', 'scrollbars=yes,width=600,height=450' ); return true;" style="float:right">
                  <input type="submit" class="btn btn-default" value="Newsletter Signup" style="float:right"><br>
              </form>
                <div id="prev-next-newsletter"><?php previous_post_link( '%link', __('previous<br />issue', 'acb_nwsltr') ); ?><?php next_post_link('%link', __('next<br />issue', 'acb_nwsltr')); ?></div>
                
            </div>
        </div>

	<?php endwhile; ?>
	
	<?php endif; ?>
	
</div>

<?php get_footer(); ?>
