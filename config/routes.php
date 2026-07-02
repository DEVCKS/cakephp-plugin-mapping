<?php
use Cake\Routing\Route\DashedRoute;
$routes->plugin(
    'Mapping',
    ['path' => '/'],
    function ($routes) {
        $routes->fallbacks(DashedRoute::class);
        $routes->get('/cpv', ['controller' => 'NormalizedTaxonomies', 'action' => 'indexCpv']);
        $routes->get('/nacre', ['controller' => 'NormalizedTaxonomies', 'action' => 'indexNacre']);
        $routes->get('/naf', ['controller' => 'NormalizedTaxonomies', 'action' => 'indexNaf']);
    }
);
