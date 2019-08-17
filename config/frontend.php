<?php

return [

    'js_vendor'=>[
            '/vendor/js/jquery.min.js',
            '/vendor/js/bootstrap.min.js',
            '/vendor/js/popper.min.js',
            '/vendor/js/angular.min.js',
            '/vendor/js/angular-route.min.js',
            '/vendor/js/angular-animate.min.js',
            '/vendor/js/angular-aria.min.js',
            '/vendor/js/angular-messages.min.js',
            '/vendor/js/angular-material.min.js',
            '/prototype.js'
    ],
    'css_vendor'=>[
        '/vendor/css/bootstrap.min.css',
        "/vendor/css/bootstrap-theme.min.css",
        "/vendor/css/angular-material.min.css"
    ],
    'angular_provider'=>['cPProvider','$routeProvider','$locationProvider','routingProvider','$rootScopeProvider'],
    'except'=>[
        'values/user.js'
    ],

    'dynamic'=>['controller','filter','service','factory','values','directive']

];
