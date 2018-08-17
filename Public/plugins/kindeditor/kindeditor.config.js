/**
 * KindEditor配置文件
 * author: xiayulei@gmail.com
 */
var KindEditorOptions = {
    //编辑器的宽度，可以设置px或%，比textarea输入框样式表宽度优先度高
    //width: '300px',
    //编辑器的高度，只能设置px，比textarea输入框样式表高度优先度高
    //height: '300px',
    //指定编辑器最小宽度，单位为px
    //minWidth: 200,
    //指定编辑器最小高度，单位为px
    minHeight: 300,
    //配置编辑器的工具栏，其中”/”表示换行，”|”表示分隔符
    items: [
        'source', '|', 'preview', 'code', 'cut', 'copy', 'paste',
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image', 'multiimage',
        'media', 'insertfile', 'table', 'hr', 'baidumap', 'pagebreak',
        'anchor', 'link', 'unlink', '|', 'about'
    ],
    //designMode 为false时，要保留的工具栏图标
    //noDisableItems: false,
    //true时根据 htmlTags 过滤HTML代码，false时允许输入任何代码
    filterMode: false,
    //指定要保留的HTML标记和属性。Object的key为HTML标签名，value为HTML属性数组，”.”开始的属性表示style属性
    htmlTags: {
        font: ['color', 'size', 'face', '.background-color'],
        span: [
            '.color', '.background-color', '.font-size', '.font-family', '.background',
            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.line-height'
        ],
        div: [
            'align', '.border', '.margin', '.padding', '.text-align', '.color',
            '.background-color', '.font-size', '.font-family', '.font-weight', '.background',
            '.font-style', '.text-decoration', '.vertical-align', '.margin-left'
        ],
        table: [
            'border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor',
            '.padding', '.margin', '.border', 'bgcolor', '.text-align', '.color', '.background-color',
            '.font-size', '.font-family', '.font-weight', '.font-style', '.text-decoration', '.background',
            '.width', '.height', '.border-collapse'
        ],
        'td,th': [
            'align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor',
            '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.font-weight',
            '.font-style', '.text-decoration', '.vertical-align', '.background', '.border'
        ],
        a: ['href', 'target', 'name'],
        embed: ['src', 'width', 'height', 'type', 'loop', 'autostart', 'quality', '.width', '.height', 'align', 'allowscriptaccess'],
        img: ['src', 'width', 'height', 'border', 'alt', 'title', 'align', '.width', '.height', '.border'],
        'p,ol,ul,li,dl,dt,dd,blockquote,h1,h2,h3,h4,h5,h6': [
            'class', 'id',
            'align', '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.background',
            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.text-indent', '.margin-left'
        ],
        pre: ['class'],
        hr: ['class', '.page-break-after'],
        'br,tbody,tr,strong,b,sub,sup,em,i,u,strike,s,del': [],
        iframe: ['src', 'width', 'height', 'style']
    },
    //true时美化HTML数据
    //wellFormatMode: true,
    //2或1或0，2时可以拖动改变宽度和高度，1时只能改变高度，0时不能拖动
    //resizeType: 2,
    //指定主题风格，可设置”default”、”simple”，指定simple时需要引入simple.css
    //themeType: 'default',
    //指定语言，可设置”en”、”zh-CN”，需要引入lang/[langType].js
    //langType: 'zh-CN',
    //可视化模式或代码模式
    //designMode: true,
    //true时加载编辑器后变成全屏模式
    //fullscreenMode: false,
    //指定编辑器的根目录路径
    //basePath: '',
    //指定编辑器的themes目录路径
    //themesPath: basePath + 'themes/',
    //指定编辑器的plugins目录路径
    //pluginsPath: basePath + 'plugins/',
    //指定编辑器的lang目录路径
    //langPath: basePath + 'lang/',
    //undo/redo文字输入最小变化长度，当输入的文字变化小于这个长度时不会添加到undo记录里
    //minChangeSize: 5,
    //改变站内本地URL，可设置”“、”relative”、”absolute”、”domain”。空为不修改URL，relative为相对路径，absolute为绝对路径，domain为带域名的绝对路径
    urlType: 'absolute',
    //设置回车换行标签，可设置”p”、”br”
    //newlineTag: 'p',
    //设置粘贴类型，0:禁止粘贴, 1:纯文本粘贴, 2:HTML粘贴
    //pasteType: 2,
    //设置弹出框(dialog)的对齐类型，可设置”“、”page”，指定page时按当前页面居中，指定空时按编辑器居中
    //dialogAlignType: 'page',
    //true时弹出层(dialog)显示阴影
    //shadowMode: true,
    //指定弹出层的基准z-index
    //zIndex: 811213,
    //true时使用右键菜单，false时屏蔽右键菜单
    //useContextmenu: true,
    //同步数据的方式，可设置”“、”form”，值为form时提交form时自动同步，空时不会自动同步
    //syncType: 'form',
    //wellFormatMode 为true时，HTML代码缩进字符
    //indentChar: '\t',
    //指定编辑器iframe document的CSS文件，用于设置可视化区域的样式
    //cssPath: '',
    //指定编辑器iframe document的CSS数据，用于设置可视化区域的样式
    //cssData: '',
    //指定编辑器iframe document body的className
    //bodyClass: 'ke-content',
    //指定取色器里的颜色
    /*
    colorTable: [
            ['#E53333', '#E56600', '#FF9900', '#64451D', '#DFC5A4', '#FFE500'],
            ['#009900', '#006600', '#99BB00', '#B8D100', '#60D978', '#00D5FF'],
            ['#337FE5', '#003399', '#4C33E5', '#9933E5', '#CC33E5', '#EE33EE'],
            ['#FFFFFF', '#CCCCCC', '#999999', '#666666', '#333333', '#000000']
    ],
    */
    //设置编辑器创建后执行的回调函数
    //afterCreate: function(){},
    //编辑器内容发生变化后执行的回调函数
    //afterChange: function(){},
    //按下TAB键后执行的的回调函数
    //afterTab: function(){},
    //编辑器聚焦(focus)时执行的回调函数
    //afterFocus: function(){},
    //编辑器失去焦点(blur)时执行的回调函数
    afterBlur: function(){
        this.sync();
    },
    //上传文件后执行的回调函数
    //afterUpload: function(){},
    //指定上传文件的服务器端程序
    //uploadJson:  basePath + 'php/upload_json.php',
    //指定浏览远程图片的服务器端程序
    //fileManagerJson: basePath + 'php/file_manager_json.php',
    //true时鼠标放在表情上可以预览表情
    //allowPreviewEmoticons: true,
    //true时显示图片上传按钮
    //allowImageUpload: true,
    //true时显示Flash上传按钮
    //allowFlashUpload: true,
    //true时显示视音频上传按钮
    //allowMediaUpload: true,
    //true时显示文件上传按钮
    //allowFileUpload: true,
    //true时显示浏览远程服务器按钮
    //allowFileManager: false,
    //指定文字大小
    //fontSizeTable: ['9px', '10px', '12px', '14px', '16px', '18px', '24px', '32px'],
    //图片弹出层的默认显示标签索引
    //imageTabIndex: 0,
    //false时不会自动格式化上传后的URL
    //formatUploadUrl: true,
    //false时禁用ESC全屏快捷键
    //fullscreenShortcut: false,
    //上传图片、Flash、视音频、文件时，支持添加别的参数一并传到服务器
    //extraFileUploadParams: {},
    //指定上传文件form名称
    //filePostName: 'imgFile',
    //true时图片上传成功后切换到图片编辑标签，false时插入图片后关闭弹出框
    //fillDescAfterUploadImage: false,
    //从图片空间选择文件后执行的回调函数
    //afterSelectFile: function(){},
    //可指定分页符HTML
    //pagebreakHtml: '<hr style="page-break-after:always;" class="ke-pagebreak" />',
    //true时显示网络图片标签，false时不显示
    //allowImageRemote: true,
    //值为true，并引入autoheight.js插件时自动调整高度
    //autoHeightMode: false,
    //值为true，并引入fixtoolbar.js插件时固定工具栏位置
    //fixToolBar: false,
    //批量上传图片单张最大容量
    imageSizeLimit: '2MB',
    //批量上传图片同时上传最多个数
    //imageUploadLimit: 20
};