<?php

return [

    'js_vendor'=>[
            'vendor/js/jquery.min.js',
            'vendor/js/bootstrap.min.js',
            'vendor/js/popper.min.js',
            'vendor/js/angular.min.js',
            'vendor/js/angular-route.min.js',
            'vendor/js/angular-animate.min.js',
            'vendor/js/angular-aria.min.js',
            'vendor/js/angular-messages.min.js',
            'vendor/js/angular-material.min.js',
            'vendor/js/pusher.min.js',
            'prototype.js'
    ],
    'css_vendor'=>[
        'vendor/css/bootstrap.min.css',
        "vendor/css/bootstrap-theme.min.css",
        "vendor/css/angular-material.min.css"
    ],
    'angular_provider'=>[
        'cPProvider',
        '$routeProvider',
        '$locationProvider',
        'routingProvider',
        '$rootScopeProvider'
    ],
    'except'=>[
        'values/user.js'
    ],

    'dynamic'=>['controller','filter','service','factory','values','directive'],
    'kpi_company_headers'=>[
        '序号 No.',
        '项目 Deskripsi',
        '单位
        Unit',
        '当月 份目标 Realisasi Bulan Berjalan',
        'Realisasi
        (% R/T)',
        '一月
        Jan',
        '二月
        Feb',
        '三月
        Mar',
        '四月
        Apr',
        '五月
        May',
        '六月
        Jun',
        '七月
        Jul',
        '八月
        Aug',
        '九月
        Sep',
        '十月
        Oct',
        '十一月
        Nov',
        '十二月
        Dec'
    ]

];
