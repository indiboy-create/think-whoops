<?php

declare(strict_types=1);

namespace Youvision\ThinkWhoops;

use think\App;
use think\Config;
use think\Request;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Whoops 渲染入口
 *
 * 负责把 ThinkPHP 的请求信息附加到错误页面，并以字符串方式返回渲染结果，
 * 由框架统一生成 Response，避免 Whoops 直接输出内容造成重复响应。
 */
class Whoops
{
    protected const DEFAULT_OPTIONS = [
        'enable' => true,
        'editor' => '',
        'title'  => '系统发生内部错误',
        'tables' => true,
        'ajax'   => false,
        'except' => [],
    ];

    protected Run $run;

    protected array $options;

    public function __construct(Run $run, Config $config)
    {
        $this->run     = $run;
        $this->options = array_replace(self::DEFAULT_OPTIONS, (array) $config->get('whoops', []));
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->run, $name], $arguments);
    }

    /**
     * 渲染异常页面并返回 HTML 字符串
     */
    public function render(Throwable $e, ?Request $request = null): string
    {
        $handler = $this->makeHandler($request);

        $this->run->clearHandlers();
        $this->run->pushHandler($handler);
        $this->run->writeToOutput(false);
        $this->run->allowQuit(false);

        return (string) $this->run->handleException($e);
    }

    protected function makeHandler(?Request $request): PrettyPageHandler
    {
        $handler = new PrettyPageHandler();

        $title = trim((string) ($this->options['title'] ?? ''));

        if ($title !== '') {
            $handler->setPageTitle($title);
        }

        $editor = trim((string) ($this->options['editor'] ?? ''));

        if ($editor !== '') {
            try {
                $handler->setEditor($editor);
            } catch (Throwable $e) {
                // 编辑器标识非法时忽略，避免异常页面渲染失败
            }
        }

        if ($request && ($this->options['tables'] ?? true)) {
            $handler->addDataTable('ThinkPHP Application', $this->applicationData($request));
        }

        return $handler;
    }

    protected function applicationData(Request $request): array
    {
        return [
            'Version'      => App::VERSION,
            'URI'          => $request->url(true),
            'Request URI'  => $request->url(),
            'Path Info'    => $request->pathinfo(),
            'Query String' => $request->query() ?: '<none>',
            'HTTP Method'  => $request->method(),
            'Base URL'     => $request->baseUrl(),
            'Scheme'       => $request->scheme(),
            'Port'         => $request->port(),
            'Host'         => $request->host(),
            'Client IP'    => $request->ip(),
        ];
    }
}