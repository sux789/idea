<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
const SAFE_PATH=__DIR__.'/../application/common';


isset($_GET['file']) && showFile(SAFE_PATH .'/'. trim($_GET['file'],'/.'));
$lists = printDir(SAFE_PATH);


/**
 * @param string $path
 * @return array
 */
function listDir(string $path, $filter_func = null): array
{
    $rt = [];
    $len = strlen($path);
    if (is_dir($path)) {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        if (is_callable($filter_func)) {
            foreach ($iterator as $info) {
                $path = substr($info->getPathname(), $len, 100);
                if ($filter_func($path)) {
                    $rt[] = $path;
                }
            }
        } else {
            foreach ($iterator as $info) {
                $rt[] = substr($info->getPathname(), $len, 100);
            }
        }
    }
    return $rt;
}

function printDir($dir)
{
    $filter_func = function ($path) {
        return strstr($path, '.php');
    };
    $rs = listDir($dir, $filter_func);
    $rs = array_map('encodeHtml', $rs);
    return $rs;
}

function encodeHtml($file)
{
    return "<a class=file_source href=#$file>$file</a>";
}

function showFile($file)
{

    if (utf8_check(file_get_contents($file)) > 2) {
        header("Content-type:text/html;charset=gbk");
    } else {
        header("Content-type:text/html;charset=utf-8");
    }

    highlight_file($file);
    die;

}

function utf8_check($text)
{
    $utf8_bom = chr(0xEF) . chr(0xBB) . chr(0xBF);

    // BOM头检查
    if (strstr($text, $utf8_bom) === 0)
        return 1;

    $text_len = strlen($text);

    // UTF-8是一种变长字节编码方式。对于某一个字符的UTF-8编码，如果只有一个字节则其最高二进制位为0；
    // 如果是多字节，其第一个字节从最高位开始，连续的二进制位值为1的个数决定了其编码的位数，其余各字节均以10开头。
    // UTF-8最多可用到6个字节。
    //
    // 如表：
    // < 0x80 1字节 0xxxxxxx
    // < 0xE0 2字节 110xxxxx 10xxxxxx
    // < 0xF0 3字节 1110xxxx 10xxxxxx 10xxxxxx
    // < 0xF8 4字节 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
    // < 0xFC 5字节 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx
    // < 0xFE 6字节 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx

    $bad = 0; // 不符合utf8规范的字符数
    $good = 0; // 符号utf8规范的字符数

    $need_check = 0; // 遇到多字节的utf8字符后，需要检查的连续字节数
    $have_check = 0; // 已经检查过的连续字节数

    for ($i = 0; $i < $text_len; $i++) {
        $c = ord($text[$i]);

        if ($need_check > 0) {
            $c = ord($text[$i]);
            $c = ($c >> 6) << 6;

            $have_check++;

            // 10xxxxxx ~ 10111111
            if ($c != 0x80) {
                $i -= $have_check;
                $need_check = 0;
                $have_check = 0;
                $bad++;
            } else if ($need_check == $have_check) {
                $need_check = 0;
                $have_check = 0;
                $good++;
            }

            continue;
        }

        if ($c < 0x80)      // 0xxxxxxx
            $good++;
        else if ($c < 0xE0) // 110xxxxx
            $need_check = 1;
        else if ($c < 0xF0) // 1110xxxx
            $need_check = 2;
        else if ($c < 0xF8) // 11110xxx
            $need_check = 3;
        else if ($c < 0xFC) // 111110xx
            $need_check = 4;
        else if ($c < 0xFE) // 1111110x
            $need_check = 5;
        else
            $bad++;
    }

    if ($bad == 0)
        return 2;
    else if ($good > $bad)
        return 3;
    else
        return 4;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>sux list files</title>
    <style>
        .box-left {
            float: left;
            width: 500px;
            /*height: 300px;*/
            background-color: #e8e8e8;
            word-wrap: break-word;
        }

        .box-right {
            background-color: #f6f6f6;
            margin-left: 510px;

        }

        .cur {
            font-weight: 900;
            color: #ff0000;
        }

        a {
            text-decoration: none
        }
    </style>
</head>
<body>
<div class="box">
    <div class="box-left">
        <?= join("\n<br>", $lists) ?>
    </div>
    <div class="box-right">

    </div>
</div>
</body>
</html>
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script>
    $(function () {
        $('.file_source').on('click', function () {
            $boxRight = $('.box-right');
            $boxRight.load('?file=' + $(this).text());
            $('.file_source.cur').removeClass("cur");
            $boxRight.offset({top: $(document).scrollTop()})
            $(this).addClass("cur");

        })
    })
</script>
