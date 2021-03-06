<?php
/**
 * User: xiang.su@qq.com
 * Date: 2018/10/13
 */

namespace app\reli\controller;

use app\common\adapter\ReliController;
use app\common\code_doc\ParseServer;
use think\App;
use Michelf\Markdown;
use think\facade\Env;

class Index extends ReliController
{
    function __construct(App $app = null)
    {
        //self::clear();
        parent::__construct($app);
    }

    public function index()
    {
        $readme_path = Env::get('app_path') . '../README.md';
        $content = Markdown::defaultTransform(file_get_contents($readme_path));
        return view('index', ['content' => $content]);
    }

    /**
     * 数据库结构,方便查看
     * @todo 根据设计原则,显示不符合设计原则的提示
     * @return \think\response\View
     */
    function listSchema()
    {
        $data = ParseServer::listSchema();
        return view('list_schema', ['data' => $data]);
    }

    /**
     * 显示服务
     */
    function listService()
    {
        $data = [
            'data' => ParseServer::listParsedService(),
            'relatedActions' => ParseServer::listRelatedAction(),
        ];
        //echo '<pre>';
        //print_r($data['relatedActions']);die;
        return view('list_service', $data);
    }

    /**
     * 显示控制器
     */
    function listController()
    {
        $data = ParseServer::listParsedController();
        return view('list_controller', ['data' => $data]);
    }

    /**
     * 清楚解析服务缓存
     */
    function clear()
    {
        ParseServer::clear();
    }

    /**
     * 根据文档展示表单
     */
    function showForm($url)
    {
        $url = urldecode($url);
        $segs = array_filter(explode('/', $url));
        list($module, $controllName, $actionName) = $segs;
        $className = "app\\$module\\controller\\$controllName";
        $rs = ParseServer::listParsedController();
        $actionInfo = $rs[$className]['methods'][$actionName] ?? [];

        $data = [
            'url' => $url,
            'actionInfo' => $actionInfo,
            'method' => get_http_method($actionName),
        ];
        return view('show_form', $data);
    }

    /**
     * 显示源代码
     * @param string $file
     */
    function listFile($file = '')
    {
        $dir = env('app_path') . 'common/';
        $menu = list_file(
            $dir,
            function ($path) {
                return strpos($path, '.php')
                    && 0 !== strpos($path, 'adapter')
                    && 0 !== strpos($path, 'middleware')
                    && 0 !== strpos($path, 'exception');
            }
        );

        // 安全和修复
        if ($file) {
            $file = str_replace('..', '', $file);//安全
            if (!strpos($file, '.')) {
                $file .= '.php';
            }
        }

        // 支持配置文件
        if (0 === strpos($file, 'config/')) {
            $dir = dirname(env('config_path')) . '/';
        }

        return view('list_file', ['menu' => $menu, 'file' => $file, 'dir' => $dir]);
    }
}