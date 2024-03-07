<?php

/**
 * Template Name: Payment success page
 */
get_header();

require_once './vendor/autoload.php';
$stripe = new \Stripe\StripeClient('sk_test_51NFCsSHzvcY81fLmk3CRSnwWfEecDnzpjbeq4muJk0vEgFrXHPALVEP1YhZAFZBEliyzTiZ6XBYDBjhuZIZNJUiP007eOHxVaq');

?>

<h1>this is payement success page</h1>

<?php
try {
    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
    $sDate = sanitize_text_field($_GET['start_date']);
    $eDate = sanitize_text_field($_GET['end_date']);
    $children = $_GET['children'];
    $adults = $_GET['adults'];
    $current_date = date('F j, Y'); // Format: Month Day, Year
    $new_booking = array(
        'post_title' => 'New Booking ' . $current_date . ' / ' . uniqid(),
        'post_type' => 'booking',
        'post_status' => 'publish'
    );
    $booking_id = wp_insert_post($new_booking);
    $dates_data = array(
        'starting_date' => $sDate,
        'ending_date' => $eDate
    );
    echo $sDate;
    update_field('dates', $dates_data, $booking_id);
    update_post_meta($booking_id, 'adults', $adults);
    update_post_meta($booking_id, 'children', $children);
    // $customer = $stripe->customers->retrieve($session->customer);
    // $sDate = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '';
    // $eDate = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : '';

    // if (!empty($sDate) && !empty($eDate)) {
    //     // Convert the start and end dates to DateTime objects
    //     $startDate = new DateTime($sDate);
    //     $endDate = new DateTime($eDate);

    //     // Create an array to store the categorized dates
    //     $categorizedDates = [];

    //     while ($startDate <= $endDate) {
    //         $currentDate = clone $startDate;
    //         $currentMonth = $currentDate->format('n'); // Get the month (1-12)
    //         $currentDay = $currentDate->format('j');   // Get the day of the month

    //         // Determine the category for the current date
    //         if (($currentMonth == 5 && $currentDay >= 1) || ($currentMonth == 6 && $currentDay <= 30)) {
    //             // 1st May - 30th Jun
    //             $category = "price_1NpO1wHzvcY81fLmHTqBhdHN";
    //         } elseif ($currentMonth == 7) {
    //             // 1st Jul - 30th Jul
    //             $category = "price_1NpO2HHzvcY81fLmhMtVQ8oP";
    //         } elseif ($currentMonth >= 8 && $currentMonth <= 12) {
    //             // 1st Aug - 13 Dec
    //             $category = "price_1NpO3FHzvcY81fLmZbXAWfbr";
    //         } elseif ($currentMonth == 12 && $currentDay >= 14) {
    //             // 14th Dec - 7th Jan
    //             $category = "price_1NpO3YHzvcY81fLmXaV65dXb";
    //         } elseif (($currentMonth == 1 && $currentDay >= 8) || ($currentMonth == 4 && $currentDay <= 30)) {
    //             // 8th Jan - 30th Apr
    //             $category = "price_1NpNOkHzvcY81fLmz2VRcsyq";
    //         } else {
    //             $category = "Unknown";
    //         }

    //         // Store the category in the categorizedDates array
    //         $categorizedDates[] = $category;

    //         // Move to the next day
    //         $startDate->add(new DateInterval('P1D'));
    //     }

    //     // Count the occurrences of each category
    //     $categoryCounts = array_count_values($categorizedDates);

    //     // Create the final array of arrays
    //     $resultArray = [];

    //     foreach ($categoryCounts as $category => $count) {
    //         $resultArray[] = [
    //             'price' => $category,
    //             'quantity' => $count,
    //         ];
    //     }

    //     // Print or use $resultArray as needed
    //     print_r($resultArray);
    // } else {
    //     echo "Please provide valid start and end dates.";
    // }


    echo $session;
    // echo "<h1>Thanks for your order, $customer->name!</h1>";
    http_response_code(200);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}


?>




<?php
get_footer();
?>