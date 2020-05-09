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
`php artisan zip:forConfig ` 会根据 `need_config` 的罗列列表对文件进行打包,压缩包的名称同上，默认`app.zip`

###备注 
请确保PHP开启ZipArchive类，能实现压缩解压功能
####Windows环境：
>Windows环境：
> * 首先需要从官网上下载，下载地址 https://windows.php.net/downloads/pecl/releases/zip/
> * 打开官网列表后需要查找适合自己的PHP版本和系统的zip，我的PHP版本是7.1的，这里我选择的版本号是1.13.5
> * 下载完后解压，把里面的php_zip.dll文件放到PHP的扩展文件夹里  `Linux`可使用`php -i | grep extension_dir`找到扩展目录 `Windows`一般在PHP安装目录下的`ext`子目录中
> * 把`php_zip.dll`文件放进去后，打开PHP的配置文件`php.ini`，添加`extension=php_zip.dll`或者取消`extension=php_zip.dll`前的
>`:`，保存后，重启apache/nginx服务器
