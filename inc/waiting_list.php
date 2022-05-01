<?php
if ( ! class_exists( 'waiting_list' ) ) {

	class waiting_list {

		public $classes_array;

		public function __construct() {

			$class_list = new class_list();

			$this->classes_array = $class_list->build_classes_array();

			
			//$this->classes_array = $this->build_classes_array();

		}

		/**
		 * prints shortcode [waiting_list_admin]
		 * Page for copying the email addresses from class waiting lists
		 *
		 * @param int $id Event ID
D		 * 
		 * @return string HTML
		 */

		public function waiting_list_admin() {

			$ht = '';

			//pre_r($this->classes_array);

			foreach ($this->classes_array as $class) {
				
				foreach ($class->class_meta as $cm) {
					
					if(isset($cm["product"]["waiting_list"][0])) {

						$ht .= '
						<div class="class-meta-item">
							<h4>' . $class->post_title . ' - ' . $cm["event"]["start"]["human"] . '</h4>
							<textarea id="waiting_list' . $class->ID . '">';
						foreach ($cm["product"]["waiting_list"] as $wl) {
							
							$ht .= $wl["email_address"] . ';&#13;&#10;';

						}
						$ht .='

							</textarea>
							<br><a href="#" onClick="copy_text(\'waiting_list' . $class->ID . '\');">Copy to clipboard</a>
							<br><a href="' . get_edit_post_link($cm["product"]["product_id"]) . '">Edit this waiting list/prduct</a>

						</div>
						';

					}
				}
			}

			$ht .= '';

			return $ht;

			
		}

		public function waiting_list_button($class, $product_id, $txt = 'Waiting list') {

			return '
			<form action="" method="post">
				<input type="submit" class="btn btn-waiting btn-warning" value="' . $txt . '">
				<input type="hidden" name="product_id" value="' . $product_id . '"/>
				<input type="hidden" name="title" value="' . $class->post_title . '"/>
				<input type="hidden" name="waiting_list_form_submitted" value="true"/>
			</form>
			';

		}

		/**
		 * Get the waiting list meta data from ACF
		 *
		 * @param int $id Woocommerce product id
D		 * 
		 * @return arr or false
		 * 
		 */

		static function get_waiting_list($id) {

			$wl = get_field("waiting_list", $id);

			if(!is_array($wl)) return false;

			return (count($wl) == 0) ? false : $wl;

		}

	}

}

?>