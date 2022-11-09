<?php
/**
 * Plugin Name:       Custom links in Elementor Image Carousel
 * Description:       Add custom links in Elementor Image Carousel widget
 * Version:           1.1.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Charlie Etienne
 * Author URI:        https://web-nancy.fr
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-links-eicw
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CustomLinksEICW {

	private static $instance;

	final public static function get_instance(): CustomLinksEICW {
		if (null === self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init() {
		add_filter( 'attachment_fields_to_edit', [ $this, 'attachment_fields_to_edit' ], 10, 2 );
		add_filter( 'attachment_fields_to_save', [ $this, 'attachment_fields_to_save' ], 10, 2 );
		add_action( 'elementor/widget/render_content', [ $this, 'widget_content' ], 10, 2 );
	}

	/**
	 *  Adds 'elementor_carousel_custom_link' field to attachment
	 *
	 * @param $form_fields
	 * @param $post
	 *
	 * @return array
	 */
	public function attachment_fields_to_edit( $form_fields, $post ): array {
		$form_fields[ 'elementor_carousel_custom_link' ] = array(
			'label' => __( 'Custom link', 'elementor' ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, 'elementor_carousel_custom_link', true ),
			'helps' => __( 'This will add a link to images in Elementor Carousel', 'elementor' ),
		);

		$target  = (bool) get_post_meta( $post->ID, 'elementor_carousel_custom_link_target', true );
		$checked = ( $target ) ? 'checked' : '';

		$form_fields[ 'elementor_carousel_custom_link_target' ] = array(
			'label' => __( 'Open in new tab ?', 'elementor' ),
			'input' => 'html',
			'html'  => "<input type='checkbox' $checked name='attachments[$post->ID][elementor_carousel_custom_link_target]' id='attachments[$post->ID][elementor_carousel_custom_link_target]' />",
			'value' => $target,
			'helps' => __( 'Open custom link in Elementor Carousel in new tab ?', 'elementor' ),
		);

		return $form_fields;
	}

	/**
	 * Saves 'elementor_carousel_custom_link' field to attachment
	 *
	 * @param $post
	 * @param $attachment
	 *
	 * @return WP_Post|array|null
	 */
	public function attachment_fields_to_save( $post, $attachment ) {
		$attachment_link = $attachment[ 'elementor_carousel_custom_link' ];
		$formated_link   = $attachment_link ?? '';
		update_post_meta( $post[ 'ID' ], 'elementor_carousel_custom_link', $formated_link );

		$attachment_target = $attachment[ 'elementor_carousel_custom_link_target' ];
		$formated_target   = isset( $attachment_target ) ? ( ( $attachment_target == 'on' ) ? '1' : '0' ) : 0;
		update_post_meta( $post[ 'ID' ], 'elementor_carousel_custom_link_target', $formated_target );

		return $post;
	}

	/**
	 * Overrides Elementor Image Carousel widget rendered content
	 *
	 * Basically just a (lighter) copy of \Elementor\Widget_Image_Carousel::render() method
	 *
	 * @param $content
	 * @param $widget
	 *
	 * @return mixed|void
	 * @see          \Elementor\Widget_Image_Carousel::render()
	 * @noinspection DuplicatedCode
	 */
	public function widget_content( $content, $widget ) {
		if ( 'image-carousel' === $widget->get_name() ) {
			$settings = $widget->get_settings_for_display();

			if ( empty( $settings[ 'carousel' ] ) ) {
				return;
			}

			$slides = [];

			foreach ( $settings[ 'carousel' ] as $index => $attachment ) {
				$image_url = Elementor\Group_Control_Image_Size::get_attachment_image_src( $attachment[ 'id' ], 'thumbnail', $settings );

				if ( ! $image_url && isset( $attachment[ 'url' ] ) ) {
					$image_url = $attachment[ 'url' ];
				}

				$image_html = '<img class="swiper-slide-image" src="' . esc_attr( $image_url ) . '" alt="' . esc_attr( Elementor\Control_Media::get_image_alt( $attachment ) ) . '" />';

				$link_tag = '';

				$link = $this->get_link_url( $attachment, $settings );

				if ( $link ) {
					$link_key = 'link_' . $index;

					if ( $this->get_custom_link( $attachment ) ) {
						$link_tag = '<a data-elementor-open-lightbox="no" href="' . $this->get_custom_link( $attachment ) . '" target="' . $this->get_link_target( $attachment ) . '">';
					} else {
						$link_tag = '<a ' . $widget->get_render_attribute_string( $link_key ) . '>';
					}
				}

				$image_caption = $this->get_image_caption( $attachment, $widget );

				$slide_html = '<div class="swiper-slide">' . $link_tag . '<figure class="swiper-slide-inner">' . $image_html;

				if ( ! empty( $image_caption ) ) {
					$slide_html .= '<figcaption class="elementor-image-carousel-caption">' . wp_kses_post( $image_caption ) . '</figcaption>';
				}

				$slide_html .= '</figure>';

				if ( $link ) {
					$slide_html .= '</a>';
				}

				$slide_html .= '</div>';

				$slides[] = $slide_html;
			}

			if ( empty( $slides ) ) {
				return;
			}

			$show_dots   = ( in_array( $settings[ 'navigation' ], [ 'dots', 'both' ] ) );
			$show_arrows = ( in_array( $settings[ 'navigation' ], [ 'arrows', 'both' ] ) );

			$slides_count = count( $settings[ 'carousel' ] );

			?>
            <div <?php $widget->print_render_attribute_string( 'carousel-wrapper' ); ?>>
                <div <?php $widget->print_render_attribute_string( 'carousel' ); ?>>
					<?php // PHPCS - $slides contains the slides content, all the relevent content is escaped above. ?>
					<?php echo implode( '', $slides ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
				<?php if ( 1 < $slides_count ) : ?>
					<?php if ( $show_dots ) : ?>
                        <div class="swiper-pagination"></div>
					<?php endif; ?>
					<?php if ( $show_arrows ) : ?>
                        <div class="elementor-swiper-button elementor-swiper-button-prev">
                            <i class="eicon-chevron-left" aria-hidden="true"></i>
                            <span class="elementor-screen-only"><?php echo esc_html__( 'Previous', 'elementor' ); ?></span>
                        </div>
                        <div class="elementor-swiper-button elementor-swiper-button-next">
                            <i class="eicon-chevron-right" aria-hidden="true"></i>
                            <span class="elementor-screen-only"><?php echo esc_html__( 'Next', 'elementor' ); ?></span>
                        </div>
					<?php endif; ?>
				<?php endif; ?>
            </div>
			<?php
		} else {
			return $content;
		}
	}

	/**
	 * Get custom link for given attachment
	 *
	 * @param $attachment
	 *
	 * @return false|mixed
	 */
	public function get_custom_link( $attachment ) {
		$custom_link = get_post_meta( $attachment[ 'id' ], 'elementor_carousel_custom_link' );
		if ( isset( $custom_link ) && is_array( $custom_link ) && ! empty( $custom_link[ 0 ] ) ) {
			return $custom_link[ 0 ];
		}

		return false;
	}

	/**
	 * Get custom link target for given attachment
	 *
	 * @param $attachment
	 *
	 * @return string
	 */
	public function get_link_target( $attachment ): string {
		$target = get_post_meta( $attachment[ 'id' ], 'elementor_carousel_custom_link_target' );
		if ( isset( $target ) && is_array( $target ) && ! empty( $target[ 0 ] ) ) {
			if ( $target[ 0 ] === '1' ) {
				return "_blank";
			}

			return "";
		}

		return "";
	}

	/**
	 * Get link url
	 *
	 * Redefines Elementor private method \Elementor\Widget_Image_Carousel::get_link_url()
	 *
	 * @param $attachment
	 * @param $instance
	 *
	 * @return array|false|mixed
	 * @see \Elementor\Widget_Image_Carousel::get_link_url()
	 */
	public function get_link_url( $attachment, $instance ) {
		if ( 'none' === $instance[ 'link_to' ] ) {
			return false;
		}

		if ( 'custom' === $instance[ 'link_to' ] ) {
			if ( empty( $instance[ 'link' ][ 'url' ] ) ) {
				return false;
			}

			return $instance[ 'link' ];
		}

		return [
			'url' => wp_get_attachment_url( $attachment[ 'id' ] ),
		];
	}

	/**
	 * Get image caption
	 *
	 * Redefines Elementor private method \Elementor\Widget_Image_Carousel::get_image_caption()
	 *
	 * @param $attachment
	 * @param $widget
	 *
	 * @return string|null
	 * @see \Elementor\Widget_Image_Carousel::get_image_caption()
	 */
	public function get_image_caption( $attachment, $widget ) {
		$caption_type = $widget->get_settings_for_display( 'caption_type' );

		if ( empty( $caption_type ) ) {
			return '';
		}

		$attachment_post = get_post( $attachment[ 'id' ] );

		if ( 'caption' === $caption_type ) {
			return $attachment_post->post_excerpt;
		}

		if ( 'title' === $caption_type ) {
			return $attachment_post->post_title;
		}

		return $attachment_post->post_content;
	}
}

CustomLinksEICW::get_instance()->init();



