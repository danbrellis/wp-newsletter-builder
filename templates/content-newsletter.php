<?php
/**
 * Default output for a newsletter
 */

global $acb_newsletter, $ACBNEWS;
?>

<div id="dynamic-newsletter-wrapper">
    <!-- HEADER -->
    <table class="head-wrap" bgcolor="#ffffff" id="nwsltr-cnt-tbl">
        <tr>
            <td></td>
            <td class="header container">
                <?php if($acb_newsletter->is_inlining()): ?>
                		<span style="visibility:hidden;font-size:1px;line-height:1px;"><?php echo $acb_newsletter->post->post_excerpt; ?></span>
                    <table class="row" id="email-header">
                      <tbody>
                        <tr>
                            <td class="wrapper">
                        
                                <table class="six columns">
                                    <tr>
                                        <td><a href="<?php echo esc_url( get_permalink($acb_newsletter->id) ); ?>" target="_blank"><?php _e('View online', 'acb_nwsltr'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="{VR_F2AF_LINK}"><?php _e('Forward to a friend', 'acb_nwsltr'); ?></a></td>
                                        <td class="expander"></td>
                                    </tr>
                                </table>
                            </td>
                            <td class="wrapper last">
                        
                                <table class="six columns">
                                    <tr>
                                        <td align="right">{VR_SOCIAL_SHARING}</td>
                                        <td class="expander"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                      </tbody>
                    </table>
                <?php endif; ?>
                    
                <div class="content">
                    <table bgcolor="#ffffff">
                    <tr>
                        <td>
                            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo('name'); ?>">
                                <?php if ( get_theme_mod( 'conserva_logo' ) ) : ?>
                                    <img src="<?php echo esc_url( get_theme_mod( 'conserva_logo' ) ); ?>" alt="<?php bloginfo('name'); ?>" id="logo" />
                                <?php else : bloginfo( 'name' ); ?>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td align="right" valign="bottom"><h1 class="collapse"><?php printf(__('%s Newsletter', 'acb_nwsltr'), $acb_newsletter->get_the_title()); ?></h1></td>
                    </tr>
                </table>
                </div>
                <div class="header-banner">
                    <img src="<?php echo $ACBNEWS->plugin_url() . '/assets/img/'; ?>newsletter-banner.jpg" alt="<?php bloginfo( 'description' ); ?>" border="0" />
                </div>
                    
            </td>
            <td></td>
        </tr>
    </table><!-- /HEADER -->
    
    
    <!-- BODY -->
    <table class="body" style="background:#ffffff">
        <tr>
            <td></td>
            <td class="container" bgcolor="#FFFFFF">
    
                <div class="content">
                
                    <table class="row">
                        <tr>
                            <td class="wrapper last">
                        
                                <table class="twelve columns">
                                    <tr>
                                        <td><span class="h2"><?php _e('in this issue:', 'acb_nwsltr'); ?></span></td>
                                        <td class="expander"></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    
                    <?php //start loop
					foreach($acb_newsletter->items as $item): ?>
                        <table class="row">
                            <tr>
                                <td class="wrapper last">
                            
                                    <table class="twelve columns">
                                        <tr>
                                            <td class="text-pad-left"><span class="h3"><?php echo $item->post_title; ?></span><br /><?php echo $item->post_excerpt; ?> <a href="#<?php echo $item->post_name; ?>"><?php _e('(read)', 'acb_nwsltr'); ?></a></td>
                                            <td class="expander"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    <?php endforeach; ?>
                </div>
                <div class="header-banner-bottom">
                    <img src="<?php echo $ACBNEWS->plugin_url() . '/assets/img/'; ?>newsletter-banner-bottom.jpg" border="0" />
                </div>
                <div class="content">
                	<?php //start loop
					$i = 0;
					foreach($acb_newsletter->items as $item): ?>
                    	<a name="<?php echo $item->post_name; ?>" id="<?php echo $item->post_name; ?>"></a>
                        <table class="row item<?php if(++$i === $acb_newsletter->total_items) echo ' last'; ?>">
                            <tr>
                                <td class="wrapper last">
                                    <?php if(has_post_thumbnail($item->ID)): ?>
                                    	<div class="item-hero"><?php echo get_the_post_thumbnail( $item->ID, 'newsletter-hero' ); ?></div>
                                    <?php endif; ?>
                                    <table class="twelve columns">
                                        <tr>
                                            <td><?php if (current_user_can('edit_post', $item->ID) && !$acb_newsletter->is_inlining()) edit_post_link( 'Edit', null, '<br />', $item->ID ); ?><h2><?php echo $item->post_title; ?></h2><?php echo wpautop(do_shortcode($item->post_content)); ?></td>
                                            <td class="expander"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    <?php endforeach; ?>
                    <table class="columns">
                        <tr>
                            <td class="">
                                <!-- social & contact -->
                                <table class="social" width="100%">
                                  <tbody>
                                    <tr>
                                        <td>
                                            <!--- column 1 -->
                                            <table align="left" class="column">
                                                <tr>
                                                    <td>				
                                                        <span class="h2"><?php _e('Connect with Us:', 'acb_nwsltr'); ?></span>
                                                        <p><a href="http://facebook.com/ChesapeakeCLC" class="soc-btn fb" target="_blank"><?php _e('Facebook', 'acb_nwsltr'); ?></a> <a href="http://twitter.com/ChesapeakeCLC" class="soc-btn tw" target="_blank"><?php _e('Twitter', 'acb_nwsltr'); ?></a> <a href="http://www.linkedin.com/groups/Chesapeake-Conservation-Landscaping-Council-3864098" class="soc-btn li" target="_blank"><?php _e('LinkedIn', 'acb_nwsltr'); ?></a></p>
                                                    </td>
                                                </tr>
                                            </table><!-- /column 1 -->	
                                            <!--- column 2 -->
                                            <table align="left" class="column">
                                                <tr>
                                                    <td class="text-pad-left">				
                                                        <span class="h2"><?php _e('Contact Info:', 'acb_nwsltr'); ?></span>												
                                                        <p><?php _e('Web:', 'acb_nwsltr'); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">chesapeakelandscape.org</a><br /><?php _e('Listserve:', 'acb_nwsltr'); ?> <a href="http://www.chesapeakenetwork.org/groups/CCLC/" target="_blank">chesapeakenetwork.org</a><br /><?php _e('Email:', 'acb_nwsltr'); ?> <a href="mailto:cclc@chesapeakelandscape.org" target="_blank">cclc@chesapeakelandscape.org</a></p>
                                                    </td>
                                                </tr>
                                            </table><!-- /column 2 -->
                                            
                                            <span class="clear"></span>	
                                            
                                        </td>
                                    </tr>
                                  </tbody>
                                </table><!-- /social & contact -->
                            
                            
                            </td>
                        </tr>
                    </table>
                </div>
                                        
            </td>
            <td></td>
        </tr>
    </table><!-- /BODY -->

</div>