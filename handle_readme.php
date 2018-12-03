<?php
# 处理导出 README.md
$lines=file('./README.md');
$lines=array_map('format',$lines);
$lines=array_filter($lines);
$content=join("\n",$lines);
file_put_contents('./README.md',$content) ;

/**
 * 格式化去空行
 * @param $line
 * @return string
 */
function format($line){
    if(!ltrim($line,"* > \n\r")){
        $line='';
    }
    $line=trim($line);
    if($line){
        $line.="  ";
    }
    $line=addLinke($line);
    return $line;
}

function addLinke($line){
    return preg_replace('/[^\\[\\(](http[^\\n ]+\w)/','[\\1](\\1)',$line);
}