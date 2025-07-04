<?php

use Illuminate\Support\Facades\Route;

/**
 * Define a route for the root URL ('/').
 * When accessed, this route returns the 'welcome' view.
 *
 * Note: To redirect to '/admin', update the route logic accordingly.
 */
Route::get('/', function () {
    return redirect('/admin');
});
