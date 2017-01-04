<?php
/**
 * DLM_Download class.
 */
class ACB_Newsletter {

	public $items;
	public $total_items;
	public $inlining = false;
	public $email_html;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @param mixed $id
	 * @return void
	 */
	public function __construct( $id ) {
		$this->id              = absint( $id );
		$this->post            = get_post( $this->id );
	}
	
	/**
	 * __get function.
	 *
	 * @access public
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		$value = get_post_meta( $this->id, $key, true );

		return $value;
	}
	
	/**
	 * exists function.
	 *
	 * @access public
	 * @return void
	 */
	public function exists() {
		return ( ! is_null( $this->post ) );
	}
	
	/**
	 * determines if inlining is set to true or false
	 *
	 * @access public
	 * @return bool
	 */
	public function is_inlining() {
		return (bool)$this->inlining;
	}
	
	/**
	 * sets the status of compiling for email
	 *
	 * @access public
	 * @return void
	 */
	public function start_inlining() {
		global $ACBNEWS;
		$this->inlining = true;
		
		if($this->set_up_newsletter_items()){
			//create an html doc with the css and template
			$document = DOMImplementation::createDocument(
				null,
				'html',
				DOMImplementation::createDocumentType(
					"html", 
					"-//W3C//DTD XHTML 1.0 Transitional//EN", 
					"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
				)
			);
			$document->formatOutput = true;
			
			$html = $document->documentElement;
			$head = $document->createElement('head');
			$title = $document->createElement('title');
				$title_text = $document->createTextNode(sprintf(__('%s Newsletter | %s', 'acb_nwsltr'), $this->get_the_title(), get_bloginfo('name')));
				$title->appendChild($title_text);
			$meta = $document->createElement('meta'); //<meta name="viewport" content="width=device-width" />
				$meta_name_attr = $document->createAttribute('name');
				$meta_name_attr->value = 'viewport';
				$meta->appendChild($meta_name_attr);
				$meta_content_attr = $document->createAttribute('content');
				$meta_content_attr->value = 'width=device-width';
				$meta->appendChild($meta_content_attr);
			$css = $document->createElement('style', file_get_contents($ACBNEWS->plugin_path() . '/assets/css/acb-newsletter-email.css'));
				$css_type_attr = $document->createAttribute('type');
				$css_type_attr->value = 'text/css';
				$css->appendChild($css_type_attr);
			$body = $document->createElement('body');
				ob_start(); ?>
                <div class="newsletter-frame-wrapper" id="dynamic-newsletter-wrapper">
                	<?php $ACBNEWS->get_template_part('content-newsletter'); ?>
                </div>
                <?php $body_text = ob_get_clean();
				$body_text = mb_convert_encoding($body_text, 'HTML-ENTITIES', 'UTF-8');
				$body->appendChild($document->createTextNode($body_text));
			
			
			$head->appendChild($title);
			$head->appendChild($meta);
			$head->appendChild($css);
			
			$html->appendChild($head);
			$html->appendChild($body);
			
			$this->email_html = $document->saveHTML();
			return true;
		}
		else return false;
	}
		
	/**
	 * checks for a valid url to the external newsletter
	 */
	public function external_newsletter_file(){
		$value = esc_url($this->__get('newsletter_link'));
		if($value){
			$this->external_newsletter_url = $value;
			return true;
		}
		
		return false;
	}
	
	public function set_up_newsletter_items(){
		$potential_item_ids = (array) $this->__get('acb_newsletter_items_list');

		$get_items = array(
			'post__in'			=> $potential_item_ids,
			'post_type'			=> 'acb_newsletter_item',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
			'orderby'			=> 'post__in'
		);
		$nwsltr_items = new WP_Query( $get_items );
		
		if(!$nwsltr_items->have_posts()) return false;
		$this->items = $nwsltr_items->get_posts();
		$this->total_items = count($this->items);
		return true;
	}

	/**
	 * get_title function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_the_title() {
		return $this->post->post_title;
	}

	/**
	 * the_title function.
	 *
	 * @access public
	 * @return void
	 */
	public function the_title() {
		echo $this->get_the_title();
	}

	/**
	 * get_the_short_description function.
	 *
	 * @access public
	 * @return void
	 */
	public function get_the_short_description() {
		return wpautop( do_shortcode( $this->post->post_excerpt ) );
	}

	/**
	 * the_short_description function.
	 *
	 * @access public
	 * @return void
	 */
	public function the_short_description() {
		echo $this->get_the_short_description();
	}
	
}