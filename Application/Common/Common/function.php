<?php

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1) {
    static $list = [];
    foreach ($array as $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[] = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 构建层级（树状）数组
 * @param array  $array          要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid_name       父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid_name = 'pid', $child_key_name = 'children') {
    $counter = array_children_count($array, $pid_name);
    if (!isset($counter[0]) || $counter[0] == 0) {
        return $array;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0) {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0) {
            array_push($array, $temp);
        } else {
            if ($temp[$pid_name] == 0) {
                $tree[] = $temp;
            } else {
                $array = array_child_append($array, $temp[$pid_name], $temp, $child_key_name);
            }
        }
        $counter = array_children_count($array, $pid_name);
    }
    return $tree;
}

/**
 * 子元素计数器
 * @param array $array
 * @param int   $pid
 * @return array
 */
function array_children_count($array, $pid) {
    $counter = [];
    foreach ($array as $item) {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }
    return $counter;
}

/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name) {
    foreach ($parent as &$item) {
        if ($item['id'] == $pid) {
            if (!isset($item[$child_key_name]))
                $item[$child_key_name] = [];
            $item[$child_key_name][] = $child;
        }
    }
    return $parent;
}

/**
 * 获取时间
 */
function get_date_time($time = '') {
    $time = $time ? $time : time();
    return date('Y-m-d H:i:s', $time);
}

/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name) {
    $result = false;
    if (is_dir($dir_name)) {
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DS . $item)) {
                        delete_dir_file($dir_name . DS . $item);
                    } else {
                        unlink($dir_name . DS . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }
    return $result;
}

/**
 * 获取网站域名
 */
function domain() {
    $server = $_SERVER['HTTP_HOST'];
    $http = is_ssl() ? 'https://' : 'http://';
    return $http . $server . __ROOT__;
}

/**
 * 获取网站根域名
 */
function root_domain() {
    $server = $_SERVER['HTTP_HOST'];
    $http = is_ssl() ? 'https://' : 'http://';
    return $http . $server;
}

/**
 * 截取字符串
 * @param string $str
 * @param int $start
 * @param int $length
 * @param string $charset
 * @param boolean $suffix
 * @return type
 */
function m_sub_str($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    $new_str = '';
    if (function_exists("mb_substr")) {
        if ($suffix)
            $new_str = mb_substr($str, $start, $length, $charset);
        else
            $new_str = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        if ($suffix)
            $new_str = iconv_substr($str, $start, $length, $charset);
        else
            $new_str = iconv_substr($str, $start, $length, $charset);
    }
    if ($new_str == '') {
        $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re [$charset], $str, $match);
        $slice = join("", array_slice($match [0], $start, $length));
        if ($suffix)
            $new_str = $slice;
    }
    return (strlen($str) > strlen($new_str)) ? $new_str . "..." : $new_str;
}

/**
 * js escape php 实现
 * @param $string
 * @param $in_encoding      
 * @param $out_encoding     
 */
function escape($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2') {
    $return = '';
    if (function_exists('mb_get_info')) {
        for ($x = 0; $x < mb_strlen($string, $in_encoding); $x ++) {
            $str = mb_substr($string, $x, 1, $in_encoding);
            if (strlen($str) > 1) { // 多字节字符
                $return .= '%u' . strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding)));
            } else {
                $return .= '%' . strtoupper(bin2hex($str));
            }
        }
    }
    return $return;
}

/**
 * js unescape php 实现
 * @param string $str
 * @return type
 */
function unescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i ++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else
            if ($val < 0x800)
                $ret .= chr(0xc0 | ($val >> 6)) .
                        chr(0x80 | ($val & 0x3f));
            else
                $ret .= chr(0xe0 | ($val >> 12)) .
                        chr(0x80 | (($val >> 6) & 0x3f)) .
                        chr(0x80 | ($val & 0x3f));
            $i += 5;
        } else
        if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else
            $ret .= $str[$i];
    }
    return $ret;
}

/**
 * 建立文件夹
 * @param string $aim_url
 * @return viod
 */
function xilu_create_dir($aim_url) {
    $aim_url = str_replace('', '/', $aim_url);
    $aim_dir = '';
    $arr = explode('/', $aim_url);
    $result = true;
    foreach ($arr as $str) {
        $aim_dir .= $str . '/';
        if (!file_exists_case($aim_dir)) {
            $result = mkdir($aim_dir, 0777);
        }
    }
    return $result;
}

/**
 * 建立文件
 * @param string $aimUrl
 * @param boolean $overWrite 该参数控制是否覆盖原文件
 * @return boolean
 */
function xilu_create_file($aim_url, $over_write = false) {
    if (file_exists_case($aim_url) && $over_write == false) {
        return false;
    } elseif (file_exists_case($aim_url) && $over_write == true) {
        xilu_unlink_file($aim_url);
    }
    $aim_dir = dirname($aim_url);
    xilu_create_dir($aim_dir);
    touch($aim_url);
    return true;
}

/**
 * 删除文件
 * @param string $aim_url
 * @return boolean
 */
function xilu_unlink_file($aim_url) {
    if (file_exists_case($aim_url)) {
        unlink($aim_url);
        return true;
    } else {
        return false;
    }
}

/**
 * 写日志
 * @param string $file_path
 * @param string $word
 */
function xilu_log($file_path, $word) {
    if (!file_exists_case($file_path)) {
        xilu_create_file($file_path);
    }
    $fp = fopen($file_path, "a");
    flock($fp, LOCK_EX);
    fwrite($fp, $word);
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * 加密/解密
 * @param string $string 明文或密文
 * @param string $operation DECODE表示解密,其它表示加密
 * @param string $key
 * @param int $expiry 密文有效期
 * @return string
 */
function auth_code($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $ckey_length = 4;
    // 密匙
    $key = md5($key ? $key : C('XILU_AUTH_KEY'));
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = [];
    // 产生密匙簿
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上并不会增加密文的强度
    for ($j = $i = 0; $i < 256; $i++) {
        //$j是三个数相加与256取余
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        //在上面基础上再加1 然后和256取余
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256; //$j加$box[$a]的值 再和256取余
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符，加密和解决时($box[($box[$a] + $box[$j]) % 256])的值是不变的。
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // substr($result, 0, 10) == 0 验证数据有效性
        // substr($result, 0, 10) - time() > 0 验证数据有效性
        // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
        // 验证数据有效性，请看未加密明文的格式
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    for ($i = 0; $size >= 1024 && $i < 5; $i++)
        $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 笛卡尔积
 * @param array $data
 * @return type
 */
function combine_dika($data) {
    $result = [];
    foreach (array_shift($data) as $k => $item) {
        $result[] = [$k => $item];
    }
    foreach ($data as $k => $v) {
        $result2 = [];
        foreach ($result as $k1 => $item1) {
            foreach ($v as $k2 => $item2) {
                $temp = $item1;
                $temp[$k2] = $item2;
                $result2[] = $temp;
            }
        }
        $result = $result2;
    }
    return $result;
}

/**
 * 系统设置
 */
function get_system($name = '') {
    $system_model = M('system');
    if ($name) {
        $value = $system_model->where("name='{$name}'")->getField('value');
        $data = $value ? unserialize($value) : [];
    } else {
        $system = $system_model->field('name,value')->select();
        $data = [];
        foreach ($system as $v) {
            $data[$v['name']] = $v['value'] ? unserialize($v['value']) : [];
        }
    }
    return $data;
}

/**
 * 获取邮件参数
 */
function get_email_options() {
    
}

/**
 * 获取公众号参数
 */
function get_wechat_options() {
    $wx_config = get_system('wx_config');
    return [
        'token' => $wx_config['token'],
        'encodingaeskey' => $wx_config['encoding_aes_key'],
        'appid' => $wx_config['app_id'],
        'appsecret' => $wx_config['app_secret']
    ];
}

/**
 * 判断是否微信访问
 * @return int
 */
function is_wechat() {
    //如果在微信中打开,加载微信分享
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false) {
        return 0;
    } else {
        return 1;
    }
}

/**
 * 设置SEO信息
 * @param string $title
 * @param string $keywords
 * @param string $description
 * @return array
 */
function set_seo($title = '', $keywords = '', $description = '') {
    $seo = [
        'title' => $title ? $title : $GLOBALS['SYSTEM']['seo_config']['title'],
        'keywords' => $keywords ? $keywords : $GLOBALS['SYSTEM']['seo_config']['keywords'],
        'description' => $description ? $description : $GLOBALS['SYSTEM']['seo_config']['description']
    ];
    return $seo;
}

/**
 * 设置微信分享信息
 * @param int $user_id 用户id
 * @param string $title 标题
 * @param string $desc 描述
 * @param string $img_url 图片url
 * @param string $url 链接地址
 * @return array
 */
function set_wx_share($user_id, $title = '', $desc = '', $img_url = '', $url = '') {
    if ($url) {
        $link = $url;
    } else {
        $link = root_domain() . __SELF__;
        if (strstr($link, '?')) {
            $link .= '&recommender_id=' . $user_id;
        } else {
            $link .= '?recommender_id=' . $user_id;
        }
    }
    $img_url = $img_url ? $img_url : $GLOBALS['SYSTEM']['web_config']['wechat_share_logo'];
    if (!preg_match('/^(?:[a-z]+:)?\/\//i', $img_url)) {
        $prefix = preg_match('/^(?:[a-z]+:)?\/\//i', __CDN__) ? __CDN__ : root_domain() . __CDN__;
        $img_url = $prefix . $img_url;
    }
    $wx_share = [
        'title' => $title ? m_sub_str($title, 0, 25) : m_sub_str($GLOBALS['SYSTEM']['seo_config']['title'], 0, 25),
        'desc' => $desc ? m_sub_str($desc, 0, 25) : m_sub_str($GLOBALS['SYSTEM']['seo_config']['description'], 0, 25),
        'link' => $link,
        'imgUrl' => $img_url,
        'type' => 'link'
    ];
    return $wx_share;
}

/**
 * 将搜索记录存入Cookie中
 * @param string $key 搜索类别
 * @param string $keyword 关键词
 * @return boolean
 */
function set_search_history($key, $keyword) {
    $search_history = json_decode(auth_code(cookie($key . '_search_history')), true);
    if (!$search_history) {
        $search_history[0] = [
            'keyword' => $keyword,
            'time' => time()
        ];
    } else {
        $num = count($search_history);
        //判断是否重复
        $judge = false;
        foreach ($search_history as $k => $v) {
            if ($search_history[$k]['keyword'] == $keyword) {
                $search_history[$k]['time'] = time();
                $judge = true;
            }
        }
        if (!$judge) {
            if ($num >= 10) {
                for ($i = 0; $i < 9; $i++) {
                    $search_history[$i] = $search_history[$i + 1];
                }
                $search_history[9] = [
                    'keyword' => $keyword,
                    'time' => time()
                ];
            } else {
                $search_history[$num] = [
                    'keyword' => $keyword,
                    'time' => time()
                ];
            }
        }
    }
    cookie($key . '_search_history', auth_code(json_encode($search_history), 'ENCODE'), 3600 * 24 * 365);
}

/**
 * 生成订单号
 */
function build_order_no() {
    return date('YmdHis', NOW_TIME) . mt_rand(10000, 99999);
}

/**
 * 数组转xls格式的excel文件
 * @param array $data   需要生成excel文件的数据
 * @param array $header 需要生成excel文件的表头
 * @param string $filename 生成的excel文件名
 */
function create_xls($data, $header = null, $file_name = 'simple.xls', $title = 'sheet1') {
    // 如果手动设置表头；则放在第一行
    if (!is_null($header)) {
        array_unshift($data, $header);
    }
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $file_name = str_replace('.xls', '', $file_name) . '.xls';
    $php_excel = new PHPExcel();
    $php_excel->getProperties()
            ->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");
    $php_excel->getActiveSheet()->fromArray($data);
    $php_excel->getActiveSheet()->setTitle($title);
    $php_excel->setActiveSheetIndex(0);
    header('Content-Type: application/vnd.ms-excel');
    header("Content-Disposition: attachment;filename=$file_name");
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($php_excel, 'Excel5');
    $objwriter->save('php://output');
    exit();
}

/**
 * 验证是否是正整数
 * @param string $subject
 * @return boolean
 */
function is_positive_integer($subject) {
    $pattern = '/^[1-9]\d*$/';
    return preg_match($pattern, $subject);
}

/**
 * 获取星期几
 * @param datetime $date_time
 * @return type
 */
function get_week($date_time) {
    $weeks = ['日', '一', '二', '三', '四', '五', '六'];
    return '星期' . $weeks[date('w', strtotime($date_time))];
}

/**
 * 格式化日期
 * @param datetime $date_time
 * @return type
 */
function format_date_time($date_time) {
    $date_str = strtotime(date('Y-m-d', strtotime($date_time)));
    $today_date_str = strtotime(date('Y-m-d'));
    $difference = ($today_date_str - $date_str) / 86400;
    if ($difference == 0) {
        return date('H:i', strtotime($date_time));
    } elseif ($difference == 1) {
        return '昨天 ' . date('H:i', strtotime($date_time));
    } elseif ($difference > 1 && $difference < 7) {
        return get_week($date_time) . ' ' . date('H:i', strtotime($date_time));
    } else {
        return date('Y-m-d H:i', strtotime($date_time));
    }
}

/**
 * 多久之前
 * @param int $datefrom 开始时间戳
 * @param int $dateto 结束时间戳
 * @return string
 */
function time_ago($datefrom, $dateto = -1) {
    // Defaults and assume if 0 is passed in that
    // its an error rather than the epoch
    if ($datefrom <= 0) {
        return "很久以前";
    }
    if ($dateto == -1) {
        $dateto = time();
    }
    // Calculate the difference in seconds betweeen
    // the two timestamps
    $difference = $dateto - $datefrom;
    // If difference is less than 60 seconds,
    // seconds is a good interval of choice
    if ($difference < 60) {
        $interval = "s";
    }
    // If difference is between 60 seconds and
    // 60 minutes, minutes is a good interval
    elseif ($difference >= 60 && $difference < 60 * 60) {
        $interval = "n";
    }
    // If difference is between 1 hour and 24 hours
    // hours is a good interval
    elseif ($difference >= 60 * 60 && $difference < 60 * 60 * 24) {
        $interval = "h";
    }
    // If difference is between 1 day and 7 days
    // days is a good interval
    elseif ($difference >= 60 * 60 * 24 && $difference < 60 * 60 * 24 * 7) {
        $interval = "d";
    }
    // If difference is between 1 week and 30 days
    // weeks is a good interval
    elseif ($difference >= 60 * 60 * 24 * 7 && $difference < 60 * 60 * 24 * 30) {
        $interval = "ww";
    }
    // If difference is between 30 days and 365 days
    // months is a good interval, again, the same thing
    // applies, if the 29th February happens to exist
    // between your 2 dates, the function will return
    // the 'incorrect' value for a day
    elseif ($difference >= 60 * 60 * 24 * 30 && $difference < 60 * 60 * 24 * 365) {
        $interval = "m";
    }
    // If difference is greater than or equal to 365
    // days, return year. This will be incorrect if
    // for example, you call the function on the 28th April
    // 2008 passing in 29th April 2007. It will return
    // 1 year ago when in actual fact (yawn!) not quite
    // a year has gone by
    elseif ($difference >= 60 * 60 * 24 * 365) {
        $interval = "y";
    }
    // Based on the interval, determine the
    // number of units between the two dates
    // From this point on, you would be hard
    // pushed telling the difference between
    // this function and DateDiff. If the $datediff
    // returned is 1, be sure to return the singular
    // of the unit, e.g. 'day' rather 'days'
    switch ($interval) {
        case "m":
            $months_difference = floor($difference / 60 / 60 / 24 / 29);
            while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                $months_difference++;
            }
            $datediff = $months_difference;
            // We need this in here because it is possible
            // to have an 'm' interval and a months
            // difference of 12 because we are using 29 days
            // in a month
            if ($datediff == 12) {
                $datediff--;
            }
            $res = ($datediff == 1) ? "$datediff 月前" : "$datediff 月前";
            break;
        case "y":
            $datediff = floor($difference / 60 / 60 / 24 / 365);
            $res = ($datediff == 1) ? "$datediff 年前" : "$datediff 年前";
            break;
        case "d":
            $datediff = floor($difference / 60 / 60 / 24);
            $res = ($datediff == 1) ? "$datediff 天前" : "$datediff 天前";
            break;
        case "ww":
            $datediff = floor($difference / 60 / 60 / 24 / 7);
            $res = ($datediff == 1) ? "$datediff 周前" : "$datediff 周前";
            break;
        case "h":
            $datediff = floor($difference / 60 / 60);
            $res = ($datediff == 1) ? "$datediff 小时前" : "$datediff 小时前";
            break;
        case "n":
            $datediff = floor($difference / 60);
            $res = ($datediff == 1) ? "$datediff 分钟前" : "$datediff 分钟前";
            break;
        case "s":
            $datediff = $difference;
            $res = ($datediff == 1) ? "$datediff 秒前" : "$datediff 秒前";
            break;
    }
    return $res;
}

/**
 * 验证手机号码格式
 * @param string $mobile 手机号码
 * @return boolean
 */
function is_mobile_format($mobile) {
    if (!preg_match('/^134[0-8]\d{7}$|^13[^4]\d{8}$|^14[5-9]\d{8}$|^15[^4]\d{8}$|^16[6]\d{8}$|^17[0-8]\d{8}$|^18[\d]{9}$|^19[8,9]\d{8}$/', $mobile)) {
        return false;
    }
    return true;
}

/**
 * 验证车牌号格式
 * @param string $lpn 车牌号
 * @return boolean
 */
function is_lpn_format($lpn) {
    $lpn_pattern_7 = '/^[\x{4e00}-\x{9fa5}]{1}[A-Z]{1}[A-Z0-9]{4}[A-Z0-9港澳学]{1}$/u';
    $lpn_pattern_8 = '/^[\x{4e00}-\x{9fa5}]{1}[A-Z]{1}[A-Z0-9]{6}$/u';
    $lpn_pattern = mb_strlen($lpn, 'utf-8') == 7 ? $lpn_pattern_7 : $lpn_pattern_8;
    if (!preg_match($lpn_pattern, $lpn)) {
        return false;
    }
    return true;
}

/**
 * 验证字符是否是汉字
 * @param string $str 字符
 * @return boolean
 */
function is_chinese($str) {
    if (!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str)) {
        return false;
    }
    return true;
}

function define_str_replace($data) {
    return str_replace(' ', '+', $data);
}

/**
 * 产生随机字符串，不长于32位
 * @param int $length
 * @return 产生的随机字符串
 */
function get_nonce_str($length = 32) {
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 微信支付签名
 * @param array $param
 * @return string
 */
function make_wx_app_pay_sign($param) {
    //签名步骤一：按字典序排序参数
    ksort($param);
    $string = '';
    foreach ($param as $k => $v) {
        if ($k != 'sign' && $v != '' && !is_array($v)) {
            $string .= $k . '=' . $v . '&';
        }
    }
    $string = trim($string, '&');
    //签名步骤二：在string后加入KEY
    $string = $string . '&key=';
    //签名步骤三：MD5加密
    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
}

/**
 * 返回成功信息
 * @param string $msg 响应消息
 * @param array $data 数据体
 */
function api_success($msg = '', $data = []) {
    $result = [
        'resCode' => 0,
        'resMsg' => $msg
    ];
    if (!empty($data)) {
        $result = array_merge($result, $data);
    }
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($result));
}

/**
 * 返回错误信息
 * @param string $msg 响应消息
 * @param array $data 数据体
 */
function api_error($msg = '', $data = []) {
    $result = [
        'resCode' => 1,
        'resMsg' => $msg
    ];
    if (!empty($data)) {
        $result = array_merge($result, $data);
    }
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($result));
}

/**
 * 发送邮件
 * @param  string $address 需要发送的邮箱地址 发送给多个地址需要写成数组形式
 * @param  string $subject 标题
 * @param  string $content 内容
 * @return boolean 是否成功
 */
function send_email($address, $subject, $content) {
    $email_options = get_email_options();
    if (!$email_options['from_name'] || !$email_options['from_address'] || !$email_options['smtp'] || !$email_options['port'] || !$email_options['username'] || !$email_options['password']) {
        return ['error' => 1, 'message' => '邮箱配置不完整'];
    }
    require_cache(VENDOR_PATH . 'PHPMailer/class.phpmailer.php');
    require_cache(VENDOR_PATH . 'PHPMailer/class.smtp.php');
    $php_mailer = new PHPMailer();
    //设置PHPMailer使用SMTP服务器发送Email
    $php_mailer->IsSMTP();
    //设置smtp_secure
    $php_mailer->SMTPSecure = $email_options['stmp_secure'];
    //设置port
    $php_mailer->Port = $email_options['port'];
    //设置为html格式
    $php_mailer->IsHTML(true);
    //设置邮件的字符编码
    $php_mailer->CharSet = 'UTF-8';
    //设置SMTP服务器
    $php_mailer->Host = $email_options['smtp'];
    //设置为需要验证
    $php_mailer->SMTPAuth = $email_options['auth'];
    //设置用户名
    $php_mailer->Username = $email_options['username'];
    //设置密码
    $php_mailer->Password = $email_options['password'];
    //设置发件人邮箱
    $php_mailer->From = $email_options['from_address'];
    //设置发件人名字
    $php_mailer->FromName = $email_options['from_name'];
    //添加收件人地址，可以多次使用来添加多个收件人
    if (is_array($address)) {
        foreach ($address as $v) {
            $php_mailer->AddAddress($v);
        }
    } else {
        $php_mailer->AddAddress($address);
    }
    //设置邮件标题
    $php_mailer->Subject = $subject;
    //设置邮件正文
    $php_mailer->Body = $content;
    //发送邮件
    if (!$php_mailer->Send()) {
        return ['error' => 1, 'message' => $php_mailer->ErrorInfo];
    } else {
        return ['error' => 0];
    }
}

function hide_name($name) {
    $str_len = mb_strlen($name, 'utf-8');
    $first_str = mb_substr($name, 0, 1, 'utf-8');
    $last_str = mb_substr($name, -1, 1, 'utf-8');
    return $str_len == 2 ? $first_str . str_repeat('*', mb_strlen($name, 'utf-8') - 1) : $first_str . str_repeat('*', $str_len - 2) . $last_str;
}

function hide_mobile($mobile) {
    return substr_replace($mobile, '****', 3, 4);
}

function hide_id_number($id_number) {
    $hide_id_number = $id_number;
    $id_number_length = strlen($id_number);
    if ($id_number_length == 15) {
        $hide_id_number = substr_replace($id_number, ' **** **** ', 4, 8);
    } else if ($id_number_length == 18) {
        $hide_id_number = substr_replace($id_number, ' **** **** ', 4, 10);
    }
    return $hide_id_number;
}

/**
 * 更新用户等级
 * @param int $user_id 用户id
 */
function update_level($user_id) {
    $user_model = D('Admin/User');
    $user_level_model = M('user_level');
    $user_log_model = M('user_log');
    $max_get_commission_number = $user_level_model->max('get_commission_number');
    for ($i = 1; $i <= $max_get_commission_number; $i++) {
        $userinfo = $user_model->getLevelDetail($user_id);
        //直推人数
        $direct_count = $user_model->where("level_id>0 and recommender_id={$userinfo['id']}")->count('id');
        //团队人数
        $team_user_ids = $user_model->getTeamUserIds($userinfo['id'], $userinfo['get_commission_number'], false, false);
        $team_count = count($team_user_ids);
        //获取用户满足条件所处最高等级
        $highest_level = $user_level_model->field('id,name')
                ->where("level_up_direct_user_number<={$direct_count} and level_up_team_user_number<={$team_count}")
                ->order('id desc')
                ->find();
        //如果用户当前级别小于当前可提升至的最高级别
        if ($userinfo['level_id'] < $highest_level['id']) {
            //更新用户等级
            $user_model->where("id={$userinfo['id']}")->setField('level_id', $highest_level['id']);
            //添加用户日志
            $data = [
                'user_id' => $userinfo['id'],
                'type' => 3,
                'operator_name' => '系统',
                'content' => '用户直推人数达到' . $direct_count . '人，团队人数达到' . $team_count . '，等级由' . $userinfo['level_name'] . '提升为' . $highest_level['name'],
                'create_time' => date('Y-m-d H:i:s')
            ];
            $user_log_model->add($data);
        }
        $user_id = $userinfo['recommender_id'];
        if (!$user_id) {
            break;
        }
    }
}

/**
 * 返佣
 * @param int $user_id 用户id
 * @param double $commission 返佣金额
 * @param int $data_from 数据来源
 * @param int $data_id 数据id
 */
function return_commission($user_id, $commission, $data_from, $data_id) {
    $user_model = D('Admin/User');
    //直推奖励
    $userinfo = $user_model->getLevelDetail($user_id);
    $money = round($commission * $userinfo['direct_commission_percent'] / 100, 2);
    $user_account_model = M('user_account');
    $user_money_model = M('user_money');
    if ($money > 0) {
        $balance = $userinfo['money'] + $money;
        //添加佣金记录
        $data = [
            'user_id' => $userinfo['id'],
            'type' => 1,
            'order_money' => $commission,
            'money' => $money,
            'balance' => $balance,
            'data_from' => $data_from,
            'data_id' => $data_id,
            'title' => '直推佣金奖励',
            'description' => '直推佣金奖励',
            'create_time' => date('Y-m-d H:i:s')
        ];
        $user_money_model->add($data);
        //更新用户账户
        $data = [
            'money' => $balance,
            'total_money' => $userinfo['total_money'] + $money,
            'total_sales' => $userinfo['total_sales'] + 1
        ];
        $user_account_model->where("user_id={$userinfo['id']}")->save($data);
    }
    $recommender_id = $userinfo['recommender_id'];
    if (!$recommender_id) {
        return;
    }
    //团队奖励
    $max_get_commission_number = M('user_level')->max('get_commission_number');
    $recommender_level = 0;
    for ($i = 2; $i <= $max_get_commission_number; $i++) {
        $userinfo = $user_model->getLevelDetail($recommender_id);
        $money = round($commission * $userinfo['team_commission_percent'] / 100, 2);
        if ($userinfo['level_id'] > $recommender_level && $userinfo['get_commission_number'] >= $i && $money > 0) {
            $balance = $userinfo['money'] + $money;
            //添加佣金记录
            $data = [
                'user_id' => $userinfo['id'],
                'type' => 2,
                'order_money' => $commission,
                'money' => $money,
                'balance' => $balance,
                'data_from' => $data_from,
                'data_id' => $data_id,
                'title' => '团队佣金奖励',
                'description' => '团队佣金奖励',
                'create_time' => date('Y-m-d H:i:s')
            ];
            $user_money_model->add($data);
            //更新用户账户
            $data = [
                'money' => $balance,
                'total_money' => $userinfo['total_money'] + $money
            ];
            $user_account_model->where("user_id={$userinfo['id']}")->save($data);
        }
        $recommender_id = $userinfo['recommender_id'];
        $recommender_level = $userinfo['level_id'];
        if (!$recommender_id) {
            break;
        }
    }
}

/**
 * 企业付款
 * @param string $order_no 商户订单号
 * @param string $openid 用户openid
 * @param double $amount 金额
 * @param string $desc 描述信息
 */
function mch_pay($order_no, $openid, $amount, $desc) {
    vendor('WxPay.lib.WxPay#Api');
    $input = new \WxPayTransfersPay();
    $input->SetPartner_trade_no($order_no);
    $input->SetOpenid($openid);
    $input->SetCheck_name('NO_CHECK');
    //$input->SetRe_user_name('');
    $input->SetAmount($amount * 100);
    $input->SetDesc($desc);
    $result = \WxPayApi::transfersPay($input);
    $return_data = [
        'status' => 0,
        'msg' => '',
        'payment_no' => '',
        'payment_time' => ''
    ];
    if ($result['return_code'] == 'SUCCESS') {
        if ($result['result_code'] == 'SUCCESS') {
            $return_data['status'] = 1;
            $return_data['msg'] = '付款成功';
            $return_data['payment_no'] = $result['payment_no'];
            $return_data['payment_time'] = $result['payment_time'];
        } else {
            $return_data['msg'] = $result['err_code_des'];
        }
    } else {
        $return_data['msg'] = $result['return_msg'];
    }
    return $return_data;
}

function xml_to_array($xml) {
    libxml_disable_entity_loader(true);
    $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $values;
}

function random($min = 0, $max = 1) {
    return $min + mt_rand() / mt_getrandmax() * ($max - $min);
}

function filter_nickname($nickname) {
    $nickname = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $nickname);
    $nickname = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $nickname);
    $nickname = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $nickname);
    $nickname = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $nickname);
    $nickname = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $nickname);
    $nickname = str_replace(array('"', '\''), '', $nickname);
    return addslashes(trim($nickname));
}

function get_user_avatar($avatar) {
    if (!$avatar) {
        return __ROOT__ . '/Public/Common/images/user-default-avatar.png';
    } elseif (strstr($avatar, 'wx.qlogo.cn')) {
        return $avatar;
    } else {
        return __CDN__ . $avatar;
    }
}

function get_cdn_url() {
    $system_upload_config = get_system('upload_config');
    $upload_config = C('UPLOAD_CONFIG');
    if ($system_upload_config['type'] == 'qiniu') {
        $cdn_url = $system_upload_config['cdn_url'];
    } else {
        $cdn_url = $upload_config['cdn_url'] ? $upload_config['cdn_url'] : __ROOT__;
    }
    return $cdn_url;
}

/**
 * 
 * @param string $image_url
 * @param string $cdn_url
 * @return string
 */
function get_image_url($image_url, $cdn_url = '') {
    $cdn_url = $cdn_url ? $cdn_url : get_cdn_url();
    if (!preg_match('/^(?:[a-z]+:)?\/\//i', $image_url)) {
        $prefix = preg_match('/^(?:[a-z]+:)?\/\//i', $cdn_url) ? $cdn_url : root_domain() . $cdn_url;
        $image_url = $prefix . $image_url;
    }
    return $image_url;
}