<?php

namespace app\common\code_doc;

/**
 * 类解析类
 * 解析类方法标题,参数,注释,及控制器与服务调用关系
 */
class ClassParser
{
    /**
     * 解析入口
     * @param \ReflectionClass $class
     * @return array
     */
    public static function handle(\ReflectionClass $class)
    {
        $rt = [];
        $rt['fileName'] = $class->getFileName();
        $rt['shortName'] = $class->getShortName();
        $rt['lowerName'] = classtolower($rt['shortName']);
        $rt['isController'] = stripos($class->getNamespaceName(), '\\controller');

        foreach ($class->getMethods(\ReflectionMethod :: IS_PUBLIC) as $method) {
            $methodName = $method->getName();
            if ('__construct' == $methodName or '__'==substr($methodName,0,2)) {
                continue;
            }
            $rt['methods'][$methodName] = self::parseMethod($method, $rt);
        }

        return $rt;
    }

    /**
     * 解析方法
     * @return array [source:源代码信息,titles:方法标题和说明,params:参数,return:返回]
     */
    private static function parseMethod(\ReflectionMethod $method, $classInfo)
    {
        $rt = [];
        $rt['source'] = [
            'startLine' => $method->getStartLine(),
            'endLine' => $method->getEndLine(),
            'fileName' => $method->getFileName(),
            'comment' => $method->getDocComment(),
        ];

        if ($classInfo['isController']) {
            $rt['calledService'] = self::parseCalledService($rt['source']);
        }

        $parsedComment = MethodCommentParser::handle($rt['source']['comment']);
        $rt['titles'] = $parsedComment['titles'];

        $refTables=SchemaFieldReader::vaildTableName($parsedComment['tables']);
        if(!$refTables){
            $refTables=SchemaFieldReader::vaildTableName($classInfo['lowerName']);
        }

        $params = self::getMethodParameters($method);
        if ($params) {
            $params = self::fixParamField($params, $parsedComment['params'], $refTables);
        }
        $rt['params'] = $params;

        $return = $parsedComment['return'];
        if (!empty($return['fields'])) {
            $return['fields'] = self::fixReturnField($return['fields'], $refTables);
        }
        $rt['return'] = $return;

        return $rt;
    }

    /**
     * 解析对应代码所调用的服务
     * @param array $sourceInfo 代码信息
     * @return array
     */
    private static function parseCalledService($sourceInfo)
    {
        $start = $sourceInfo['startLine'];
        $lenth = $sourceInfo['endLine'] - $start;
        $actionSource = join("\n", array_slice(file($sourceInfo['fileName']), $start, $lenth));
        $pattern = '/call_service[\\s]*\([\\s]*[\\\'|\\"]([^\\\'\\"]+)/';
        preg_match_all($pattern, $actionSource, $out);

        $rt = [];
        if (!empty($out[1])) {
            $rt = $out[1];
        }
        return $rt;
    }

    /**
     * 解析方法实际参数
     * 为了后续文档注释或数据库字段相互补充
     */
    private static function getMethodParameters(\ReflectionMethod $method)
    {
        $rt = [];
        $parameters = $method->getParameters();

        foreach ($parameters as $key => $item) {
            $name = $item->getName();
            $rt[$name] = [];
            if ($item->isDefaultValueAvailable()) {
                $rt[$name]['default'] = $item->isDefaultValueAvailable() ? $item->getDefaultValue() : false;
            }
        }
        return $rt;
    }

    /**
     * 修复补充参数字段
     * @param array $params 代码形参
     * @param array $paramComment 注释的参数
     * @param array $rsTables 涉及的表
     * @return mixed
     */
    private static function fixParamField($params, $paramComment, $refTables)
    {
        $hasUnComment = array_diff_key($params, $paramComment);
        if ($hasUnComment) {
            $info = SchemaFieldReader::listFieldBySchema($refTables);
            $paramComment = array_merge($info, $paramComment);
        }

        foreach ($params as $key => &$param) {
            if (isset($paramComment[$key])) {
                $param['type'] = $paramComment[$key]['type'];
                $param['title'] = $paramComment[$key]['title'];
            }
        }

        return $params;
    }

    /**
     * 修复return字段信息
     * @param $fields 返回字段
     * @param $refTables 涉及的表
     */
    private static function fixReturnField($fields, $refTables)
    {
        $info = SchemaFieldReader::listFieldBySchema($refTables);
        foreach ($fields as $filedName => &$title) {
            if (empty($title) && isset($info[$filedName])) {
                $title = $info[$filedName]['title'];
            }
        }
        return $fields;
    }
}