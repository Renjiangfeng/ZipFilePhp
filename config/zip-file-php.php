<?php
return [
    //以下配置只会用到一种
    'ignore_config' => [//不需要打包压缩的文件
        "/node_modules",
        "/public",
        "/storage/*.key",
        "/vendor",
        ".env",
        "/.idea",
        "/packages",
        "/.git",
    ],
    'need_config'   => [ //需要打包压缩的文件
        '/app',
        '/config'
    ]
];
