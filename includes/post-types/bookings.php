<?php

use PostTypes\PostType;
// use PostTypes\Taxonomy;

$booking = new PostType('Booking');

$booking->options([
    'has_archive'   => true,
    'show_in_rest'  => true
]);










$booking->register();
