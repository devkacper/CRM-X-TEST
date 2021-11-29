<?php

$NS = MODULES_NS . 'Importer\Http\Controllers\\';

$router->name('importers.')->group(function () use ($router, $NS) {
    $router->get('importers', $NS.'ImporterController@index');
    $router->post('importers', $NS.'ImporterController@store');
});
