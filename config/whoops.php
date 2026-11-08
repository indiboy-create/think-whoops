<?php

return [
    // 是否启用 Whoops 接管异常，仅在 app_debug 开启时生效
    'enable' => true,

    // 从异常堆栈跟踪中打开代码编辑器的标识
    // 支持 sublime、textmate、emacs、macvim、phpstorm、idea、vscode、atom、espresso
    // 留空表示不启用编辑器链接
    'editor' => '',

    // Whoops 错误页面标题，留空时使用 Whoops 默认标题
    'title' => '系统发生内部错误',

    // 是否在错误页面附加 ThinkPHP 应用信息
    // 包含框架版本、请求 URL、请求方法、Host 等，便于定位问题
    'tables' => true,

    // AJAX / JSON 请求是否也渲染 Whoops 页面
    // false 时返回 ThinkPHP 标准的 JSON 异常数据，避免前端无法解析 HTML
    // true 时无论请求类型都返回 Whoops 页面，便于在浏览器调试接口
    'ajax' => false,

    // 不交给 Whoops 处理的异常类名，匹配到后交回 ThinkPHP 默认处理器
    // 例如 [\app\exception\BusinessException::class]
    'except' => [],
];