<?php

require_once(__DIR__ . '/vendor/autoload.php');
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

$app = new Application();

$app->post('provider-state-setup', function () {
    return new JsonResponse();
});

$app->get('recipes/{id}', function (Application $app, $id) {
    if ($id == '1') {
        return new JsonResponse(['id' => $id, 'title' => 'Cheese-Burger']);
    }

    $app->abort(404, 'Recipe not found');
});

$app->run();