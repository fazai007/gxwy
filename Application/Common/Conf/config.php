<?php

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    // 应用类库是否使用命名空间 3.2.1新增
    'APP_USE_NAMESPACE' => true,
    // 是否开启子域名部署
    'APP_SUB_DOMAIN_DEPLOY' => false,
    // 子域名部署规则
    'APP_SUB_DOMAIN_RULES' => [],
    // 域名后缀 如果是com.cn net.cn 之类的后缀必须设置
    'APP_DOMAIN_SUFFIX' => '',
    // 操作方法后缀
    'ACTION_SUFFIX' => '',
    // 是否允许多模块 如果为false 则必须设置 DEFAULT_MODULE
    'MULTI_MODULE' => true,
    // 禁止访问的模块列表
    'MODULE_DENY_LIST' => ['Common', 'Runtime'],
    // 允许访问的模块列表
    'MODULE_ALLOW_LIST' => [],
    'CONTROLLER_LEVEL' => 1,
    // 自动加载的应用类库层（针对非命名空间定义类库） 3.2.1新增
    'APP_AUTOLOAD_LAYER' => 'Controller,Model',
    // 自动加载的路径（针对非命名空间定义类库） 3.2.1新增
    'APP_AUTOLOAD_PATH' => '',
    // +----------------------------------------------------------------------
    // | 默认设置
    // +----------------------------------------------------------------------
    // 默认的模型层名称
    'DEFAULT_M_LAYER' => 'Model',
    // 默认的控制器层名称
    'DEFAULT_C_LAYER' => 'Controller',
    // 默认的视图层名称
    'DEFAULT_V_LAYER' => 'View',
    // 默认语言
    'DEFAULT_LANG' => 'zh-cn',
    // 默认模板主题名称
    'DEFAULT_THEME' => '',
    // 默认模块
    'DEFAULT_MODULE' => 'Wx',
    // 默认控制器名称
    'DEFAULT_CONTROLLER' => 'Index',
    // 默认操作名称
    'DEFAULT_ACTION' => 'index',
    // 默认输出编码
    'DEFAULT_CHARSET' => 'utf-8',
    // 默认时区
    'DEFAULT_TIMEZONE' => 'PRC',
    // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_AJAX_RETURN' => 'JSON',
    // 默认JSONP格式返回的处理方法
    'DEFAULT_JSONP_HANDLER' => 'jsonpReturn',
    // 默认参数过滤方法 用于I函数...
    'DEFAULT_FILTER' => 'trim,htmlspecialchars',
    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    // Cookie有效期
    'COOKIE_EXPIRE' => 172800,
    // Cookie有效域名
    'COOKIE_DOMAIN' => '',
    // Cookie路径
    'COOKIE_PATH' => '/',//online=>/lgqgdjt/
    // Cookie前缀 避免冲突
    'COOKIE_PREFIX' => 'LGQGDJT',
    // Cookie的httponly属性 3.2.2新增
    'COOKIE_HTTPONLY' => '',
    // +----------------------------------------------------------------------
    // | 数据库设置
    // +----------------------------------------------------------------------
    // 数据库类型
    'DB_TYPE' => 'mysql',
    // 服务器地址
    'DB_HOST' => '127.0.0.1',//online=>47.94.218.127
    // 数据库名
    'DB_NAME' => 'gxwy',
    // 用户名
    'DB_USER' => 'root',
    // 密码
    'DB_PWD' => '',//online=>Zhaoqiaoyun@123
    // 端口
    'DB_PORT' => '',
    // 数据库表前缀
    'DB_PREFIX' => 'xilu_',
    // 是否进行字段类型检查 3.2.3版本废弃
    //'DB_FIELDTYPE_CHECK'    =>  false,
    // 启用字段缓存
    'DB_FIELDS_CACHE' => true,
    // 数据库编码默认采用utf8
    'DB_CHARSET' => 'utf8',
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_DEPLOY_TYPE' => 0,
    // 数据库读写是否分离 主从式有效
    'DB_RW_SEPARATE' => false,
    // 读写分离后 主服务器数量
    'DB_MASTER_NUM' => 1,
    // 指定从服务器序号
    'DB_SLAVE_NO' => '',
    // 数据库查询的SQL创建缓存 3.2.3版本废弃
    //'DB_SQL_BUILD_CACHE'    =>  false,
    // SQL缓存队列的缓存方式 支持 file xcache和apc 3.2.3版本废弃
    //'DB_SQL_BUILD_QUEUE'    =>  'file',
    // SQL缓存的队列长度 3.2.3版本废弃
    'DB_SQL_BUILD_LENGTH' => 20,
    // SQL执行日志记录 3.2.3版本废弃
    //'DB_SQL_LOG'            =>  false,
    // 数据库写入数据自动参数绑定
    'DB_BIND_PARAM' => false,
    // 数据库调试模式 3.2.3新增
    'DB_DEBUG' => false,
    // 数据库Lite模式 3.2.3新增
    'DB_LITE' => false,
    // +----------------------------------------------------------------------
    // | 数据缓存设置
    // +----------------------------------------------------------------------
    // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_TIME' => 0,
    // 数据缓存是否压缩缓存
    'DATA_CACHE_COMPRESS' => false,
    // 数据缓存是否校验缓存
    'DATA_CACHE_CHECK' => false,
    // 缓存前缀
    'DATA_CACHE_PREFIX' => '',
    // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_TYPE' => 'File',
    // 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_PATH' => TEMP_PATH,
    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_CACHE_SUBDIR' => false,
    // 子目录缓存级别
    'DATA_PATH_LEVEL' => 1,
    // +----------------------------------------------------------------------
    // | 错误设置
    // +----------------------------------------------------------------------
    //错误显示信息,非调试模式有效
    'ERROR_MESSAGE' => '页面错误！请稍后再试～',
    // 错误定向页面
    'ERROR_PAGE' => '',
    // 显示错误信息
    'SHOW_ERROR_MSG' => false,
    // 每个级别的错误信息 最大记录数
    'TRACE_MAX_RECORD' => 100,
    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------
    // 默认不记录日志
    'LOG_RECORD' => false,
    // 日志记录类型 默认为文件方式
    'LOG_TYPE' => 'File',
    // 允许记录的日志级别
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR',
    // 是否记录异常信息日志
    'LOG_EXCEPTION_RECORD' => false,
    // +----------------------------------------------------------------------
    // | SESSION设置
    // +----------------------------------------------------------------------
    // 是否自动开启Session
    'SESSION_AUTO_START' => true,
    // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_OPTIONS' => [],
    // session hander类型 默认无需设置 除非扩展了session hander驱动
    'SESSION_TYPE' => '',
    // session 前缀
    'SESSION_PREFIX' => 'LGQGDJT',
    // +----------------------------------------------------------------------
    // | 模板引擎设置
    // +----------------------------------------------------------------------
    // 默认模板输出类型
    'TMPL_CONTENT_TYPE' => 'text/html',
    // 默认错误跳转对应的模板文件
    'TMPL_ACTION_ERROR' => THINK_PATH . 'Tpl/dispatch_jump.tpl',
    // 默认成功跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => THINK_PATH . 'Tpl/dispatch_jump.tpl',
    // 异常页面的模板文件
    'TMPL_EXCEPTION_FILE' => THINK_PATH . 'Tpl/think_exception.tpl',
    // 自动侦测模板主题
    'TMPL_DETECT_THEME' => false,
    // 默认模板文件后缀
    'TMPL_TEMPLATE_SUFFIX' => '.html',
    //模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符
    'TMPL_FILE_DEPR' => '/',
    // 默认模板引擎 以下设置仅对使用Think模板引擎有效
    'TMPL_ENGINE_TYPE' => 'Think',
    // 默认模板缓存后缀
    'TMPL_CACHFILE_SUFFIX' => '.php',
    // 模板引擎禁用函数
    'TMPL_DENY_FUNC_LIST' => 'echo,exit',
    // 默认模板引擎是否禁用PHP原生代码
    'TMPL_DENY_PHP' => false,
    // 模板引擎普通标签开始标记
    'TMPL_L_DELIM' => '{',
    // 模板引擎普通标签结束标记
    'TMPL_R_DELIM' => '}',
    // 模板变量识别。留空自动判断,参数为'obj'则表示对象
    'TMPL_VAR_IDENTIFY' => 'array',
    // 是否去除模板文件里面的html空格与换行
    'TMPL_STRIP_SPACE' => true,
    // 是否开启模板编译缓存,设为false则每次都会重新编译
    'TMPL_CACHE_ON' => true,
    // 模板缓存前缀标识，可以动态改变
    'TMPL_CACHE_PREFIX' => '',
    // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
    'TMPL_CACHE_TIME' => 0,
    // 布局模板的内容替换标识
    'TMPL_LAYOUT_ITEM' => '{__CONTENT__}',
    // 是否启用布局
    'LAYOUT_ON' => false,
    // 当前布局名称 默认为layout
    'LAYOUT_NAME' => 'layout',
    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------
    // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_CASE_INSENSITIVE' => true,
    // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式
    'URL_MODEL' => 1,
    // PATHINFO模式下，各参数之间的分割符号
    'URL_PATHINFO_DEPR' => '/',
    // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
    'URL_PATHINFO_FETCH' => 'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL',
    // 获取当前页面地址的系统变量 默认为REQUEST_URI
    'URL_REQUEST_URI' => 'REQUEST_URI',
    // URL伪静态后缀设置
    'URL_HTML_SUFFIX' => 'html',
    // URL禁止访问的后缀设置
    'URL_DENY_SUFFIX' => 'ico|png|gif|jpg',
    // URL变量绑定到Action方法参数
    'URL_PARAMS_BIND' => true,
    // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
    'URL_PARAMS_BIND_TYPE' => 0,
    // 404 跳转页面 部署模式有效
    'URL_404_REDIRECT' => '',
    // 是否开启URL路由
    'URL_ROUTER_ON' => false,
    // 默认路由规则 针对模块
    'URL_ROUTE_RULES' => [],
    // URL映射定义规则
    'URL_MAP_RULES' => [],
    // +----------------------------------------------------------------------
    // | 系统变量名称设置
    // +----------------------------------------------------------------------
    // 默认模块获取变量
    'VAR_MODULE' => 'm',
    // 默认控制器获取变量
    'VAR_CONTROLLER' => 'c',
    // 默认操作获取变量
    'VAR_ACTION' => 'a',
    // 默认的AJAX提交变量
    'VAR_AJAX_SUBMIT' => 'ajax',
    'VAR_JSONP_HANDLER' => 'callback',
    // 兼容模式PATHINFO获取变量例如 ?s=/module/action/id/1 后面的参数取决于URL_PATHINFO_DEPR
    'VAR_PATHINFO' => 's',
    // 默认模板切换变量
    'VAR_TEMPLATE' => 't',
    // 默认的插件控制器命名空间变量 3.2.2新增
    'VAR_ADDON' => 'addon',
    // +----------------------------------------------------------------------
    // | AUTH 设置
    // +----------------------------------------------------------------------
    'AUTH_CONFIG' => [
        // 认证开关
        'AUTH_ON' => true,
        // 认证方式，1为实时认证；2为登录认证。
        'AUTH_TYPE' => 1,
        // 用户组数据表名
        'AUTH_GROUP' => 'xilu_auth_group',
        // 用户-用户组关系表
        'AUTH_GROUP_ACCESS' => 'xilu_auth_group_access',
        // 权限规则表
        'AUTH_RULE' => 'xilu_auth_rule',
        // 用户信息表
        'AUTH_USER' => 'xilu_admin_user'
    ],
    // +----------------------------------------------------------------------
    // | 其他设置
    // +----------------------------------------------------------------------
    // 网页缓存控制
    'HTTP_CACHE_CONTROL' => 'private',
    // 是否检查应用目录是否创建
    'CHECK_APP_DIR' => true,
    // 文件上传方式
    'FILE_UPLOAD_TYPE' => 'Local',
    // 数据加密方式
    'DATA_CRYPT_TYPE' => 'Think',
    //加载扩展配置文件
    'LOAD_EXT_CONFIG' => '',
    //分页参数名称
    'VAR_PAGE' => 'page',
    //分页每页记录数
    'PAGE_SIZE' => 20,
    // 全站加密密钥（开发新站点前请修改此项）
    'SALT' => 'xilukeji',
    //微信授权跳转地址
    'WX_AUTH_REDIRECT_URL' => domain() . '/index.php/Api/Wechat/req',
    //支付配置
    'PAYMENT_LIST' => [
        'wxpay' => [
            'name' => '微信支付',
            'logo' => 'Admin/images/wxpay.png',
            'description' => '该系统支持微信网页支付和扫码支付',
            'status' => true
        ],
        'alipay' => [
            'name' => '支付宝支付',
            'logo' => 'Admin/images/alipay.png',
            'description' => '该系统支持即时到账接口',
            'status' => false
        ],
        'unionpay' => [
            'name' => '银联卡支付',
            'logo' => 'Admin/images/unionpay.png',
            'description' => '该系统支持即时到账接口',
            'status' => false
        ]
    ],
    //公众号配置
    'WX_CONFIG' => [
        'url' => domain() . '/index.php/Api/Wechat/index'
    ],
    'UPLOAD_CONFIG' => [
        //上传地址,默认是本地上传
        'upload_url' => '../Upload/upload',
        //CDN地址
        'cdn_url' => '',
        //最大可上传大小
        'max_size' => '100mb',
        //可上传的文件类型
        'mime_type' => 'jpg,png,bmp,jpeg,gif,swf,flv,mp3,wav,wma,wmv,mid,avi,mpg,asf,rm,rmvb,mp4,3gp,mkv,mov,ogg,amr,speed,zip,rar,xls,xlsx',
        //是否支持批量上传
        'multiple' => false
    ],
    'accountSid' => '8a216da854ff8dcc01550498f754073d',
    'accountToken' => '25b13adc19a445088b07939214c74af4',
    'appId' => '8a216da854ff8dcc01550498f7ba0743',
    'tempId' => '178635',
];
