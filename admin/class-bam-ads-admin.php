<?php

/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       https://richardleo.me/
 * @since      1.0.0
 *
 * @package    Bam_Ads
 * @subpackage Bam_Ads/admin
 * @author     Richard Leo <leo.richard2@gmail.com>
 */
class Bam_Ads_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bam_Ads_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bam_Ads_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bam-ads-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bam_Ads_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bam_Ads_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bam-ads-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function define_table_columns( $column_name ) {
		//die($column_name);
		$cols = array(
			'cb'            => '<input type="checkbox" />',
			'title'         => __( 'Title' ),
			'type'					=> __( 'Type' ),
			'template'			=> __( 'Template' ),
			'shortcode'     => __( 'Shortcode' ),
			'date'          => __( 'Date' ),
		);

		return $cols;
	}

	public function fill_custom_columns( $column, $post_id ) {
		if ( ! isset( $post_id ) ) {
			return;
		}

		switch ( $column ) {
			case "shortcode":
				echo '[bam_ad id="' . $post_id . '"]';
				break;
			case "type":
			 $category = get_the_terms( $post_id , 'ads-category' );
			  echo $category[0]->name;
				break;
			case "template":
				echo get_post_meta( $post_id, '_bam_ads_ad_template', true );
				break;
		}
	}

	public function bam_register_post_type_ad() {

		$labels = array(
				'menu_name'     			=> __( 'BAM Ads' ),
				'name_admin_bar'     	=> __( 'Ad', 'add new on admin bar' ),
				'all_items'     			=> __( 'All Ads' ),
				'name'          			=> __( 'Ads' ),
				'singular_name' 			=> __( 'Ad' ),
				'edit_item'     			=> __( 'Edit Ad' ),
				'new_item'      			=> __( 'New Ad' ),
				'add_new_item'  			=> __( 'Add New Ad' ),
				'search_items'       	=> __( 'Search Ads'),
				'not_found'     			=> __( 'No ads found.' ),
				'not_found_in_trash' 	=> __( 'No ads found in Trash.' )
		);

		$supports = array(
				'title',
				'editor' => false,
				'wpcom-markdown',
		);

		register_post_type( 'bam-ads', array(
				'labels'              	=> $labels,
				'public'              	=> true,
				'publicly_queryable'  	=> false,
				'show_ui'             	=> true,
				'query_var'           	=> true,
				'can_export'          	=> true,
				'exclude_from_search' 	=> true,
				'has_archive'         	=> false,
				'query_var'           	=> 'ads',
				'menu_icon'           	=> 'dashicons-welcome-view-site',
				'rewrite'             	=> array( 'slug' => 'Ads' ),
				'supports'            	=> $supports,
				'register_meta_box_cb' 	=> null,
				'taxonomies'           	=> array( 'ads-category'),
				'capability_type'      	=> 'page',
				'map_meta_cap'         	=> true,
				'can_export'           	=> true,
			)
		);
	}

	public function bam_post_taxonomies() {

		// Labels for Ads category taxonomy.
		$labels = array(
			'name'              => _x( 'Ad Types', 'taxonomy general name'),
			'singular_name'     => _x( 'Ad Types',   'taxonomy singular name'),
			'search_items'      => __( 'Search Ad Types' ),
			'all_items'         => __( 'All Ad Types' ),
			'parent_item'       => __( 'Parent Ad Type' ),
			'parent_item_colon' => __( 'Parent Ad Type:' ),
			'edit_item'         => __( 'Edit Ad Type' ),
			'update_item'       => __( 'Update Ad Type' ),
			'add_new_item'      => __( 'Add New Ad Type' ),
			'new_item_name'     => __( 'New Ad Type Name' ),
			'menu_name'         => __( 'Ad Types' ),
		);

		// Register Ads category taxonomy.
		register_taxonomy( 'ads-category', array( 'bam-ads' ), array(
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'ad-category' ),
			'query_var'         => true,
		) );

	}

	// Create bam_ad shortcode
	public function bam_ad_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id'				=> '',
			'title'  		=> '',
      'type'      => '',
      'template'	=> '',
      'url'       => '',
      'bgcolor'		=> ''
    ), $atts ) );

		// Buffer output so shortcode doesn't appear always above content
		ob_start();

		// Check id
		if(!empty($id)) {

			$timer = get_post_meta( $id, '_bam_ads_ad_timer', true );
			$template = get_post_meta( $id, '_bam_ads_ad_template', true );

			// Check to override url
			if(!empty($url)) {
				$ad_url = $url;
			} else {
				$ad_url = get_post_meta( $id, '_bam_ads_ad_url', true );
			}

			// Check to override title
			if(!empty($title)) {
				$ad_title = $title;
			} else {
				$ad_title = get_the_title( $id );
			}

			// Verify if its a post, if bgcolor is not override and checking category
			if(is_single()) {
				if(empty($bgcolor)) {
					if(in_category('nfl')) {
						$bgcolor = '#000';
					} else if(in_category('nfl')) {
						$bgcolor = 'orange';
					} else if(in_category('mlb')) {
						$bgcolor = 'blue';
					} else if(in_category('nba')) {
						$bgcolor = 'orange';
					}
				}
			}

			// Check template
			if($template == 'pick-template-1') :
    	?>

				<div class="bam-ad-wrap">
				  <a href="<?php echo $ad_url; ?>" target="_blank">
				    <div class="bam-ad-wrapper">
				      <div class="bam-ad-content" style="background-color: <?php echo $bgcolor; ?>">
				        <div class="bam-ad-image">
				          <img src="https://i.ibb.co/X5mSLRw/nflpic.png" alt="">
				        </div>
				        <div class="bam-ad-text">
				          <div class="bam-ad-countdown">
				            <div class="bam-countdown bam-countdown-<?php echo $id; ?>"></div>
				            <span>Remaining Time To Place Bet</span>
				          </div>
				          <div class="bam-ad-title">
				            <span class="bam-ad-highlight"><?php echo $ad_title; ?></span>
				            <span class="bam-ad-subtitle">Hurry up! <strong>25</strong> people have placed this bet</span>
				          </div>
				        </div>
				      </div>
				      <div class="bam-ad-button" style="background-color: <?php echo $bgcolor; ?>">
				        <div>
				          <img src="https://i.ibb.co/ySnNgCy/BetNow.png" alt="" width=120px height=50px>
				        </div>
				        <span>Trusted Sportsbetting.ag</span>
				      </div>
				    </div>
				  </a>
				</div>

				<script type="text/javascript">
					jQuery(document).ready(function($){

						<?php
						echo "var id = '{$id}';";
						echo "var timer = '{$timer}';";
						 ?>

						$('.bam-countdown-'+id).countdown(timer, function(event) {
						  var $this = $(this).html(event.strftime(''
						    + '<div><div>DAYS</div><div>%D</div></div> '
						    + '<div><div>HOURS</div><div>%H</div></div> '
						    + '<div><div>MIN</div><div>%M</div></div> '
						    + '<div><div>SEC</div><div>%S</div></div>'));
						});

					});
				</script>

			<?php else: ?>

				<img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/pick-template-2.png'; ?>" alt="Pick Template 2">

			<?php endif; ?>

		<?php
		} else {
			echo '<span style="color: red">Bam Ad Error: No id specified, please add id to shortcode</span>';
		}
    $html = ob_get_contents();
  	ob_end_clean();
  	return $html;

	}

	// Register bam_ad shortcode
	public function register_shortcodes(){

    add_shortcode('bam_ad', array($this, 'bam_ad_shortcode'));

	}

}
