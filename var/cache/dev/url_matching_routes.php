<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/health' => [[['_route' => 'api_health', '_controller' => 'App\\Controller\\HealthController::health'], null, ['GET' => 0], null, false, false, null]],
        '/api/restaurantes' => [
            [['_route' => 'app_restaurante_index', '_controller' => 'App\\Controller\\RestauranteController::index'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'app_restaurante_create', '_controller' => 'App\\Controller\\RestauranteController::create'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/doc' => [[['_route' => 'app.swagger_ui', '_controller' => 'nelmio_api_doc.controller.swagger_ui'], null, ['GET' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/restaurantes/([^/]++)(?'
                    .'|(*:71)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        71 => [
            [['_route' => 'app_restaurante_show', '_controller' => 'App\\Controller\\RestauranteController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'app_restaurante_update', '_controller' => 'App\\Controller\\RestauranteController::update'], ['id'], ['PUT' => 0], null, false, true, null],
            [['_route' => 'app_restaurante_delete', '_controller' => 'App\\Controller\\RestauranteController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
