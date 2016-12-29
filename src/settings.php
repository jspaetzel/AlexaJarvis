<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'determineRouteBeforeAppMiddleware' => true, // make middleware aware of routes
        'addContentLengthHeader' => false, // allow content outside the slim response object, such as var_dump and echo
    ],
];
