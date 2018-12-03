<?php

namespace app\common\code_doc;

/**
 * 解析方法的注释
 */
class MethodCommentParser
{
    public static function handle($comment)
    {
        $tags = self::parseTag($comment);
        $rt['titles'] = self::parseTitles($comment);
        $commentTable = $tags['table'][0] ?? '';
        $rt['tables'] = $commentTable ? self::parseRefTable($commentTable) : [];
        $rt['params'] = isset($tags['param']) ? self::parseParam($tags['param']) : [];
        $rt['return'] = [];
        if (isset($tags['return'])) {
            $rt['return'] = ReturnCommentParser::handle($tags['return'][0]);
        }
        return $rt;
    }

    /**
     * 解析注释tag
     * @param $comment
     * @return array
     */
    private static function parseTag($comment)
    {
        preg_match_all('/ \\* \\@([^\\s]+) +([\S| ]+)/', $comment, $out);
        $rt = [];
        if (!empty($out[1])) {
            foreach ($out[1] as $key => $token) {
                $rt[$token][] = $out[2][$key];
            }
        }
        return $rt;
    }

    /**
     * 解析方法注释标题及补充说明的副标题
     * @param $comment
     * @return array [标题,副标题]
     */
    private static function parseTitles($comment)
    {
        $trim = function ($str) {
            return trim($str, " \r\n\t/*");
        };
        $segs = array_filter(array_map($trim, explode("\n", $comment)));

        $title = (string)array_shift($segs);//may not be $seg[0]

        $nextLine = (string)array_shift($segs);
        $subTitle = ($nextLine && '@' != $nextLine[0]) ? $nextLine : '';

        return [$title, $subTitle];
    }

    /**
     * 解析@table标签
     * @param $tagTxt
     * @return array
     */
    private static function parseRefTable($tagTxt)
    {
        return array_filter(preg_split('/[^\w]/', $tagTxt));
    }

    /**
     * 从注释读取参数注释
     * @param array $tokens @param对应注释解析为名称
     * @return array
     */
    private static function parseParam($tags)
    {
        $rt = [];
        foreach ($tags as $tagTxt) {
            preg_match_all('/(\w+ )?\\$([^ \\$]+) +([\S\ ]+)/', $tagTxt, $out);
            if (!empty($out[2])) {
                $rt[current($out[2])] = ['type' => current($out[1]), 'title' => current($out[3])];
            }
        }
        return $rt;
    }
}