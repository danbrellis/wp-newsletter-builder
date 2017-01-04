<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * DLM_Admin class.
 */
class ACB_Newsletter_Admin {

	private $settings;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init') );
		add_filter( 'post_row_actions', array($this, 'post_row_actions'), 10, 2 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
		//add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array($this, 'save_metadata') );
	}

	/**
	 * init_settings function.
	 *
	 * @access private
	 * @return void
	 */
	private function init_settings() {
		$this->settings = array(
			'general' => array(
				__( 'General', 'download-monitor' ),
				array(
					array(
						'name' 		=> 'dlm_default_template',
						'std' 		=> '',
						'label' 	=> __( 'Default Template', 'download-monitor' ),
						'desc'		=> __( 'Choose which template is used for <code>[download]</code> shortcodes by default (this can be overridden by the <code>format</code> argument).', 'download-monitor' ),
						'type'      => 'select',
						'options'   => array(
							''             => __( 'Default - Title and count', 'download-monitor' ),
							'button'       => __( 'Button - CSS styled button showing title and count', 'download-monitor' ),
							'box'          => __( 'Box - Box showing thumbnail, title, count, filename and filesize.', 'download-monitor' ),
							'filename'     => __( 'Filename - Filename and download count', 'download-monitor' ),
							'title'        => __( 'Title - Shows download title only', 'download-monitor' ),
							'version-list' => __( 'Version list - Lists all download versions in an unordered list', 'download-monitor' ),
							'custom'       => __( 'Custom template', 'download-monitor' )
						)
					),
					array(
						'name' 		=> 'dlm_custom_template',
						'std' 		=> '',
						'label' 	=> __( 'Custom Template', 'download-monitor' ),
						'desc'		=> __( 'Leaving this blank will use the default <code>content-download.php</code> template file. If you enter, for example, <code>image</code>, the <code>content-download-image.php</code> template will be used instead. You can add custom templates inside your theme folder.', 'download-monitor' )
					),
					array(
						'name' 		=> 'dlm_generate_hash_md5',
						'std' 		=> '0',
						'label' 	=> __( 'MD5 hashes', 'download-monitor' ),
						'cb_label'  => __( 'Generate MD5 hash for uploaded files', 'download-monitor' ),
						'desc'		=> '',
						'type'      => 'checkbox'
					),
					array(
						'name' 		=> 'dlm_generate_hash_sha1',
						'std' 		=> '0',
						'label' 	=> __( 'SHA1 hashes', 'download-monitor' ),
						'cb_label'  => __( 'Generate SHA1 hash for uploaded files', 'download-monitor' ),
						'desc'		=> '',
						'type'      => 'checkbox'
					),
					array(
						'name' 		=> 'dlm_generate_hash_crc32b',
						'std' 		=> '0',
						'label' 	=> __( 'CRC32B hashes', 'download-monitor' ),
						'cb_label'  => __( 'Generate CRC32B hash for uploaded files', 'download-monitor' ),
						'desc'		=> __( 'Hashes can optionally be output via shortcodes, but may cause performance issues with large files.', 'download-monitor' ),
						'type'      => 'checkbox'
					),
				),
			),
			'endpoints' => array(
				__( 'Endpoint', 'download-monitor' ),
				array(
					array(
						'name' 		=> 'dlm_download_endpoint',
						'std' 		=> 'download',
						'placeholder'	=> __( 'download', 'download-monitor' ),
						'label' 	=> __( 'Download Endpoint', 'download-monitor' ),
						'desc'		=> sprintf( __( 'Define what endpoint should be used for download links. By default this will be <code>%s</code>.', 'download-monitor' ), home_url( '/download/' ) )
					),
					array(
						'name' 		=> 'dlm_download_endpoint_value',
						'std' 		=> 'ID',
						'label' 	=> __( 'Endpoint Value', 'download-monitor' ),
						'desc'		=> sprintf( __( 'Define what unique value should be used on the end of your endpoint to identify the downloadable file. e.g. ID would give a link like <code>%s</code>', 'download-monitor' ), home_url( '/download/10/' ) ),
						'type'      => 'select',
						'options'   => array(
							'ID'   => __( 'Download ID', 'download-monitor' ),
							'slug' => __( 'Download slug', 'download-monitor' )
						)
					),
					array(
						'name' 		=> 'dlm_xsendfile_enabled',
						'std' 		=> '',
						'label' 	=> __( 'X-Accel-Redirect / X-Sendfile', 'download-monitor' ),
						'cb_label'  => __( 'Enable', 'download-monitor' ),
						'desc'		=> __( 'If supported, <code>X-Accel-Redirect</code> / <code>X-Sendfile</code> can be used to serve downloads instead of PHP (server requires <code>mod_xsendfile</code>).', 'download-monitor' ),
						'type'      => 'checkbox'
					),
					array(
						'name' 		=> 'dlm_hotlink_protection_enabled',
						'std' 		=> '',
						'label' 	=> __( 'Prevent hotlinking', 'download-monitor' ),
						'cb_label'  => __( 'Enable', 'download-monitor' ),
						'desc'		=> __( 'If enabled, the download handler will check the PHP referer to see if it originated from your site and if not, redirect them to the homepage.', 'download-monitor' ),
						'type'      => 'checkbox'
					)
				)
			),
			'logging' => array(
				__( 'Logging', 'download-monitor' ),
				array(
					array(
						'name' 		=> 'dlm_enable_logging',
						'cb_label'  => __( 'Enable', 'download-monitor' ),
						'std' 		=> '1',
						'label' 	=> __( 'Download Log', 'download-monitor' ),
						'desc'		=> __( 'Log download attempts, IP addresses and more.', 'download-monitor' ),
						'type' 		=> 'checkbox'
					),
					array(
						'name' 			=> 'dlm_ip_blacklist',
						'std' 			=> '192.168.0.*',
						'label' 		=> __( 'Blacklist IPs', 'download-monitor' ),
						'desc'			=> __( 'List IP Addresses to blacklist, 1 per line. Use <code>*</code> for a wildcard.', 'download-monitor' ),
						'placeholder' 	=> '',
						'type' 			=> 'textarea'
					),
					array(
						'name' 		=> 'dlm_user_agent_blacklist',
						'std' 		=> 'Googlebot',
						'label' 	=> __( 'Blacklist user agents', 'download-monitor' ),
						'desc'		=> __( 'List browser user agents to blacklist, 1 per line.', 'download-monitor' ),
						'placeholder' => '',
						'type' 			=> 'textarea'
					),
				)
			)
		);
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {
		$this->init_settings();

		foreach ( $this->settings as $section ) {
			foreach ( $section[1] as $option ) {
				if ( isset( $option['std'] ) )
					add_option( $option['name'], $option['std'] );
				register_setting( 'acb-newsletter', $option['name'] );
			}
		}
	}

	/**
	 * admin_enqueue_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook ) {
		global $ACBNEWS, $post;
		
		if($post->post_type == 'acb_newsletter_item')
			wp_enqueue_style( 'acb_newsletter_admin_css', $ACBNEWS->plugin_url() . '/assets/css/acb-newsletter-admin.css' );
	}
	
	/**
	 * Modify tinyMce params
	 */
	public function tiny_mce_before_init($init) {
		$init['width'] = '399';
		return $init;
	}
	
	/**
	 * add compile link to newsletter table
	 */
	public function post_row_actions($actions, $post){
		if ($post->post_type == "e_newsletter"){
			$url = add_query_arg(
				array(
					'page'		=> 'acb-newsletter-compile',
					'nwsltr_id'	=> $post->ID
				),
				$_SERVER['REQUEST_URI']
			);
			$compile = '<a href="'.$url.'">' . __('Compile', 'acb_nwsltr') . '</a>';
			$actions[] = $compile;
		}
		return $actions;
	}


	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=e_newsletter', __( 'Compile for Email', 'acb_nwsltr' ), __( 'Compile', 'acb_nwsltr' ), 'publish_posts', 'acb-newsletter-compile', array( $this, 'compile_page' ) );
	}

	/**
	 * settings_page function.
	 *
	 * @access public
	 * @return void
	 */
	public function compile_page() {
		global $ACBNEWS, $acb_newsletter; ?>
		<div class="wrap">
	        <div id="icon-edit" class="icon32 icon32-posts-dlm_download"></div>
	        <h2><?php _e( 'Compile for Email', 'acb_nwsltr' ); ?></h2>
            <?php $nwsltr_id = intval($_GET['nwsltr_id']);
			$GLOBALS['acb_newsletter'] = new ACB_Newsletter( $nwsltr_id );
			if($acb_newsletter->exists()): ?>
                <?php //send data request
				if($acb_newsletter->start_inlining()):
					$args = array(
						'method' => 'POST',
						'timeout' => 45,
						'redirection' => 5,
						'httpversion' => '1.0',
						'blocking' => true,
						//'body' => array( 'returnraw' => 'yes', 'source' => $acb_newsletter->email_html ),
						'body' => array( 
							'line_length'				=> 65,
							'adapter'					=> 'nokogiri',
							'html' 						=> $acb_newsletter->email_html,
							'base_url'					=> get_bloginfo('url'),
							'preserve_styles'			=> true,
							'remove_ids'				=> false,
							'remove_classes'			=> false,
							'remove_comments'			=> false
						)
					);
					/**
					 * Unfortunately, there is no reliable API for inlining an HTML document for email via an HTTP POST request.
					 * Hence, until such time, the user has to take the html and paste it into an inliner 
					 *
					 * Posible inliner APIs include:
					 	- http://premailer.dialect.ca/api
						- http://inlinestyler.torchboxapps.com/styler/api/
					
					$compiler = wp_remote_post('http://premailer.dialect.ca/api/0.1/documents', $args);
					if ( is_wp_error( $compiler ) ):
					   $error_message = $compiler->get_error_message(); ?>
                       <h3><?php _e('Something went wrong!', 'acb_nwsltr');?></h3>
					   <p><?php echo $error_message; ?></p>
					<?php else : ?>
                    <h3><?php printf(__('%s Compiled!', 'acb_nwsltr'), $acb_newsletter->get_the_title());?></h3>
                    <p><?php _e('Copy and past the below code into your email messaging system.', 'acb_nwsltr'); ?></p>
                    	<textarea class="large-text code" rows="20"><?php echo esc_html($compiler['body']); //_e('Loading...', 'acb_nwsltr'); ?></textarea>
                    <?php endif;
					*/
					?>
					<h3><?php printf(__('%s Compiled!', 'acb_nwsltr'), $acb_newsletter->get_the_title());?></h3>
                    <p><?php printf(__('Copy and paste the below code into your favorite email inliner. May I suggest %s?', 'acb_nwsltr'), '<a href="http://zurb.com/ink/inliner.php" target="_blank"><strong>Zurb\'s Ink Inliner</strong></a>'); ?></p>
                    <p><?php _e('Then, paste <em>that</em> inlined code into your newsletter messaging service.', 'acb_nwsltr'); ?></p>
                    <textarea class="large-text code" rows="20"><?php echo $acb_newsletter->email_html; //_e('Loading...', 'acb_nwsltr'); ?></textarea>
                        
					
				<?php else: ?>
					<p><?php _e('Sorry, but there was an error in the Newsletter you selected. Make sure there is at least 1 "item" ID listed.', 'acb_nwsltr'); ?></p>
				<?php endif; ?>
            <?php else: ?>
            	<p><?php printf(__('Select a newsletter from the %s and click "compile" next to options', 'acb_nwsltr'), '<a href="'.add_query_arg("page", false).'"><strong>list</strong></a>'); ?></p>
                <p><a href="<?php echo add_query_arg("page", false); ?>"><img src="<?php echo $ACBNEWS->plugin_url() . '/assets/img/how-to-compile.jpg'; ?>" /></a></p>
            <?php endif; ?>
		</div>
		<?php
	}
	
	function add_meta_boxes($post_type, $post) {
		global $wp_meta_boxes;
		$wp_meta_boxes['e_newsletter']['side']['low']['postimagediv']['title'] = 'Newsletter Image';
		
		//For Newsletter Items
		
		// For e-Newsletter
		add_meta_box( 
			'newsletter_items',
			__('Newsletter Items', 'acb_nwsltr'),
			array($this, 'acb_newsletter_items'),
			'e_newsletter',
			'advanced',
			'high'
		);
		add_meta_box( 
			'newsletter_sponsor',
			__('Newsletter Sponsor', 'acb_nwsltr'),
			array($this, 'acb_newsletter_sponsor'),
			'e_newsletter',
			'side',
			'low'
		);
		add_meta_box( 
			'newsletter_item_guide',
			__('Newsletter Info', 'acb_nwsltr'),
			array($this, 'acb_newsletter_guide'),
			'acb_newsletter_item',
			'side',
			'high'
		);
	}
	
	//Meta Box Content	
	function acb_newsletter_items(){
		global $post;
		$items = get_post_meta( $post->ID, 'acb_newsletter_items_list', true );
		$items = $items && is_array($items) ? implode(',', $items) : '';
		wp_nonce_field( 'acb_newsletter_items', 'acb_newsletter_items_nonce' );
		
		echo '<label class="screen-reader-text" for="newsletter_items">';
			_e('List the IDs of the newsletter items to be included, in the order you\d like them.', 'acb_nwsltr');
        echo '</label>';
		echo '<input type="text" name="newsletter_items" id="newsletter_items" value="'.$items.'" style="width:98%" />';
		echo '<span class="description">' . __('Comma separated, please.', 'acb_nwsltr') . '</span>';
	}
	
	function acb_newsletter_sponsor(){
		global $post;
		$sponsor_text = get_post_meta( $post->ID, 'acb_newsletter_sponsors', true );

		wp_nonce_field( 'acb_newsletter_sponsors', 'acb_newsletter_sponsors_nonce' );
		
		echo '<label for="acb_newsletter_sponsors">';
			_e('Enter the text and images to display under the "Sponsor Highlight" section. Content constrained to 147px', 'acb_nwsltr');
        echo '</label>';
		wp_editor($sponsor_text, 'nwsltrsponsortxt', array(
				'wpautop'       =>      true,
				'media_buttons' =>      true,
				'textarea_name' =>      'acb_newsletter_sponsors'
		));
		
	}
	
	function acb_newsletter_guide(){
		printf('<span class="dashicons dashicons-megaphone alignleft" style="font-size: 40px; width: 40px; height: 40px; margin: 0 10px 10px 0;"></span><p><strong>%s</strong></p>', __('When adding images to the editor, remember that the email content is 580px wide, so be sure to resize images to no larger than 580px in width!', 'acb_nwsltr'));
		printf('<p><strong>%s</strong></p>', __('To show an image above the title, add a "Featured Image", otherwise, just add the image right in the editor.', 'acb_nwsltr'));
	}
			
	//Saving Meta
	function save_metadata( $post_id ) {
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return;
		
		// Check permissions
		if ( 'e_newsletter' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_post', $post_id ) ) return;
		}
		
		// OK, we're authenticated: we need to find and save the data
		if ( isset( $_POST['acb_newsletter_items_nonce'] ) && wp_verify_nonce( $_POST['acb_newsletter_items_nonce'], 'acb_newsletter_items' ) ) {

			$newsletter_items = sanitize_text_field( $_POST['newsletter_items'] );
			
			$newsletter_items_arr = $items = array();
			if($newsletter_items){
				$newsletter_items_arr = explode(',', $newsletter_items); //put the items into an array
				foreach($newsletter_items_arr as $id){
					if(get_post_type(intval($id)) == 'acb_newsletter_item') $items[] = $id;
				}
			}
			
			if($items && !empty($items)) update_post_meta($post_id, 'acb_newsletter_items_list', $items);
			else delete_post_meta($post_id, 'acb_newsletter_items_list');
		}
		
		if ( isset( $_POST['acb_newsletter_sponsors_nonce'] ) && wp_verify_nonce( $_POST['acb_newsletter_sponsors_nonce'], 'acb_newsletter_sponsors' ) ) {
			if(isset($_POST['acb_newsletter_sponsors']) && $_POST['acb_newsletter_sponsors'] != '')
                update_post_meta($post_id, 'acb_newsletter_sponsors', $_POST['acb_newsletter_sponsors']);
			else delete_post_meta($post_id, 'acb_newsletter_sponsors');
		}
		
	}
}

new ACB_Newsletter_Admin();

?>