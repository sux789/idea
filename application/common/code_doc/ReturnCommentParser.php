<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/21
 */

namespace app\common\code_doc;

/**
 * 对return注释解析用于自动生成文档
 * @todo 数据库中没有字段注释，已经有的字段可以分析测试环境日志自动填充
 */
class ReturnCommentParser
{
    public static function handle($comment){
        $segs = explode(" ", trim($comment));
        $type = array_shift($segs);
        $content = join($segs);//字段注释

        $rt['type']=$type;
        if ('array' == $type && $content) {
            list($isMulti,$fields)=self::parseArray($content);
            $rt['isMulti']=$isMulti;
            $rt['fields']=$fields;
        }
        return $rt;
    }

    /**
     * 解析数组文本
     * @param $content
     * @return array [$isMulti,$fields]
     */
    private static function parseArray($content){
        $isMulti = false;
        preg_match_all('/\\[\\[([^(\\]\\])]+)/', $content, $out);
        $fieldTxt = $out[1] ?? '';
        if ($fieldTxt) {
            $isMulti = true;
        } else {
            preg_match_all('/\\[([^(\\])]+)/', $content, $out);
            $fieldTxt = $out[1] ?? '';
        }

        $fields = [];
        if (!empty($fieldTxt[0])) {
            $fields = self::parseReturnField($fieldTxt[0]);
        }

        return [$isMulti,$fields,];
    }

    /**
     * 简单解析文本到为字段信息
     * @todo 字段名称出现逗号需要补充到上一个字段
     * @param string $fieldTxt @return原始注释字段内容
     * @return array [filedName=>title]
     */
    private static function parseReturnField($fieldTxt)
    {
        $segs = explode(',', $fieldTxt);
        $segs=array_filter(array_map('trim',$segs));
        $rt = [];
        foreach ($segs as $filed) {
            $fieldInfo = preg_split('/[^\w]/', $filed);
            $name = array_shift($fieldInfo);
            if($name && 0===strpos($filed,$name)){
                $rt[$name]=trim(substr($filed,strlen($name))," :");
            }
        }
        return $rt;
    }

}