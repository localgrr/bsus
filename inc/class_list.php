<?php
if ( ! class_exists( 'class_list' ) ) {

	class class_list {

		const TIMEZONE = 'Europe/Berlin';

		public $classes_array;

		public $cats;

		public function __construct() {

			$this->cats = get_categories();
			
			$this->classes_array = $this->build_classes_array();

		}

		/**
		 * Check the query string for notices and print them above the class list.
		 *
		 */

		public function check_notices() {

			if(isset($_GET["updated"])) if($_GET["updated"] == "true") echo bss_functions::alert("You have been added to the waiting list", "info");

		}

		/**
		 * Print the HTML for the class list
		 *
		 * @return string HTML
		 */

		public function print_class_list() {

			$this->check_notices();

			$ht = '<ul class="class-list">';

			foreach ($this->classes_array as $class) {

				$past = ($class->past) ? "is-past" : "true";

				$hide = ($class->hide_class) ? ' style="display:none"' : '';

				$url = "/" . $class->post_name;
				
				$ht .= '<li class="class-list-item ' . $this->get_class_css($class) . '">
						<h3 class="class-list-item-title"><a href="' . $url . '" class="More info">' . $class->post_title . '</a></h3>';

				if(count($class->class_meta) > 0) {

					$ht .= '<ul class="class-meta">';

					foreach ($class->class_meta as $i => $cm) {

						$repeat_dates = isset($cm["event"]["date"]["repeat"]["human"]) ? $cm["event"]["date"]["repeat"]["human"] : '';

						$ht .= '

						<li class="class-meta-item"' . $hide . '><div class="row">

							<div class="col-md-7">

							<a href="' . $url . '" title="More info">';

							$ht .= 

							(($cm["postponed"]) ? '<strong>(Postponed)</strong> <del>' : '') .

							(($repeat_dates != '') ? $repeat_dates : '') .

							(isset($cm["event"]["date"]["start"]["time"]) ? (' ' . $cm["event"]["date"]["start"]["time"]) : '') .

							(isset($cm["event"]["date"]["end"]["time"]) ? (' to ' . $cm["event"]["date"]["end"]["time"]) : '') . 

							(($cm["postponed"]) ? '</del>' : '');

							$ht .= 

							'</a></div><div class="col-md-5"><div class="product-info">'

							. $this->get_event_button($cm, $class, true)

							. '</div></div></div></li>';

						

					}

					$ht .= '</li></ul>';

				} else {

					$ht .= $this->enquiry_button($class);

				}

			}

			$ht .= '</ul>';

			return $ht;

		}

		/**
		 * Get the class object by its post ID
		 *
		 *
		 * @param int $id Post ID of class
		 * @return class object or false if not found
		 */

		function get_class_by_id($id) {

			foreach ($this->classes_array as $class) {
				
				if($class->ID == $id) return $class;

			}

			return false;

		}

		/**
		 * Unfinished. Function for returning css identifiers of a
		 * class object as determined by it's categories
		 *
		 *
		 * @param obj $class class object
		 * @return string
		 */

		function get_class_css($class) {
	
			$cats = $this->cats;

			$current_cat = wp_get_post_categories($class->ID, ['exclude'=>1]);

			$css_cats = [];

			foreach ($current_cat as $i => $cc) {	

				array_push($css_cats, "class-" . get_category($cc)->slug);

				//$cat = get_the_category_by_ID($cc->)

			}

			return implode(" ", $css_cats);
		}

		/**
		 * Return the human readable start date string of a given class object
		 *
		 *
		 * @param obj $class class object
		 * 
		 * @return string
		 */

		public function get_start_date_human($class) {

			return ((isset($class->class_meta[0]["event"]["date"])) ? $class->class_meta[0]["event"]["date"]["start"]["human"] : false);

		}

		/**
		 * Display a Bootstrap button that launches a contact form modal. 
		 * It also passes information about the class to the contact form
		 *
		 *
		 * @param obj $class class object
		 * @param string $text button text
		 * @param string $type Bootstrap button style. See:
		 * https://getbootstrap.com/docs/4.0/components/buttons/
		 * 
		 * @return string
		 */

		public function enquiry_button($class, $text = 'Make an enquiry', $btn_type = 'secondary') {

			$date = $this->get_start_date_human($class);

			return '<a title="Contact Us" href="#enquiry-modal" class="btn btn-contact btn-' . $btn_type . '" data-toggle="modal" data-subject="' . $class->post_title . ' - ' . $date . '" >' . $text . '</a>';

		}


		/**
		 * Clean and truncate a classes long description
		 *
		 * @param int $class_id post ID of class
		 * @param int $limit maximum amount of words to show
		 * 
		 * @return string
		 */

		function get_excerpt($class_id, $limit = 40) {

			$content = get_post_field('post_content', $class_id);
			$content = strip_tags($content);

		    if (str_word_count($content, 0) > $limit) {
		        $words = str_word_count($content, 2);
		        $pos   = array_keys($words);
		        $content  = substr($content, 0, $pos[$limit]) . '...';
		    }
		    return $content;
		}

		/**
		 * Choose a button to display with a class event
		 *
		 *
		 * @param obj $class_meta specific product/event pair from the class object
		 * @param obj $class class object
		 * @param bool $show_price show the price of the class on the button
		 * 
		 * @return string html
		 */

		public function get_event_button($class_meta, $class, $show_price = false) {

			$wl = new waiting_list();

			$external = get_field('external_ticket_link', $class->ID);

			$ht = '';

			if(isset($class_meta["event"]["date"]["past"])) if($class_meta["event"]["date"]["past"] && !$class_meta["postponed"]) {

				//return $this->enquiry_button($class, 'Past event. Enquire about future classes', 'secondary');

				return $wl->waiting_list_button($class, $class_meta["product"]["product_id"], "Join waiting list for the next class");
			}  

			if(isset($class_meta["product"]["product_id"])) {

				$url = '/cart/?add-to-cart=' . $class_meta["product"]["product_id"];

				$buy_button = '<a class="btn btn-success" href="' . $url . '">Buy now ';

				if($show_price) $buy_button .= '&euro;' . $class_meta["product"]["price"];

				$buy_button .= '</a>';

				if($class_meta["product"]["stock_quantity"] <= 0) {

					$ht .= $wl->waiting_list_button($class, $class_meta["product"]["product_id"]);

				} else {

					if($class_meta["product"]["nearly_sold_out"]==true) {

						$ht .= '<span class="bg-warning">Nearly sold out!</span>';

					}

					$ht .= $buy_button;

				}

			}	

			if($external) {

				$ht .= '<a class="btn btn-info" href="' . $external . '" target="_new">Buy from external provider</a>';
			}

			return $ht;
		}

		/**
		 * Make the class object
		 *
		 * 
		 * @return obj 
		 */

		public function build_classes_array() {

			$args = array(

				'posts_per_page'   => -1,
				'orderby'          => 'date',
				'order'            => 'DESC',
				'post_type'        => 'class',
				'post_status'      => 'publish'

			);

			$posts_array = bss_functions::get_posts_clean( $args ); 

			$this->get_class_meta($posts_array);

			$now = new DateTime();

			foreach ($posts_array as $i=> $p) {

				//create separate arrays for easy sorting
				//past events
				$cm_past_events = [];
				//current and future events
				$cm_events = [];
				//non events EG script editing
				$cm_non_events = [];
				//potponed are active events that have no dates set 
				//or even if they do ignore them
				$cm_postponed_events = [];

				//set up flags
				$past = true;
				$non_event = true;
				$postponed = true;

				foreach ($p->class_meta as $ii => $cm) {

					if(isset($cm["event"]["date"]["start"]["date"])) {

						if($cm["event"]["date"]["start"]["date"] <= $now) {

							//event has passed
							$cm["event"]["date"]["past"] = true;
							array_push($cm_past_events, $cm);

						} else {

							array_push($cm_events, $cm);
							$past = false;

						}

						$non_event = false;


					} else {

						//event has no date therefor is a "non-event"
						array_push($cm_non_events, $cm);

					}


				}

				//if all events in a class meet a condition then mark the whole class as this condition

				if($past) $p->past = true;

				if($postponed) $p->postponed = true;

				if($non_event) $p->non_event = true;

				//Sort the events within a class by date

				usort($cm_events, function($a, $b) {

				    return($a["event"]["date"]["start"]["date"]->getTimestamp() - $b["event"]["date"]["start"]["date"]->getTimestamp());

				});

				//now tack on the past events and then the non events

				$posts_array[$i]->class_meta = array_merge($cm_events, $cm_postponed_events, $cm_past_events, $cm_non_events); 


			}

			//Sorting of the classes in the list themselves

			usort($posts_array, function($a, $b) {

				if($a->non_event == true) return 1;
				if($b->non_event == true) return 0;

				if($a->past == true ) return 1;
				if($b->past == true ) return 0;

			    return($a->class_meta[0]["event"]["date"]["start"]["date"]->getTimestamp() - $b->class_meta[0]["event"]["date"]["start"]["date"]->getTimestamp());

			});

			return array_merge($posts_array);

		}

		/**
		 * Create the "class meta" object for each class. This contains the product/event pairs
		 * read from the ACF field class_meta
		 *
		 *
		 * @param arr $posts array a Wordpress array containing an array of class objects
		 * 
		 * @return null
		 */

		public function get_class_meta($posts_array) {

			foreach ($posts_array as $i => $p) {

				$class_events = get_field("class_events", $p->ID);

				$hide_class = get_field("hide_class", $p->ID);

				$posts_array[$i]->class_meta = [];

				$posts_array[$i]->hide_class = $hide_class;

				if($class_events) foreach ($class_events as $ii => $event) {

					$posts_array[$i]->class_meta[$ii]["product"] = $this->get_product($event["woo_product"], $p->ID);

					$posts_array[$i]->class_meta[$ii]["event"] = $this->get_event($event["event"]);

					$posts_array[$i]->class_meta[$ii]["postponed"] = $event["postponed"];

				}

			}

		}

		/**
		 * Create an array of Woo product information for a specific class
		 *
		 *
		 * @param int $product_id Woocommerce product id
		 * @param int $class_id post id for the related class
		 * 
		 * @return arr
		 */

		public function get_product($product_id, $class_id) {

			if(!$product_id) return false;

			$pf = new WC_Product_Factory(); 

			$product = $pf->get_product( $product_id );

			$arr = [

				'product_id' => $product->get_id(),
				'price' => $product->get_price(),
				'stock_quantity' => $product->get_stock_quantity()

			];

			$arr["nearly_sold_out"] = bss_functions::is_nearly_sold_out($arr, $class_id, $product_id);

			$arr["waiting_list"] = waiting_list::get_waiting_list($product_id);


			return $arr;
		}

		/**
		 * Get the event details
		 *
		 *
		 * @param int $id Event ID
D		 * 
		 * @return arr
		 */

		public function get_event($id) {

			$arr = [

				'id' => $id,
				'date' => $this->get_mec_date($id)
			];

			return $arr;

		}

		private function get_special_days($arr, $id) {

			$special_days = get_field("special_days", $id);

			if(!$special_days) return $arr;

			if(!isset($arr["start"]["date"])) return false;

			foreach ($special_days as $special_day) {

				if($special_day["date"] == $arr["start"]["date"]->format("Y-m-d")) {

					$arr["start"]["special"] = $special_day;

				}


			}

			//pre_r($arr);

			return $arr;

		}


		/**
		 * Parse the dates from Modern Events calendar
		 * https://webnus.net/modern-events-calendar/
		 * Into useful formats
		 *
		 * @param int $id Event ID
D		 * 
		 * @return arr
		 */

		private function get_mec_date($id) {

			$date_json = get_post_meta( $id, "mec_date", true);

			if(!$date_json["start"]["hour"]) return false;

			$start_date = $this->build_mec_date($date_json, "start");

			$start_time = $start_date->format("H:i");

			$end_date = $this->build_mec_date($date_json, "end");

			$end_time = $end_date->format("H:i");

			$arr = [
				'start' => [
					'date' => $start_date,
					'time' => $start_time,
					'human' => bss_functions::human_date($start_date)
				],
				'end' => [
					'date' => $end_date,
					'time' => $end_time,
					'human' => bss_functions::human_date($end_date)
				],
				'repeat' => $this->get_mec_repeat($id, $start_date, $end_date)
			];

			$arr["special"] = $this->get_special_days($arr, $id);

			return $arr;

		}

		/**
		 * Push date object and human readable date to the $arr_repeat array
		 *
		 * @param arr $array_repeat ID
		 * @param date $start_date_obj
		 * @param date $end_date_obj
		 */
		private function push_to_arr_repeat($arr_repeat, $start_date_obj, $end_date_obj) {

			array_push($arr_repeat, [

				'start' => [
					'date' => clone $start_date_obj,
					'human' => bss_functions::human_date($start_date_obj)
				],
				'end' => [
					'date' => clone $end_date_obj,
					'human' => bss_functions::human_date($end_date_obj)
				]

			]);

			return $arr_repeat;

		}

		/**
		 * Parse the repeating dates from the ME Calendar
		 * https://webnus.net/modern-events-calendar/
		 * Into useful formats
		 *
		 * @param int $id Event ID
		 * @param date $start_date
		 * @param date $end_date
D		 * 
		 * @return arr
		 */
	
		private function get_mec_repeat($id, $start_date, $end_date) {

			/* 
			 * I found if I don't work with clones any date modify stuff actually affects the original date
			*/
			$sd = clone $start_date;
			$ed = clone $end_date;

			$st = $start_date->format("H:i");
			$et = $end_date->format("H:i");

			$repeat_json = get_post_meta( $id, "mec_repeat", true);

			$arr_repeat = [];

			if($repeat_json["type"] == "weekly") {

				for ($i=1; $i < $repeat_json["end_at_occurrences"] ; $i++) { 

					$start_date_obj = $sd->modify('+1 weeks');

					$end_date_obj = $ed->modify('+1 weeks');
					
					$arr_repeat = $this->push_to_arr_repeat($arr_repeat, $start_date_obj, $end_date_obj);

				}

			}

			if($repeat_json["type"] == "custom_days") {

				$mec_in_days = explode(",",get_post_meta( $id, "mec_in_days", true));

				foreach ($mec_in_days as $mid) {

					$mid_parts = explode(":", $mid);

					$date_string_start = $mid_parts[0] . " " . $mid_parts[2];

					$date_string_end = $mid_parts[0] . " " . $mid_parts[3];

					$start_date_obj = DateTime::createFromFormat('Y-m-d h-i-A', $date_string_start, new DateTimeZone(static::TIMEZONE));

					$end_date_obj = DateTime::createFromFormat('Y-m-d h-i-A', $date_string_end, new DateTimeZone(static::TIMEZONE));

					$arr_repeat = $this->push_to_arr_repeat($arr_repeat, $start_date_obj, $end_date_obj);
					
				}

			}

			if($repeat_json["type"] == "certain_weekdays") {

				/*
				 * this contains an array containing the days of the week
				 * 1 being Monday and 7 being Sunday
				*/
				$weekdays = $repeat_json["certain_weekdays"];

				$occurrences = $repeat_json["end_at_occurrences"];

				/*
				 * Get the very first week of dates for this event, then we can just +7 days for the rest
				 * of the occurences. Not elegant I guess!
				*/
				$day_names = ["","monday","tuesday","wednesday","thursday","friday","saturday","sunday"];

				$sd_monday_this_week = $sd->modify('monday this week');
				$ed_monday_this_week = $ed->modify('monday this week');

				foreach ($weekdays as $i => $day) {

					if($i == 0) continue; //we dont want the very first date as these are repeats

					$start_date_obj = $sd_monday_this_week->modify($day_names[$day] . ' this week ' . $st);
					$end_date_obj = $ed_monday_this_week->modify($day_names[$day] . ' this week ' . $et);

					$arr_repeat = $this->push_to_arr_repeat($arr_repeat, $start_date_obj, $end_date_obj);

				}

				for ($i=count($weekdays); $i < $occurrences; $i++) { 

					$start_date_obj = $sd_monday_this_week->modify('monday this week +1 week' . $st);
					$end_date_obj = $ed_monday_this_week->modify('monday this week + 1 week' . $et);

					foreach ($weekdays as $day) {

						$start_date_obj = $start_date_obj->modify($day_names[$day] . ' this week ' . $st);
						$end_date_obj = $end_date_obj->modify($day_names[$day] . ' this week ' . $et);

						$arr_repeat = $this->push_to_arr_repeat($arr_repeat, $start_date_obj, $end_date_obj);

						$i++;

					}

				}



			}

			$short_date = "D M j";
			$long_date = "l jS F @ H:i";

			$str = bss_functions::human_date($start_date, $short_date);
			$human_arr = [bss_functions::human_date($start_date, $long_date). " - " . bss_functions::human_date($end_date, "H:i") ]; 

			foreach ($arr_repeat as $r) {
				
				$str .= ", " . bss_functions::human_date($r["start"]["date"], $short_date);
				array_push($human_arr, (bss_functions::human_date($r["start"]["date"], $long_date) . " - " . bss_functions::human_date($r["end"]["date"], "H:i")) );

			}

			$arr_repeat["human"] = $str;
			$arr_repeat["human_arr"] = $human_arr;

			return $arr_repeat;

		}

		/**
		 * Convert ME Calendar date type to a PHP date in our timezone
		 * https://webnus.net/modern-events-calendar/
		 *
		 * @param json $date_json json date from ME Calendar
		 * @param string $type "start" or "end"
D		 * 
		 * @return date
		 */	

		public function build_mec_date( $date_json, $type ) {

			$string = $date_json[$type]["date"] . " " . $date_json[$type]["hour"] . ":" . str_pad($date_json[$type]["minutes"], 2, '0', STR_PAD_LEFT) . ":00 " . $date_json[$type]["ampm"];

			$date = DateTime::createFromFormat('Y-m-d g:i:s A', $string, new DateTimeZone(static::TIMEZONE));

			return $date;

		}

	}

}

?>