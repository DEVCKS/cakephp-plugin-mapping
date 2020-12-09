<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'Mapping',
    ['path' => '/mapping'],
    function (RouteBuilder $routes) {
        $routes->fallbacks(DashedRoute::class);
        $routes->get('/cpv', ['controller' => 'NormalizedTaxonomies', 'action' => 'indexCpv']);
        $routes->get('/nacre', ['controller' => 'NormalizedTaxonomies', 'action' => 'indexNacre']);
        $routes->get('/naf', ['controller' => 'NormalizedTaxonomies', 'action' => 'indexNaf']);
    }
);
