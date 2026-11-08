<?php

declare(strict_types=1);

namespace Youvision\ThinkWhoops;

use think\exception\Handle as ThinkHandle;
use think\Service as BaseService;

/**
 * ThinkPHP 8 服务注册
 *
 * composer.json 通过 extra.think.services 自动发现本服务，
 * 负责合并默认配置并把 Whoops 与异常处理器注册到容器。
 */
class Service extends BaseService
{
    public function register(): void
    {
        $default = require dirname(__DIR__) . '/config/whoops.php';
        $current = (array) $this->app->config->get('whoops', []);

        $this->app->config->set(array_replace_recursive($default, $current), 'whoops');

        $this->app->bind('whoops', Whoops::class);
        $this->app->bind(ThinkHandle::class, Handle::class);
    }

    public function boot(): void
    {
    }
}