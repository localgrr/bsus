<?php

/*
 * this require includes PhpSpreadsheet
 * @link https://phpspreadsheet.readthedocs.io/en/latest/
 * & woocommperce rest api 
 * @link https://woocommerce.github.io/woocommerce-rest-api-docs
 */

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

if ( ! class_exists( 'student_registers' ) ) {


	class student_registers {

		private $woo;

		private $product_array;

		private $orders_array;

		public function __construct() {


			if( $_SERVER['SERVER_NAME'] == "bsus.local") {

				$server = "http://bsus.local";
				$key = WOO_LOCAL_KEY;
				$secret = WOO_LOCAL_SECRET;

			} else {

				$server = "https://berlinstandupschool.com";
				$key = WOO_LIVE_KEY;
				$secret = WOO_LIVE_SECRET;

			}
			//
			// - srcret

			//echo $_SERVER['SERVER_NAME'];

			$this->woo = new Client(
			    $server,
			    $key, 
			    $secret,
			    [
			        'wp_api' => true, // Enable the WP REST API integration
			        'version' => 'wc/v3' // WooCommerce WP REST API version
			    ]
			);


		}

		private function get_all_woo($endpoint, $attributes = [], $pages = 20) {

			$attr = array_merge($attributes, ['page' => 1]);

			$woo = $this->woo;

			$arr = $woo->get($endpoint, $attr );

			$i = 2;

			while ($i < $pages) {

				$attr = array_merge($attributes, ['page' => $i]);
				
				$item = $woo->get($endpoint, $attr );

				if(count($item) == 0) break;

				$arr = array_merge($arr, $item);

				$i++;
			}

			return $arr;

		}

		public function student_registers() {

			

			$this->product_array = $this->get_all_woo("products", ['status' => 'publish']);

			$this->orders_array = $this->get_all_woo("orders");

			$this->print_product_select();

			$pid = $_GET["pid"];

			if(isset($pid)) {

				$this->print_orders_table($pid);

			}


		}

		private function print_orders_table($pid) {

			

			foreach ($this->product_array as $pr) {
				
				if($pr->id == $pid) {

					$orders = $this->get_orders_by_pid($pid);

				}

			}

			$ht = '<table class="student-register" width="100%" border="1" cellpadding="3">
			<thead><tr>
				<th>Order ID</th>
				<th>Date</th>
				<th>Email</th>
				<th>Name</th>
				<th>Address</th>
				<th>Address 2</th>
				<th>City</th>
				<th>Postcode</th>
				<th>SKU</th>
				<th>Total</th>
				<th>Quantity</th>
				<th>Payment method</th>
				<th>Customer note</th>
			</tr></thead>
			<tbody>
			';

			foreach ($orders["orders"] as $order) {
				
				$ht .= '<tr>
					<td>' . $order["order_id"] . '</td>
					<td>' . $order["date"] . '</td>
					<td>' . $order["customer"]->email . '</td>
					<td>' . $order["customer"]->first_name . ' ' . $order["customer"]->last_name . '</td>
					<td>' . $order["customer"]->address_1 . '</td>
					<td>' . $order["customer"]->address_2 . '</td>
					<td>' . $order["customer"]->city . '</td>
					<td>' . $order["customer"]->postcode . '</td>
					<td>' . $order["sku"] . '</td>
					<td>' . $order["total"] . '</td>
					<td>' . $order["quantity"] . '</td>
					<td>' . $order["payment_method"] . '</td>
					<td>' . $order["customer_note"] . '</td>

				</tr>';
			}

			$ht .= '</tbody></table>';

			echo $ht;

		}

		private function get_orders_by_pid($pid) {

			$orders = [];
			$orders_cancelled = [];

			foreach ($this->orders_array as $order) {
				
				foreach ($order->line_items as $line_item) {
			
					if($line_item->product_id == $pid) {

						$arr = [

							//'product_id' => 	$pid,
							'order_id' => 		$order->id,
							'quantity' => 		$line_item->quantity,
							'sku' => 			$line_item->sku,
							'date' =>			date('d/m/Y', strtotime($order->date_created)),
							'total' =>			$order->total,
							'customer' => 		$order->billing,
							'customer_note' =>	$order->customer_note,
							'payment_method' =>	$order->payment_method

						];

						if($order->status == "cancelled") { 

							array_push($orders_cancelled, $arr);

						} else {

							array_push($orders, $arr);

						}

					}

				}

			}

			return [
				'orders' => $orders,
				'cancelled' => $orders_cancelled
			];

		}

		private function print_product_select($args = []) {

			$pid = $_GET["pid"];

			$ht = '<label>Chose a product<br>
			<select id="product_dropdown" name="product_dropdown" onchange="document.location.href = \'?pid=\' + this.value" autocomplete="off">
				<option> -- </option>';

			foreach ($this->product_array as $pr) {

				$selected = ($pid == $pr->id) ? ' selected="selected"' : '';
				
				$ht .= '<option value="' . $pr->id . '"' . $selected . '>' . $pr->name . '</option>';

			}


			$ht .= '</select></label>';

			echo $ht;

		}

	}

}




?>