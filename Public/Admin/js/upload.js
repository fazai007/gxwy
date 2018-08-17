function pluploadInit(uploadIds) {
    $.each(uploadIds, function (i, n) {
        var self = this.toString();//上传元素id
        var $this = $('#' + self);
        var url = $this.data('url');
        var maxSize = $this.data('max-size');
        var mimeType = $this.data('mime-type');
        var multipart = $this.data('multipart');
        var multiple = $this.data('multiple');
        //填充ID
        var inputId = $this.data('input-id') ? $this.data('input-id') : '';
        //预览ID
        var previewId = $this.data('preview-id') ? $this.data('preview-id') : '';
        //上传URL
        url = url ? url : myConfig.upload.upload_url;
        //最大可上传文件大小
        maxSize = typeof maxSize !== 'undefined' ? maxSize : myConfig.upload.max_size;
        //文件类型
        mimeType = typeof mimeType !== 'undefined' ? mimeType : myConfig.upload.mime_type;
        //请求的表单参数
        multipart = typeof multipart !== 'undefined' ? multipart : myConfig.upload.multipart;
        //是否支持批量上传
        multiple = typeof multiple !== 'undefined' ? multiple : myConfig.upload.multiple;
        var mimeTypeArr = new Array();
        //支持后缀和Mimetype格式,以,分隔
        if (mimeType && mimeType !== '*' && mimeType.indexOf('/') === -1) {
            var tempArr = mimeType.split(',');
            for (var i = 0; i < tempArr.length; i++) {
                mimeTypeArr.push({title: '文件(多)', extensions: tempArr[i]});
            }
            mimeType = mimeTypeArr;
        }
        //生成Plupload实例
        var uploader = new plupload.Uploader({
            browse_button: self, //触发文件选择对话框的DOM元素
            url: url, //服务器端接收和处理上传文件的脚本地址
            filters: {
                max_file_size: maxSize, //限定上传文件的类型
                mime_types: mimeType//限定上传文件的大小
            },
            multipart_params: $.isArray(multipart) ? {} : multipart, //上传时的附加参数
            runtimes: 'html5,flash,silverlight,html4',
            multi_selection: multiple, //是否允许多选批量上传
            flash_swf_url: myConfig.public_path + '/plugins/plupload/js/Moxie.swf',
            silverlight_xap_url: myConfig.public_path + '/plugins/plupload/js/Moxie.xap'
        });
        //在实例对象上调用init()方法进行初始化
        uploader.init();
        //绑定各种事件，并在事件监听函数中做你想做的事
        //初始化完成后
        uploader.bind('PostInit', function (uploader) {

        });
        //当文件添加到上传队列后
        uploader.bind('FilesAdded', function (uploader, files) {
            var $button = $('#' + uploader.settings.browse_button[0].id);
            $button.data('bakup-html', $button.html());
            var maxCount = $button.data('max-count');
            var inputId = $button.data('input-id') ? $button.data('input-id') : '';
            maxCount = typeof maxCount !== 'undefined' ? maxCount : 0;
            if (maxCount > 0 && inputId) {
                var inputObj = $('#' + inputId);
                if (inputObj.size() > 0) {
                    var value = $.trim(inputObj.val());
                    var nums = value === '' ? 0 : value.split(/\,/).length;
                    var remains = maxCount - nums;
                    if (files.length > remains) {
                        for (var i = 0; i < files.length; i++) {
                            uploader.removeFile(files[i]);
                        }
                        layer.msg('你最多还可以上传' + remains + '个文件');
                        return false;
                    }
                }
            }
            //添加后立即上传
            setTimeout(function () {
                uploader.start();
            }, 1);
        });
        //在文件上传过程中
        uploader.bind('UploadProgress', function (uploader, file) {
            var $button = $('#' + uploader.settings.browse_button[0].id);
            $button.prop('disabled', true).html('<i class="fa fa-upload"></i> 上传' + file.percent + '%');
        });
        //当队列中的某一个文件正要开始上传前
        uploader.bind('BeforeUpload', function (uploader, file) {

        });
        //当队列中的某一个文件上传完成后的回调
        uploader.bind('FileUploaded', function (uploader, file, response) {
            var $button = $('#' + uploader.settings.browse_button[0].id);
            $button.prop('disabled', false).html($button.data('bakup-html'));
            try {
                var ret = typeof response.response === 'object' ? response.response : JSON.parse(response.response);
                if (ret.hasOwnProperty('key') && !ret.hasOwnProperty('err_code')) {
                    ret.code = 1;
                    ret.data = {
                        url: '/' + ret.key
                    };
                } else if (!ret.hasOwnProperty('code')) {
                    $.extend(ret, {code: -2, msg: response.response, data: null});
                }
            } catch (e) {
                var ret = {code: -1, msg: e.message, data: null};
            }
            file.ret = ret;
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            if (ret.code === 1) {
                //上传成功后回调
                if ($button) {
                    var inputId = $button.data('input-id') ? $button.data('input-id') : '';
                    //如果有文本框则填充
                    if (inputId) {
                        var urlArr = [];
                        var inputObj = $('#' + inputId);
                        if ($button.data('multiple') && inputObj.val() !== '') {
                            urlArr.push(inputObj.val());
                        }
                        urlArr.push(data.url);
                        inputObj.val(urlArr.join(',')).trigger('change');
                    }
                    var onDomUploadSuccess = $button.data('upload-success');
                    if (onDomUploadSuccess) {
                        //todo_something
                    }
                }
            } else {
                if ($button) {
                    var onDomUploadError = $button.data('upload-error');
                    if (onDomUploadError) {
                        //todo_something
                    }
                }
                layer.msg(ret.msg + '(code:' + ret.code + ')');
            }
        });
        //当上传队列中所有文件都上传完成后
        uploader.bind('UploadComplete', function (uploader, files) {
            var $button = $('#' + uploader.settings.browse_button[0].id);
            if ($button) {
                var onDomUploadComplete = $button.data('upload-complete');
                if (onDomUploadComplete) {
                    //todo_something
                }
            }
        });
        //当发生错误时
        uploader.bind('Error', function (uploader, err) {
            var $button = $('#' + uploader.settings.browse_button[0].id);
            $button.prop('disabled', false).html($button.data('bakup-html'));
            layer.msg(err.message + '(code:' + err.code + ')');
        });
        //todo_something
    });
}