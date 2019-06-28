<?php
/**
 * Creates metaboxes for Ads post type
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bam_Ads
 * @subpackage Bam_Ads/admin
 * @author     Richard Leo <leo.richard2@gmail.com>
 */
class Bam_Ads_Admin_Meta {

	/**
	 * Returns array of fields.
	 *
	 * @since 0.1.0
	 */
	public function get_fields() {
		$fields = array(
			'type' => array(
				'title' => __( 'AD Background color'),
				'name'  => '_bam_ads_ad_backgroundcolor',
				'type'  => 'text',
				'id'    => 'bam_ads_ad_backgroundcolor',
				'class' => 'large-text',
				'desc'  => __( 'Enter background color, e.g: #000000'),
				'sntz'  => 'text',
			),
      'url' => array(
				'title' => __( 'AD URL'),
				'name'  => '_bam_ads_ad_url',
				'type'  => 'url',
				'id'    => 'bam_ads_ad_url',
				'class' => 'large-text',
				'desc'  => __( 'Enter AD URL, e.g: https://google.com'),
				'sntz'  => 'url',
			),
			'timer' => array(
				'title' => __( 'AD Countdown time'),
				'name'  => '_bam_ads_ad_timer',
				'type'  => 'text',
				'id'    => 'bam_ads_ad_timer',
				'class' => 'large-text',
				'desc'  => __( 'YYYY/MM/DD, e.g: 2019/06/30'),
				'sntz'  => 'text',
			),
			'templates' => array(
				'title'     => __( 'Ad template (Click to select)'),
				'name'      => '_bam_ads_ad_template',
				'type'      => 'checkbox',
        'options' => array(
          'pick-template-1' => plugin_dir_url( __FILE__ ) . 'img/pick-template-1.png',
          'pick-template-2' => plugin_dir_url( __FILE__ ) . 'img/pick-template-2.png'
        ),
				'id'        => 'bam_ads_ad_template',
				'class'     => 'large-text bam-checkbox',
				'desc'      => __( 'Choose AD template' ),
				'sntz'      => 'text',
			),
		);

		return $fields;
	}

	/**
	 * Registers Metabox.
	 *
	 * @since 0.1.0
	 */
	public function meta_box() {
		add_meta_box(
			'bam_ads_ad_shortcode',
			__( 'AD Shortcode', 'bam-ads' ),
			array( $this, 'display_shortcode' ),
			'bam-ads',
			'normal'
		);
		add_meta_box(
			'bam_ads_ad_details',
			__( 'AD Settings', 'bam-ads' ),
			array( $this, 'display_fields' ),
			'bam-ads',
			'normal'
		);
	}

	public function display_shortcode() {
		$bam_shortcode = '[bam_ad id="'. get_the_ID() .'"]';
		echo '<div class="ptk-field-wrap">';
		echo '<label for="bam-shortcode">Copy this code and paste it on a post or page content:</label><br /><br />';
		echo $bam_shortcode;
		echo '</div>';
	}

	/**
	 * Displays Metaboxes.
	 *
	 * @since 0.1.0
	 */
	public function display_fields() {

		foreach ( $this->get_fields() as $field ) {
			// Get data if it was already set.
			$value = get_post_meta( get_the_ID(), $field['name'], true );

			// Display input.
			echo '<div class="ptk-field-wrap">';

			printf(
				'<label for="%1$s"><strong>%2$s</strong></label>',
				esc_attr( $field['name'] ),
				esc_html( $field['title'] )
			);

      switch ($field['type']) {
        case 'dropdown':
          echo '<select name="'. $field['name'] .'">';

          foreach($field['options'] as $option) {
            if ($option == $value) {
              echo '<option selected>'. $option .'</option>';
            } else {
              echo '<option>'. $option .'</option>';
            }
          }

          echo '</select>';
          break;

        case 'checkbox':
          echo '<div class="bam-template-list"><ul>';
          foreach($field['options'] as $template => $image) {
            $checked = '';
            if($value == $template) {
              $checked = 'checked';
            }
            echo '<li>';
            printf( '<input name="%1$s" type="%2$s" value="%3$s" class="%4$s" id="%5$s"'. $checked .' /><label for="' . $template . '"><img src="' . $image . '" /></label> ',
              esc_attr( $field['name'] ),
              esc_attr( $field['type'] ),
              esc_attr( $template ),
              esc_attr( $field['class'] ),
              esc_attr( $template )
            );
            echo '</li>';
          }
          echo '</ul></div>';
          break;

        default:
            printf( '<input name="%1$s" type="%2$s" value="%3$s" class="%4$s" placeholder="%5$s"> ',
              esc_attr( $field['name'] ),
              esc_attr( $field['type'] ),
              esc_attr( $value ),
              esc_attr( $field['class'] ),
              esc_attr( $field['desc'] )
            );
          break;
      }
			echo '</div>';
		}

		// Output nonce.
		wp_nonce_field( plugin_basename( __FILE__ ), '_bam_ads_nonce' );

	}

	/**
	 * Saves Metabox data to a database.
	 *
	 * @param int    $post_id ID of the post we are working with.
	 * @param object $post    Post object.
	 * @since 0.1.0
	 */
	public function save_data( $post_id, $post ) {
		// Check if nonce is set.
		if ( ! isset( $_POST['_bam_ads_nonce'] ) ) {
			return;
		}

		// In case it is, verify it.
		$nonce = sanitize_key( $_POST['_bam_ads_nonce'] );
		if ( ! wp_verify_nonce( $nonce, plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// Return if it is a revision or autosave.
		if ( wp_is_post_autosave( $post->ID ) || wp_is_post_revision( $post->ID ) ) {
			return;
		}

		// Is the user allowed to edit the post or page?
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		// Loop through meta array, saving or deleting data.
		foreach ( $this->get_fields() as $field ) {
			if ( isset( $_POST[ $field['name'] ] ) ) {

				// Sanitize and save data.
				if ( 'url' == $field['sntz'] ) {
					$value = esc_url_raw( wp_unslash( $_POST[ $field['name'] ] ) );
				} else {
					$value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ) );
				}
				update_post_meta( $post->ID, $field['name'], $value );

			} else {
				delete_post_meta( $post->ID, $field['name'] );
			}
		}

	}
}
