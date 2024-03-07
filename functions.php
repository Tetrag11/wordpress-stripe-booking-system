<?php
define(
	'CUSTOM_POST_TYPES',
	get_stylesheet_directory() .
		'/includes/post-types/index.php'
);



// Require Dependencies
require __DIR__ . '/includes/vendors/index.php';
// Custom Post Types
include_once CUSTOM_POST_TYPES;

require_once __DIR__ . '/vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51NFCsSHzvcY81fLmk3CRSnwWfEecDnzpjbeq4muJk0vEgFrXHPALVEP1YhZAFZBEliyzTiZ6XBYDBjhuZIZNJUiP007eOHxVaq');

// require_once './stripeIntegration/secrets.php';




/**
 * KumburaVilla functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package KumburaVilla
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */


add_action('wp_ajax_send_booking', 'send_booking');
add_action('wp_ajax_nopriv_send_booking', 'send_booking');


function send_booking()
{
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sDate = sanitize_text_field($_POST['start_date']);
		$eDate = sanitize_text_field($_POST['end_date']);
		$children = $_POST['children'];
		$adults = $_POST['adults'];

		if (!empty($sDate) && !empty($eDate)) {
			// Convert the start and end dates to DateTime objects
			$startDate = new DateTime($sDate);
			$endDate = new DateTime($eDate);

			// Create an array to store the categorized dates
			$categorizedDates = [];

			while ($startDate <= $endDate) {
				$currentDate = clone $startDate;
				$currentMonth = $currentDate->format('n'); // Get the month (1-12)
				$currentDay = $currentDate->format('j');   // Get the day of the month

				// Determine the category for the current date
				if (($currentMonth == 5 && $currentDay >= 1) || ($currentMonth == 6) || ($currentMonth == 7 && $currentDay <= 30)) {
					// 1st May - 30th Jun
					$category = "price_1NpO1wHzvcY81fLmHTqBhdHN";
				} elseif ($currentMonth == 7) {
					// 1st Jul - 30th Jul
					$category = "price_1NpO2HHzvcY81fLmhMtVQ8oP";
				} elseif ($currentMonth >= 8 && $currentMonth <= 12) {
					if ($currentMonth == 12 && $currentDay > 13) {
						$category = "price_1NpO3YHzvcY81fLmXaV65dXb";
					} else {
						$category = "price_1NpOH7HzvcY81fLmBBrhYO2N";
					}
				} elseif (($currentMonth == 12 && $currentDay >= 14) || ($currentMonth == 1 && $currentDay <= 7)) {
					// 14th Dec - 7th Jan
					$category = "price_1NpO3YHzvcY81fLmXaV65dXb";
				} elseif (($currentMonth == 1 && $currentDay >= 8) || ($currentMonth == 4 && $currentDay <= 30)) {
					// 8th Jan - 30th Apr
					$category = "price_1NpNOkHzvcY81fLmz2VRcsyq";
				} else {
					$category = "Unknown";
				}

				// Store the category in the categorizedDates array
				$categorizedDates[] = $category;

				// Move to the next day
				$startDate->add(new DateInterval('P1D'));
			}

			// Count the occurrences of each category
			$categoryCounts = array_count_values($categorizedDates);

			// Create the final array of arrays
			$resultArray = [];

			foreach ($categoryCounts as $category => $count) {
				$resultArray[] = [
					'price' => $category,
					'quantity' => $count,
				];
			}

			// Print or use $resultArray as needed
			\Stripe\Stripe::setApiKey('sk_test_51NFCsSHzvcY81fLmk3CRSnwWfEecDnzpjbeq4muJk0vEgFrXHPALVEP1YhZAFZBEliyzTiZ6XBYDBjhuZIZNJUiP007eOHxVaq');
			// header('Content-Type: application/json');

			$YOUR_DOMAIN = 'http://localhost/kumburavilla';

			$checkout_session = \Stripe\Checkout\Session::create([
				'line_items' => $resultArray,
				'mode' => 'payment',
				'success_url' => $YOUR_DOMAIN . "/payment-success-page?session_id={CHECKOUT_SESSION_ID}&start_date=$sDate& end_date=$eDate&children=$children&adults=$adults",
				'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
			]);

			// header("HTTP/1.1 303 See Other");
			// header("Location: " . $checkout_session->url);
			echo ($checkout_session->url);
			exit();
		} else {
			echo "Please provide valid start and end dates.";
		}


		// echo ('success');
		// exit();


	}
}





/**
 * Enqueue scripts and styles.
 */
function kumburavilla_scripts()
{
	wp_enqueue_script('jquery');
	wp_enqueue_style('jquery-ui-datepicker-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('kumburavilla-style', get_stylesheet_uri(), array(), _S_VERSION);
	wp_style_add_data('kumburavilla-style', 'rtl', 'replace');
	// wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);

	// Enqueue your custom script
	// wp_enqueue_script('custom-checkout', get_template_directory_uri() . '/stripeIntegration/checkout.js', array('jquery'), null, true);

	wp_enqueue_script('kumburavilla-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'kumburavilla_scripts');



function get_booking_dates()
{
	$booking_posts = get_posts(array('post_type' => 'booking'));
	$dates = array();

	foreach ($booking_posts as $booking) {
		$dates_data = get_field('dates', $booking->ID); // Retrieve the entire "dates" group field

		if ($dates_data) {
			$starting_date = isset($dates_data['starting_date']) ? $dates_data['starting_date'] : '';
			$ending_date = isset($dates_data['ending_date']) ? $dates_data['ending_date'] : '';

			$dates[] = array(
				'starting_date' => $starting_date,
				'ending_date' => $ending_date
			);
		}
	}

	return rest_ensure_response($dates);
}

add_action('rest_api_init', function () {
	register_rest_route('custom/v1', '/booking-dates', array(
		'methods' => 'GET',
		'callback' => 'get_booking_dates',
	));
});

function stripe_init()
{
	function calculateOrderAmount(array $items): int
	{
		// Replace this constant with a calculation of the order's amount
		// Calculate the order total on the server to prevent
		// people from directly manipulating the amount on the client
		return 1400;
	}

	header('Content-Type: application/json');

	try {
		// retrieve JSON from POST body
		$jsonStr = file_get_contents('php://input');
		$jsonObj = json_decode($jsonStr);

		// Create a PaymentIntent with amount and currency
		$paymentIntent = \Stripe\PaymentIntent::create([
			'amount' => calculateOrderAmount($jsonObj->items),
			'currency' => 'eur',
			'automatic_payment_methods' => [
				'enabled' => true,
			],
		]);

		$output = [
			'clientSecret' => $paymentIntent->client_secret,
		];

		echo json_encode($output);
	} catch (Error $e) {
		http_response_code(500);
		echo json_encode(['error' => $e->getMessage()]);
	}
}

add_action('rest_api_init', function () {
	register_rest_route('custom/v1', '/create', array(
		'methods' => 'POST',
		'callback' => 'stripe_init',
	));
});
