<?php

if ( ! class_exists( 'student_registers' ) ) {


	class student_registers {

		private $table_heading;

		private $products_array;

		private $product;

		private $orders_data;

		private $orders;

		public function __construct() {

		}

		/**
		 * The init function for this class, called by shortcode [student_registers]
		 *
		 */

		public function student_registers() {

			$this->set_arrays();

			$this->print_product_select();


			$this->print_orders_table();


			if(isset($_POST["download_csv_submitted"])) $this->download_csv();

		}

		private function set_arrays() {

			$pid = $this->get_product_id_from_qs();

			$this->products_array = $this->get_products();

			if($pid) $this->product = wc_get_product($pid);

			if($this->product) {

				$orders = $orders_cancelled = [];

				$order_ids = $this->get_orders_ids_by_product_id();
				$product = $this->product;

				foreach ($order_ids as $id) {

					$order = wc_get_order( $id );

					if( ($order->get_status() == "cancelled") || ($order->get_status() == "failed")) {

						array_push($orders_cancelled, $order);


					} else {

						array_push($orders, $order);

					}

				}

				$this->orders = [

					'orders' => $orders,
					'cancelled' => $orders_cancelled,
					'data' => $this->get_orders_table_data($orders),
					'data_cancelled' => $this->get_orders_table_data($orders_cancelled),

				];

			}

		}

		private function print_orders_table() {

			$this->print_orders_toolbar();

			$ht = $this->print_orders_table_html($this->orders["orders"]);

			if(count($this->orders["cancelled"])>0) {

				$ht .= '<h4 class="title-cancelled">Cancelled orders</h4>';
				$ht .= $this->print_orders_table_html($this->orders["cancelled"]);

			}

			echo $ht;

		}

		private function get_orders_ids_by_product_id() {

			$product_id = $this->product->get_id();

		    global $wpdb;

		    $results = $wpdb->get_col("
		        SELECT order_items.order_id
		        FROM {$wpdb->prefix}woocommerce_order_items as order_items
		        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
		        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
		        WHERE posts.post_type = 'shop_order'
		        AND order_items.order_item_type = 'line_item'
		        AND order_item_meta.meta_key = '_product_id'
		        AND order_item_meta.meta_value = '$product_id'
		    ");

		    return $results;
		}

		private function get_products() {

			$query = new WC_Product_Query( array(
			    'limit' => -1,
			    'orderby' => 'date',
			    'order' => 'DESC',
			    'status' => 'publish'
			) );

			return $query->get_products();

		}

		private function download_csv() {

			ob_end_clean();

			$output = fopen('php://output', 'w');

			$th_flat = [];

			foreach ($this->table_heading as $i => $th) {

				if($i==0) continue; //we dont want the checkbox
				array_push($th_flat, $th[0]);

			}

			fputcsv($output, $th_flat);

			$orders_data = $this->orders["data"];
			if(count($this->orders["data_cancelled"])>0) $orders_data_cancelled = $this->orders["data_cancelled"];

			$this->output_csv_data_body($orders_data, $output);

			if(isset($orders_data_cancelled)) {

				fputcsv($output, []);
				fputcsv($output, ["Cancelled"]);
				$this->output_csv_data_body($orders_data_cancelled, $output);
			}

		    header('Content-Type: application/csv');
		    // tell the browser we want to save it instead of displaying it
		    header('Content-Disposition: attachment; filename="data.csv";');

		    exit;

		}

		private function output_csv_data_body($orders_data, $output) {

			foreach ($orders_data as $od) {

				$row = [];
				
				foreach ($od as $i => $o) {
					
					array_push($row, $o[0]);

				}

				fputcsv($output, $row);

			}

		}

		private function get_orders_table_heading() {

			return [
				['<label><input type="checkbox" class="all"> All</label>', 'narrow'],
				['Order ID'],
				['Order status'],
				['Date'],
				['Email','wide'],
				['Name','wide'],
				['Address','wide'],
				['Address 2'],
				['City'],
				['Postcode'],
				['Country'],
				['SKU'],
				['Total'],
				['Stripe fees'],
				['Payment method'],
				['Customer note']
			];

		}

		private function get_orders_table_data($orders) {

			$product = $this->product;
			$data = [];

			foreach ($orders as $order) {

				$total_price = ($product->get_price() - $order->get_total_discount(false));
				$country = $order->get_billing_country();
				$method = $order->get_payment_method();
				$id = $order->get_id();
				$sku = $product->get_sku();

				array_push($data, [
					
					[$id],
					[$order->get_status()],
					[date('d/m/Y', strtotime($order->get_date_created()))],
					[$order->get_billing_email(), 'email'],
					[$order->get_billing_first_name() . ' ' . $order->get_billing_last_name()],
					[$order->get_billing_address_1()],
					[$order->get_billing_address_2()],
					[$order->get_billing_city()],
					[$order->get_billing_postcode()],
					[$country],
					[$sku],
					[$total_price, 'total'],
					[$this->get_stripe_fees($total_price, $country, $method)],
					[$method],
					[$order->get_customer_note()]

				]);
			}

			return $data;
		}

		private function print_orders_table_html($orders) {

			$this->table_heading = $this->get_orders_table_heading();
			$orders_data = $this->get_orders_table_data($orders);

			$ht = '<table class="student-register-table table-responsive" width="100%" border="1" cellpadding="3">
			<thead><tr>';
				foreach ($this->table_heading as $th) {
					$class = isset($th[1]) ? ' class ="' . $th[1] . '"' : '';
					$ht .= '<th' . $class . '>' . $th[0] . '</th>';
				}
			$ht .= '
			</tr></thead>
			<tbody>
			';

			foreach ($orders_data as $i => $d) {
				
				$ht .= '<tr data-row="' . $i . '">
					<td><input type="checkbox" class="check-row" data-row="' . $i . '"></td>';

					foreach ($d as $dd) {
						$class = isset($dd[1]) ? ' class="' . $dd[1] . '"' : '';
						$ht .= '<td' . $class . '>' . $dd[0] . '</td>';
					}

				$ht .='</tr>';
			}

			$ht .= '</tbody></table>';

			return $ht;
		}



		private function print_orders_toolbar() {

			$ht = '
			<div class="orders-table-toolbar">
			<form method="post" action="" class="download-csv-form">
				<button class="copy-emails">Copy Selected Emails</button>
				<textarea class="emails" id="orders_emails"></textarea>
				<button class="download-csv">Download CSV</button>
				<label>Total <input type="number" class="grand-total"></label>
				<label>Total - 19% VAT<input type="number" class="grand-total-minus-vat"></label>
				<input type="hidden" name="download_csv_submitted" value="true">
			</form>
			</div>';

			echo $ht;
		}

		private function get_stripe_fees($price, $country, $method) {

			if(strpos($method, "stripe") === false) return 0;

			$add = 0.25;
			$eu_percent = 1.4;
			$other_percent = 2.9;

			$eu_arr = array( 'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HU', 'HR', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK' );

			$percent = (in_array($country, $eu_arr)) ? $eu_percent : $other_percent;

			return round($price * ($percent / 100),2) + $add;

		}

		private function get_product_id_from_qs() {

			return isset($_GET["pid"]) ? $_GET["pid"] : -1;

		}

		private function print_product_select($args = []) {

			$pid = $this->product->get_id() ? $this->product->get_id() : null;

			$ht = '<div class="product-select"><label>Chose a product<br>
			<select id="product_dropdown" name="product_dropdown" onchange="document.location.href = \'?pid=\' + this.value" autocomplete="off">
				<option> -- </option>';

			foreach ($this->products_array as $pr) {

				if($pid) $selected = ($pid == $pr->get_id()) ? ' selected="selected"' : '';
				
				$ht .= '<option value="' . $pr->get_id() . '"' . $selected . '>' . $pr->get_name() . '</option>';

			}


			$ht .= '</select></label></div>';

			echo $ht;

		}

	}

}




?>