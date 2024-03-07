<?php

/**
 * Template Name: home Page
 */

get_header();
require_once './vendor/autoload.php';
// require_once '../secrets.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['submit'])) {
    }


    \Stripe\Stripe::setApiKey('sk_test_51NFCsSHzvcY81fLmk3CRSnwWfEecDnzpjbeq4muJk0vEgFrXHPALVEP1YhZAFZBEliyzTiZ6XBYDBjhuZIZNJUiP007eOHxVaq');
    // header('Content-Type: application/json');

    $YOUR_DOMAIN = 'http://localhost:4242';

    $checkout_session = \Stripe\Checkout\Session::create([
        'line_items' => [[
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            'price' => 'price_1Nckr5HzvcY81fLmKEgNESq6',
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.html',
        'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
    ]);

    echo $checkout_session->url;

    // header("HTTP/1.1 303 See Other");
    // header("Location: " . $checkout_session->url);
}



?>

this is home page

<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" id="booking-form">

    <input type="text" id="date" name="date" hidden>
    <input type="number" name="adults" id="adults" placeholder="adults" min="0" max="100">
    <input type="number" name="children" id="children" placeholder="children" min="0" max="100">

    <input type="text" id="startDate" name="start_date" required autocomplete="off">
    <input type="text" id="endDate" name="end_date" required autocomplete="off">

    <input type="submit">

</form>

<form method="POST">
    <button name="submit" type="submit" id="checkout-button">Checkoute</button>
</form>


<?php
do_shortcode('[simpay id="67"]');

?>


<div class="container">
    <h3> Highlight Particular Dates in JQuery UI Datepicker </h3>

    <!-- <div id="calendar"> </div> -->

</div>

<?php
// Fetch dates from your custom post type
$booking_posts = get_posts(array('post_type' => 'booking'));

// Prepare an array to hold the event dates
$event_dates = array();
foreach ($booking_posts as $booking) {
    $dates = get_field('dates', $booking->ID);
    if ($dates) {
        $event_dates[] = $dates;
    }
}
?>



<script>
    // Contact Emai;

    jQuery(document).ready(function($) {


        $('#booking-form').submit(function(e) {

            e.preventDefault(); // Prevent form submission
            const adults = jQuery("#adults").val();
            const children = jQuery("#children").val();

            if ((adults == 0 && children == 0) || (adults == '' && children == '')) {
                window.alert('Fields are empty');
                return;
            }

            var formData = $(this).serialize();
            formData += '&action=send_booking';
            $.ajax({
                type: 'POST',
                url: $('#booking-form').attr('action'), // WordPress AJAX URL
                data: formData,
                success: function(response) {

                    window.location.href = response;
                    // jQuery('#date').val('');
                    // jQuery('#adults').val(null);
                    // jQuery('#children').val(null);
                    // location.reload();

                }
            });
        })


        jQuery.ajax({
            url: '/kumburavilla/wp-json/custom/v1/booking-dates',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Initialize the eventDates object

                var eventDates = {};

                // Populate eventDates with the fetched dates
                data.forEach(function(dateRange) {
                    // Convert the date strings to Date objects
                    var startingDate = new Date(dateRange.starting_date.split('/').reverse().join('/'));
                    var endingDate = new Date(dateRange.ending_date.split('/').reverse().join('/'));

                    // Populate eventDates with the range of dates
                    var currentDate = new Date(startingDate);
                    while (currentDate <= endingDate) {
                        eventDates[currentDate] = currentDate.toString();
                        currentDate.setDate(currentDate.getDate() + 1);
                    }
                });

                // Initialize the Datepicker with the eventDates
                // jQuery('#calendar').datepicker({
                //     minDate: 0, // 0 means today, any positive integer means future days
                //     beforeShowDay: function(date) {
                //         var highlight = eventDates[date];
                //         if (highlight) {
                //             return [true, "event", highlight];
                //         } else {
                //             return [true, '', ''];
                //         }
                //     },
                //     onSelect: function(dateText, inst) {
                //         var selectedDate = new Date(dateText);
                //         // var formattedDate = (selectedDate.getMonth() + 1) + '/' + selectedDate.getDate() + '/' + selectedDate.getFullYear();
                //         var formattedDate = (inst.currentMonth + 1) + '/' + inst.currentDay + '/' + inst.currentYear;

                //         jQuery('#date').val(formattedDate);
                //         console.log(formattedDate);
                //     }
                // });


                jQuery(function() {
                    var startDateInput = jQuery("#startDate");
                    var endDateInput = jQuery("#endDate");

                    if (startDateInput === '' || endDateInput === "") {
                        window.alert('please select the dates');
                        return;
                    }

                    startDateInput.datepicker({
                        minDate: 0,
                        numberOfMonths: 2,
                        dateFormat: "mm/dd/yy",
                        beforeShowDay: function(date) {
                            var highlight = eventDates[date];
                            if (highlight) {
                                return [false, "event-disabled", "Highlighted date (disabled)"];
                            } else {
                                return [true, '', ''];
                            }
                        },
                        onSelect: function(selectedDate) {
                            var instance = startDateInput.data("datepicker");
                            var date = jQuery.datepicker.parseDate(instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                            endDateInput.datepicker("option", "minDate", date);
                            console.log(startDateInput.val());
                        }
                    });

                    endDateInput.datepicker({
                        minDate: 0,
                        numberOfMonths: 2,
                        dateFormat: "mm/dd/yy",
                        beforeShowDay: function(date) {
                            var highlight = eventDates[date];
                            if (highlight) {
                                return [false, "event-disabled", "Highlighted date (disabled)"];
                            } else {
                                return [true, '', ''];
                            }
                        },
                        onSelect: function(selectedDate) {
                            var instance = endDateInput.data("datepicker");
                            var date = jQuery.datepicker.parseDate(instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                            startDateInput.datepicker("option", "maxDate", date);

                            console.log(endDateInput.val());

                        }
                    });
                });



            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });




    });
</script>

<?php
get_footer();
?>