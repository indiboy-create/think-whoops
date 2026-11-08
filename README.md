# think-whoops

ThinkPHP 8 的 Whoops 异常调试接管组件，提供堆栈清晰、界面友好的错误页面。


## 环境要求

- PHP >= 8.0
- ThinkPHP 8
- filp/whoops >= 2.19

## 安装

```bash
composer require youvision/think-whoops
```

安装后 ThinkPHP 会自动完成：

- 服务注册：`extra.think.services`
- 配置复制：`extra.think.config`，生成 `config/whoops.php`（已存在则不覆盖）
- 异常处理器替换：绑定 `think\exception\Handle` 到本包的 `Handle`

若项目未执行过配置发布，可手动执行：

```bash
php think vendor:publish
```

## 启用条件

只有同时满足以下条件时 Whoops 才会接管异常：

- `app_debug` 为 `true`
- `whoops.enable` 为 `true`
- 当前不是命令行（CLI）请求
- 异常不是 `HttpResponseException`
- 异常不属于 `whoops.except` 配置的类

其他情况仍由 ThinkPHP 默认异常处理器处理。

## 配置

配置文件位于 `config/whoops.php`：

| 配置 | 默认值 | 说明 |
| --- | --- | --- |
| `enable` | `true` | 是否启用 Whoops，仅在调试模式生效 |
| `editor` | `''` | 代码编辑器标识，留空不启用 |
| `title` | `系统发生内部错误` | 错误页面标题 |
| `tables` | `true` | 是否附加 ThinkPHP 应用信息表 |
| `ajax` | `false` | AJAX/JSON 请求是否也渲染 Whoops 页面 |
| `except` | `[]` | 不接管处理的异常类列表 |

### 打开编辑器

`editor` 支持 `sublime`、`textmate`、`emacs`、`macvim`、`phpstorm`、`idea`、`vscode`、`atom`、`espresso`：

```php
return [
    'editor' => 'vscode',
];
```

### 接口调试

默认情况下 AJAX / JSON 请求返回 ThinkPHP 标准的 JSON 异常数据。如果希望在浏览器中直接查看接口异常的 Whoops 页面，可设置：

```php
return [
    'ajax' => true,
];
```

### 跳过指定异常

```php
return [
    'except' => [
        \app\exception\BusinessException::class,
    ],
];
```

## 在业务代码中调用

`whoops` 已注册到容器，可直接使用：

```php
app('whoops')->render($exception, request());
```

## 许可

MIT
