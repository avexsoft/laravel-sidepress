<?php

Route::get('{wordpress_uri}', WordpressRouter::class)->where('wordpress_uri', '.*');
Route::post('{wordpress_uri}', WordpressRouter::class)->where('wordpress_uri', '.*');
