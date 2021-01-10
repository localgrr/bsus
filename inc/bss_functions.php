<?php
if ( ! class_exists( 'bss_functions' ) ) {

	class bss_functions {

		public $class_list;

		public function __construct() {

			add_shortcode( 'bss_get_teacher', array( $this, 'get_teacher' ) ); 

			add_shortcode( 'bss_external_ticket_link', array( $this, 'external_ticket_link' ) ); 

			add_action( 'wp_enqueue_scripts', array( $this, 'cliff_scripts' ) );

			$this->class_list = new class_list();

			add_shortcode( 'class_list', array( $this->class_list, 'print_class_list' ) ); 

			$this->single_class = new single_class();

			add_shortcode( 'bss_class_times_and_location', array( $this->single_class, 'bss_class_times_and_location' ) ); 

			$this->waiting_list = new waiting_list();

			add_shortcode( 'waiting_list_admin', array( $this->waiting_list, 'waiting_list_admin' ) ); 

		}

		static function get_posts_clean($args, $fields = ["ID", "post_title", "post_excerpt", "post_name"]) {

			$posts = $posts_new = get_posts($args);

			foreach ($posts as $i => $p) {

				foreach ($p as $ii => $pp) {

					if(!in_array($ii, $fields)) {

						unset($posts_new[$i]->$ii);

					}

				}


			}

			return $posts_new;

		}

		static function cliff_scripts() {

			wp_enqueue_script( 'macy-script', '/wp-content/themes/understrap-child/js/third-party/macy.js/dist/macy.js', array(), false, 1, true);

			wp_enqueue_script( 'ajaxchimp', '/wp-content/themes/understrap-child/js/third-party/jquery.ajaxchimp/jquery.ajaxchimp.js', array()); 

			wp_enqueue_script( 'cliff-script', '/wp-content/themes/understrap-child/js/cliff-custom.js', array(), false, 1, true);

			wp_enqueue_style('cliff_css', '/wp-content/themes/understrap-child/css/main.css');

		}

		public function external_ticket_link() {

			global $post;

			$link = get_field('external_ticket_link', $post->ID);

			if($link) echo '<a href="' . $link . '" class="btn btn-primary btn-sm col-sm-3" target="new">Get Tickets</a> (external link)';

		}

		public function get_teacher( $id ) {

			global $post;

			$teachers = get_field("teacher", $post->ID);

			if( !$teachers ) return false;

			$plural = count($teachers) > 1 ? "s" : "";

			$output = '<p class="teachers"><strong>Teacher' . $plural . ':</strong> ';

			foreach ($teachers as $t) {

				$p = get_post( $t );

				$output .= '<a href="' . $p->guid . '" class="teacher">' . $p->post_title . '</a> ';

			}

			echo $output;

		}

		static function is_nearly_sold_out($em, $id) {

			return ( (($em["stock_quantity"] <3) && ($em["stock_quantity"] > 0)) || get_field("nearly_sold_out", $id));
		}

		/**
		 * Display a human readable date from a PHP date string
		 *
		 *
		 * @param $date dateObj
		 * @param $format string PHP date format
		 * 
		 * @return str
		 */

		public function human_date($date, $format = "D M j, Y H:i") {

			if(!$date) return false;

			$d = clone $date;

			return  $d->format($format);

		}

	}

}

?>