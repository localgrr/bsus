<?php

if ( ! class_exists( 'student_registers' ) ) {


	class student_registers {

		private $products_array;

		public function __construct() {

		}

		/**
		 * Get All orders IDs for a given product ID.
		 *
		 * @param  integer  $product_id (required)
		 * @param  array    $order_status (optional) Default is 'wc-completed'
		 *
		 * @return array
		 */

		private function get_orders_ids_by_product_id( $product_id ) {

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

		public function student_registers() {

			$pid = $_GET["pid"];

			$this->products_array = $this->get_products();

			$this->print_product_select();

			if(isset($pid)) {

				$this->print_orders_table($pid);

			}

		}

		private function print_orders_table_html($orders, $product) {

			$ht = '<table class="student-register-table" width="100%" border="1" cellpadding="3">
			<thead><tr>
				<th class="narrow"><label><input type="checkbox" class="all"> All</label></th>
				<th>Order ID</th>
				<th>Order status</th>
				<th>Date</th>
				<th class="wide">Email</th>
				<th class="wide">Name</th>
				<th class="wide">Address</th>
				<th>Address 2</th>
				<th>City</th>
				<th>Postcode</th>
				<th>Country</th>
				<th>SKU</th>
				<th>Total</th> 
				<th>Stripe fees</th> 
				<th>Payment method</th>
				<th>Customer note</th>
			</tr></thead>
			<tbody>
			';

			foreach ($orders as $i => $order) {

				$total_price = ($product->get_price() - $order->get_total_discount(false));
				$country = $order->get_billing_country();
				$method = $order->get_payment_method();
				$id = $order->get_id();
				$order_notes = implode(", ", $id);
				
				$ht .= '<tr data-row="' . $i . '">
					<td><input type="checkbox" class="check-row" data-row="' . $i . '"></td>
					<td>' . $id . '</td>
					<td>' . $order->get_status() . '</td>
					<td>' . date('d/m/Y', strtotime($order->get_date_created())) . '</td>
					<td class="email">' . $order->get_billing_email() . '</td>
					<td>' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '</td>
					<td>' . $order->get_billing_address_1() . '</td>
					<td>' . $order->get_billing_address_2() . '</td>
					<td>' . $order->get_billing_city() . '</td>
					<td>' . $order->get_billing_postcode() . '</td>
					<td>' . $country . '</td>
					<td>' . $product->get_sku(). '</td>
					<td class="total">' . $total_price . '</td>
					<td>' . $this->get_stripe_fees($total_price, $country, $method) . '</td>
					<td>' . $method . '</td>
					<td>' . $order_notes . '</td>

				</tr>';
			}

			$ht .= '</tbody></table>';

			return $ht;
		}

		private function print_orders_table($pid) {

			$order_ids = $this->get_orders_ids_by_product_id($pid);

			$product = wc_get_product($pid);

			if(!$product) return false;

			$orders = [];
			$orders_cancelled = [];

			$this->print_orders_toolbar();
			
			foreach ($order_ids as $id) {

				$order = wc_get_order( $id );

				if( ($order->get_status() == "cancelled") || ($order->get_status() == "failed")) {

					array_push($orders_cancelled, $order);


				} else {

					array_push($orders, $order);

				}

			}

			$ht = $this->print_orders_table_html($orders, $product);

			if(count($orders_cancelled)>0) {

				$ht .= '<h4 class="title-cancelled">Cancelled orders</h4>';
				$ht .= $this->print_orders_table_html($orders_cancelled, $product);

			}

			echo $ht;

		}

		private function print_orders_toolbar() {

			$ht = '
			<div class="orders-table-toolbar">
				<button class="copy-emails">Copy Selected Emails</button>
				<input type="text" class="emails">
				<label>Total <input type="text" class="grand-total"></label>
				<label>Total - 19% VAT<input type="text" class="grand-total-minus-vat"></label>
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

		private function print_product_select($args = []) {

			$pid = $_GET["pid"];

			$ht = '<div class="product-select"><label>Chose a product<br>
			<select id="product_dropdown" name="product_dropdown" onchange="document.location.href = \'?pid=\' + this.value" autocomplete="off">
				<option> -- </option>';

			foreach ($this->products_array as $pr) {

				$selected = ($pid == $pr->id) ? ' selected="selected"' : '';
				
				$ht .= '<option value="' . $pr->get_id() . '"' . $selected . '>' . $pr->get_name() . '</option>';

			}


			$ht .= '</select></label></div>';

			echo $ht;

		}

	}

}




?>