<?php
if ( ! class_exists( 'single_class' ) ) {

	class single_class {

		public $class_list;

		public function __construct() {

			$this->class_list = new class_list();

		}

		function bss_class_times_and_location() {


			$id = get_the_ID();

			$class_list = $this->class_list;

			$class = $class_list->get_class_by_id($id);

			$ht = '';

			foreach ($class->class_meta as $cm) {


				
				$ht .= '<div class="class-item"><div class="class-times"><h2>Class Times</h2><ul>';

				foreach ($cm["event"]["date"]["repeat"]["human_arr"] as $class_time) {
					
					$ht .= '<li>' . $class_time . '</li>';
				}

				$ht .= '</ul>';

				$ht .= '</div>';

				$class_show = get_field("class_show", $cm["event"]["id"]);

				if($class_show) {

					$ht .= '<div class="class-show"><h3>Class Show</h3>' . $class_show . '</div>';

				}

				$ht .= '<div class="location">' . $this->get_location($cm["event"]["id"]) . '</div>';

				$ht .= $this->get_buy_box($cm, $class);

				$ht .= '</div>';

				
			}



			echo $ht;

		}

		function get_buy_box($cm, $class) {

			$class_list = $this->class_list;

			$ht .='<div class="bsus-buy">';

			$ht .= $class_list->get_event_button($cm, $class, true);

			$ht .='</div>';

			return $ht;

		}

		function get_location($id) {

			$terms = get_the_terms( $id, "mec_location" );

			if(!$terms) return false;

			$terms_meta = get_term_meta( $terms[0]->term_id );

			$name = $terms[0]->name;

			$address = $terms_meta["address"][0];

			$ht = '<h3>Location</h3><p class="venue">' . $name . ', ' . $address;

			$map_link = $this->get_map_link($name, $address);

			if($map_link) $ht .= (', ' . $map_link);

			$ht .= '</p>';

			return $ht;

		}

		function get_map_link($name, $address) {

			return '<a target="_new" href="https://www.google.com/maps/search/?api=1&query=' . $name . ', ' . $address . '">map</a>';

		}

	}

}

?>