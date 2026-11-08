<?php

declare(strict_types=1);

namespace Youvision\ThinkWhoops;

use think\exception\Handle as BaseHandle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;
use Throwable;

/**
 * ThinkPHP 8 异常处理器
 *
 * 在调试模式且 whoops.enable 开启时接管异常渲染，
 * 其余情况仍交回框架默认处理器。
 */
class Handle extends BaseHandle
{
    public function render(Request $request, Throwable $e): Response
    {
        if (!$this->shouldUseWhoops($e)) {
            return parent::render($request, $e);
        }

        if ($this->shouldReturnJson($request)) {
            return $this->renderJson($e);
        }

        try {
            $content = $this->app->whoops->render($e, $request);
        } catch (Throwable $renderException) {
            // Whoops 渲染失败时回退到框架默认处理器
            return parent::render($request, $e);
        }

        $response = Response::create($content, 'html', $this->getStatusCode($e));

        if ($e instanceof HttpException) {
            $response->header($e->getHeaders());
        }

        return $response;
    }

    protected function shouldUseWhoops(Throwable $e): bool
    {
        if (!$this->app->isDebug() || $this->app->runningInConsole()) {
            return false;
        }

        if (!$this->app->config->get('whoops.enable', true)) {
            return false;
        }

        if ($e instanceof HttpResponseException) {
            return false;
        }

        foreach ((array) $this->app->config->get('whoops.except', []) as $class) {
            if (is_string($class) && $class !== '' && $e instanceof $class) {
                return false;
            }
        }

        return true;
    }

    protected function shouldReturnJson(Request $request): bool
    {
        if ($this->app->config->get('whoops.ajax', false)) {
            return false;
        }

        return $this->isAjaxRequest($request);
    }

    protected function isAjaxRequest(Request $request): bool
    {
        if ($request->isAjax() || $request->isJson()) {
            return true;
        }

        $accept = (string) $request->header('accept', '');

        if (stripos($accept, 'application/json') !== false || stripos($accept, 'text/json') !== false) {
            return true;
        }

        $userAgent = (string) $request->header('user-agent', '');

        if (stripos($userAgent, 'postman') !== false) {
            return true;
        }

        return $request->header('sec-fetch-mode') === 'cors';
    }

    protected function renderJson(Throwable $e): Response
    {
        $response = Response::create($this->convertExceptionToArray($e), 'json');

        if ($e instanceof HttpException) {
            $response->header($e->getHeaders());
        }

        return $response->code($this->getStatusCode($e));
    }

    protected function getStatusCode(Throwable $e): int
    {
        return $e instanceof HttpException ? $e->getStatusCode() : 500;
    }
}