<?php

namespace Eric\ZipFilePhp\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use  ZipArchive;

class ZipFilesFilterIgnore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zip:ignore {name=appIgnore}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zip_name = $this->argument('name');

        $this->addZip($zip_name);

    }
    public function addZip($zip_name)
    {
        //需要忽略的文件
        $ignoreArr = $this->parse_ignore_file(config('zip-file-php.ignore_config'));
        //最终需要压缩的文件
        $datalist        = $this->list_dir(base_path("/"), $ignoreArr);
        $base_path_count = count(str_split(base_path())) + 1;
        $file_arr        = [];//根目录的文件
        $file_path_arr   = [];//更深的文件
        foreach ($datalist as &$item) {
            $item = substr($item, $base_path_count);
            if (strstr($item, '/')) {
                $file_path_arr[] = $item;
            } else {
                $file_arr[] = $item;
            }
        }
        $exportPath = $zip_name;
        $zip        = new ZipArchive();
        //创建压缩包
        if (!$zip->open("$exportPath.zip", ZIPARCHIVE::CREATE)) {
            $this->info("创建[$exportPath.zip]失败");
            return;
        }
        $base_path = base_path();
        foreach ($file_arr as $v) {
            if (file_exists($base_path . "/" . $v)) {
                $zip->addFile($base_path . "/" . $v, basename($base_path . "/" . $v));
            }
        }
        foreach ($file_path_arr as $k => $v) {
            $first_path = explode('/', $v);
            array_pop($first_path);
            $new_path = join("/", $first_path);
            $zip->addEmptyDir($new_path);
            $zip->addFile($base_path . "/" . $v, $v);
        }
        $zip->close();
    }

    /**
     * 获取需要压缩的文件列表
     * @param  string $dir 需要压缩的文件夹
     * @param array $ignoreArr 需要忽略的文件夹
     * @return array
     * */
    function list_dir($dir, $ignoreArr = [])
    {
        $result = array();
        if (is_dir($dir)) {
            $file_dir = scandir($dir);
            foreach ($file_dir as $file) {
                if ($file == '.' || $file == '..') {
                    continue;
                } elseif (is_dir($dir . $file)) {
                    $result = array_merge($result, $this->list_dir($dir . $file . '/', $ignoreArr));
                } elseif ($ignoreArr) {
                    if ($this->filter_ignore_file($dir . $file, $ignoreArr)) {
                        array_push($result, $dir . $file);
                    }
                } else {
                    array_push($result, $dir . $file);
                }
            }
        }
        foreach ($result as &$v) {
            $v = str_replace('\\', "/", $v);
//            $v = str_replace("\\\\", "\\", $v);
        }
        return $result;
    }
    /**
     * 判断是否需要排除
     * @param string $file 需要压缩的文件路径
     * @param array $ignoreArr 需要忽略的文件列表
     * @return boolean false 表示 需要忽略
     * */
    function filter_ignore_file($file, $ignoreArr)
    {
        $file = str_replace('\\', "/", $file);
//        $file = str_replace("\\\\", "\\", $file);
        foreach ($ignoreArr as $v) {
            if (Str::startsWith($file, $v)) {
                return false;
            }
        }
        return true;
    }
    /**
     * 获取需要排除的文件
     * 遵循ignore文件
     * @param array $fileArr 忽略的文件列表
     * @return array
     * */
    function parse_ignore_file($fileArr)
    {

        $dir = base_path();

        $matches = array();

        foreach ($fileArr as $line) {


            $line = trim($line);


            if ($line === '') {
                continue;
            } # empty line


            if (substr($line, 0, 1) == '#') {
                continue;
            } # a comment

            if (substr($line, 0, 1) == '!') { # negated glob

                $line = substr($line, 1);

                $files = array_diff(glob("$dir/*"), glob("$dir/$line"));

            } else { # normal glob

                $files = glob("$dir$line");

            }

            $matches = array_merge($matches, $files);

        }
        foreach ($matches as &$v) {
            $v = str_replace('\\', "/", $v);
        }

        return $matches;


    }

}
