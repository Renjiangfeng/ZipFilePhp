# ZipFilePhp
`zip-file-php`是一个根据配置文件对文件进行压缩打包的laravel包


# 安装

`composer require renjiangfeng/zip-file-php`


# 开始使用

##### 执行命令

*  php artisan vendor:publish --force --provider="Eric\ZipFilePhp\ZipFilePhpServiceProvider" 


##### 会创建配置文件
* `confg/zip-file-php.php`

```php
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
        'index.php'
    ],
    'need_config'   => [ //需要打包压缩的文件
        '/app',
        '/config',
         'index.php'
    ]
];
```
分别有两种命令根据上面的两种配置进行打包压缩，配置文件写法基本沿用`.gitignore`文件的写法

### 1.忽略法
`php artisan zip:ignore ` 会根据 `ignore_config` 的列表忽略掉不需要打包的文件，可以自定义压缩包的名称，
`php artisan zip:ignore demo` 会打包为`demo.zip`在根目录，默认`appignore.zip`

### 2.罗列法
`php artisan zip:zip:forConfig ` 会根据 `need_config` 的罗列列表对文件进行打包,压缩包的名称同上，默认`app.zip`

