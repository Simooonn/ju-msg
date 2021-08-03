<?php

//过滤文本_支持数组
if (!function_exists('glwb')) {
    function glwb($data = "")
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = glwb($value);
            }
            return $data;
        }
        else {
            $aq_tags = '<div><p><b><img><br>';
            $data    = strip_tags($data, $aq_tags);
            while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|style|evel|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background[^-]|codebase|dynsrc|lowsrc)([^><]*)/i', $data, $match)) {
                $data = str_ireplace($match[0], $match[1] . $match[3], $data);
            }
            while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $data, $match)) {
                $data = str_ireplace($match[0], $match[1] . $match[3], $data);
            }
            $text = str_replace(["'", "\"", "\\", "<", ">", "chr(0)", "chr(16)"], ['&#39;', '&#34;', '&#92;', '&#60;', '&#62;', '', ''], $data);
            return trim($text);
        }
    }
}

//返回app/x下的扩展配置文件配置 return x config
if (!function_exists('RX')) {
    function RX($filename, $key = '')
    {
        static $return = [];
        $filePath = APP_X . $filename . ".php";
        if (is_file($filePath)) {
            if (!isset($return[$filename])) {
                $return[$filename] = require_once($filePath);
            }
        }
        else {
            _e404("{$filePath} 文件不存在!");
        }
        $re = $return[$filename];
        if (!empty($key)) {
            if (isset($return[$filename][$key])) {
                $re = $return[$filename][$key];
            }
            else {
                _e404("{$filename} 未找到 {$key} 配置项!");
            }
        }
        return $re;
    }
}

//404页面
if (!function_exists('_e404')) {
    function _e404($error = '')
    {
        if (DEBUG) {
            if (TRACE) {
                trace::halt($error);
            }
            exit('<div style="margin:100px;padding: 10px 20px;font-size: 16px;border-radius:5px;background:#f60;color:#fff;line-height:34px;">' . $error . '</div>');
        }
        header('HTTP/1.0 404 Not Found');
        exit("<html><head><title>404 Not Found</title></head><body>404 Not Found</body></html>");
    }
}

//字符串是否包含某个关键词
if (!function_exists('instr')) {
    function instr($all,$str){
        return !is_array($all) && stripos($all,$str)!==false ? 1 : 0;
    }
}

//批量验证
if (!function_exists('check')) {
    function check($list = [])
    {
        $yz   = new check();
        $list = array_filter($list);
        if (is_array($list)) {
            foreach ($list as $v) {
                $re = $yz->_check($v[0], $v[1]);
                if ($re !== true) {
                    if (isset($v[2]) && $v[2] != "") {
                        return $v[2];
                    }
                    else {
                        return $re;
                    }
                }
            }
            return true;
        }
        else {
            return true;
        }
    }
}

//正整数返回该值,非正整数返回$v
if (!function_exists('ints')) {
    function ints($b, $v = 0)
    {
        if (!preg_match('/^[0-9]*$/', $b)) {
            return $v;
        }
        if ($b > 9999999999) {
            return $v;
        }
        $i = intval($b);
        return $i ? $i : $v;
    }
}

//替换字符串中的变量 {$xxx}
if (!function_exists('mb_th')) {
    /**
     *
     *
     * @param $str
     * @param $a
     *
     * @return string|string[]
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    function mb_th($str, $a) {
        if ($a == '') {
            return (string)$str;
        }
        elseif (is_array($a)) {
            $replace = array_keys($a);
            foreach ($replace as &$v) {
                $v = '{$' . $v . '}';
            }
            return str_replace($replace, $a, $str);
        }
        return (string)$str;
    }
}

//获取用户id
if (!function_exists('x_userid')) {
    function x_userid()
    {
        return session_get(['user', 'uid']);
    }
}

//读session
if (!function_exists('session_get')) {
    function session_get($key = '')
    {
        if ($key == '') {
            return $_SESSION;
        }
        $val = "";
        if (isset($key[0]) and empty($key[1]) && isset($_SESSION[$key[0]])) {
            $val = $_SESSION[$key[0]];
        }
        else if (isset($key[1]) && isset($_SESSION[$key[0]])) {
            $val = $_SESSION[$key[0]][$key[1]];
        }
        return $val;
    }
}

/**
 * 获取客户端IP地址
 *
 * @param $type 1=返回IP地址 0=返回IPV4地址数字
 * @param $ms   1=严格模式,防伪装
 *
 * @return mixed
 */
if (!function_exists('getip')) {
    function getip($type = 1, $ms = 1)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if (null !== $ip) {
            return $ip[$type];
        }
        if ($ms != 1) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
            }
            elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        }
        elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? [$long, $ip] : [0, '0.0.0.0'];
        return $ip[$type];
    }
}

/**
 * 获取时间戳对应日期（不传参默认当前日期）
 *
 * @param null $time
 *
 * @return false|string
 * @author wumengmeng <wu_mengmeng@foxmail.com>
 */
if (!function_exists('ymdhis')) {
    function ymdhis($time = null) {
        $time = $time ?? time();
        return date('Y-m-d H:i:s', $time);
    }
}

//缓存锁
//上锁
if (!function_exists('lock')) {
    function lock($k, $time) {
        $redis  = new redisx();
        $random = suiji(8, '123456789');
        $rs     = $redis->set2($k, $random, ['nx', 'ex' => $time]);
        if ($rs) {
            return $random;
        }
        else {
            return 0;
        }
    }
}
//解锁
if (!function_exists('unlock')) {
    function unlock($k, $v) {
        $redis = new redisx();
        if ($redis->get($k) == $v) {
            $redis->del($k);
        }
    }
}

if (!function_exists('arr_only')) {
    function arr_only($array, $keys) {
        return array_intersect_key($array, array_flip((array)$keys));
    }
}

//返回x下的扩展配置文件配置 return x config
if (!function_exists('RX')) {
    function RX($filename, $key = '') {
        static $return = [];
        $filePath = XPATH . $filename . ".php";
        if (is_file($filePath)) {
            if (!isset($return[$filename])) {
                $return[$filename] = require_once($filePath);
            }
        }
        else {
            _e404("{$filePath} 文件不存在!");
        }
        $re = $return[$filename];
        if (!empty($key)) {
            if (isset($return[$filename][$key])) {
                $re = $return[$filename][$key];
            }
            else {
                _e404("{$filename} 未找到 {$key} 配置项!");
            }
        }
        return $re;
    }
}


/**
 * curl 操作函数
 * $d 参数列表
 * url  请求网址url
 * do  请求方式 DELETE/PUT/GET/POST 默认为GET data不为空则POST
 * tz  跳转跟随 0不跟随 1跟随 默认1
 * data  请求数据 支持数组方式
 * ref  来路
 * llq  浏览器头
 * qt  其他header信息 多个用数组传递
 * cookie cookie文件路径或者cookie信息 当为文件时.txt结尾
 * time 超时时间 默认10
 * daili 为空不用代理 array('CURLOPT_PROXY','CURLOPT_PROXYUSERPWD')
 * headon  是否返回header信息 默认0不返回 1=返回
 * code  是否返回HTTP状态码 code=1开启=>将return信息为 ['状态码','获取到的内容']
 */
if (!function_exists('chttp')) {
    function chttp($d = []) {
        $mrd = ['url' => '', 'do' => '', 'tz' => '', 'data' => '', 'ref' => '', 'llq' => '', 'qt' => '', 'cookie' => '', 'time' => '', 'daili' => [], 'headon' => '', 'code' => ''];
        $d   = array_merge($mrd, $d);

        $url = $d['url'];
        if ($url == "") {
            exit("URL不能为空!");
        }
        $header = [];

        if ($d['llq']) {
            $header[] = "User-Agent:" . $d['agent'];
        }
        else {
            $header[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64)AppleWebKit/537.36 (KHTML, like Gecko)Chrome/63.0.3239.26 Safari/537.36';
        }
        if ($d['ref']) {
            $header[] = "Referer:" . $d['ref'];
        }

        $ch = curl_init($url);
        //cookie 文件/文本
        if ($d['cookie'] != "") {
            if (substr($d['cookie'], -4) == ".txt") {
                //文件不存在则生成
                if (!wjif($d['cookie'])) {
                    wjxie($d['cookie'], '');
                }
                $d['cookie'] = realpath($d['cookie']);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $d['cookie']);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $d['cookie']);
            }
            else {
                $cookie   = 'cookie: ' . $d['cookie'];
                $header[] = $cookie;
            }
        }
        //附加头信息
        if ($d['qt']) {
            foreach ($d['qt'] as $v) {
                $header[] = $v;
            }
        }

        //代理
        if (count($d['daili']) == 2) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $d['daili'][0]);
            curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $d['daili'][1]);
        }

        $postData = $d['data'];
        $timeout  = $d['time'] == "" ? 10 : ints($d['time'], 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");

        //跳转跟随
        if ($d['tz'] == "0") {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        }
        else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        //SSL
        if (substr($url, 0, 8) === 'https://') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        //请求方式
        if (in_array(strtoupper($d['do']), ['DELETE', 'PUT'])) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($d['do']));
        }
        else {
            //POST数据
            if (!empty($postData)) {
                if (is_array($postData)) {
                    $postData = http_build_query($postData);
                }
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            }
            //POST空内容
            elseif (strtoupper($d['do']) == "POST") {
                curl_setopt($ch, CURLOPT_POST, 1);
            }
        }
        if ($d['headon'] == "1") {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        //超时时间
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (int)$timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int)$timeout);

        //执行
        $content = curl_exec($ch);
        $content = to_utf8($content);
        //是否返回状态码
        if ($d['code'] == "1") {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $content  = [$httpCode, $content];
        }

        curl_close($ch);
        return $content;
    }
}

/**
 * json转数组
 *
 * @param $json
 *
 * @return array|mixed
 * @author wumengmeng <wu_mengmeng@foxmail.com>
 */
if (!function_exists('j2a')) {
    function j2a($json,$mode = 1) {
        return is_string($json) ? json_decode($json, true) : [];
    }
}

/**
 * 数组转json
 *
 * @param array $d
 *
 * @return false|string
 * @author wumengmeng <wu_mengmeng@foxmail.com>
 */
if (!function_exists('a2j')) {
    function a2j($d = []) {
        return is_array($d) ? json_encode($d, JSON_UNESCAPED_UNICODE) : '';
    }
}


/**
 * 成功返回
 *
 * @param string $msg
 * @param array  $data
 *
 * @return array
 * @author wumengmeng <wu_mengmeng@foxmail.com>
 */
if (!function_exists('r_ok')) {
    function r_ok($msg = '', $data = []) {
        return rs($msg, 1, $data);
    }
}
/**
 * 失败返回
 *
 * @param        $msg
 * @param int    $code
 * @param array  $data
 *
 * @return array
 * @author wumengmeng <wu_mengmeng@foxmail.com>
 */
if (!function_exists('r_fail')) {
    function r_fail($msg = '', $code = -1, $data = []) {
        return rs($msg, $code, $data);
    }
}
//统一返回方法
if (!function_exists('rs')) {
    function rs($msg = '', $code = -1, $data = '', $qt = []) {
        $rs = ['code' => $code, 'msg' => $msg];
        if ($data) {
            $rs['data'] = $data;
        }
        if (!empty($qt) && is_array($qt)) {
            $rs = array_merge($rs, $qt);
        }
        return $rs;
    }
}

if (!function_exists('_log')) {
    /**
     *  记录日志
     *
     * @param string $path  path路径
     * @param        $data  data内容
     *
     * @return bool
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    function _log($path = "mylog", $data, $lx = 0)
    {
        $path     = str_replace('::', DS, $path);
        $log_path = LOG . $path;
        $file     = $log_path . DS . date('Y-m-d') . ".txt";
        $time     = date('Y-m-d H:i:s');
        $ip       = getip(1);
        $url      = geturl();
        wjxie(LOG . DS . 'logs' . DS . date('Y-m-d') . ".txt", "{$time}\t{$ip}\t{$path}\t{$url}\t{$data}\r\n");//记录总日志
        return wjxie($file, "{$time}\t{$ip}\t{$url}\t{$data}\r\n");
    }
}

//数组去空去重
if (!function_exists('array_x')) {
    function array_x($a) {
        return is_array($a) ? array_filter(array_unique($a)) : $a;
    }
}

//重置二维数组键名为指定字段的值
if (!function_exists('arr_key')) {
    function arr_key($arr, $key) {
        if (!is_array($arr)) {
            return [];
        }
        $rt = [];
        foreach ($arr as $v) {
            $rt[$v[$key]] = $v;
        }
        return $rt;
    }
}

if (!function_exists('shouji')) {
    function is_shouji($str) {
        return preg_match('/^1[3456789]{1}\d{9}$/', $str) ? true : false;
    }
}

if (!function_exists('in_arr')) {
    /**
     * 判断字符串或数字是否在一维数组内
     *
     * [默认区分大小写,第3个参数设置为true时,不区分大小写]
     *
     * 比in_array()功能要精简一些
     * 解决了in_array()严格模式下,会对比数据类型问题,如 in_array(2, ['1-1','2','3'],true),返回为false
     * 非严格模式下,会将字符转为数字后对比,如 in_array(5, ['5-1','2','3']) 或 in_array('5-1', [5,'2','3']),返回为true
     *
     * @param $str
     * @param $array
     *
     * @return bool
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    function in_arr($str, $array, $lower = false) {
        if (!is_array($array)) {
            return false;
        }
        $array = array_map('strval', $array);
        $str   = strval($str);
        if ($lower === true) {
            $array = array_map('strtolower', $array);
            $str   = strtolower($str);
        }
        //第三个参数不需要,因为已经将数据强制转为字符串类型了。如果该参数设置为 TRUE,则 in_array() 函数检查搜索的数据与数组的值的类型是否相同。
        return in_array($str, $array);
    }
}

if (!function_exists('in_arrx')) {
    /**
     * 判断字符串或数字是否在一维数组内 [不区分大小写]
     *
     * @param $str
     * @param $array
     *
     * @return bool
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    function in_arrx($str, $array) {
        return in_arr($str, $array, true);
    }
}

if (!function_exists('musta')) {
    /**
     * 参数必须是数组，且不能为空数组
     *
     * @param $arr
     *
     * @return bool
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    function musta($arr) {
        return is_array($arr) && count($arr) > 0;
    }
}

if (!function_exists('ids')) {
    /**
     * 验证正整数和逗号
     *
     * @param $str
     *
     * @return bool
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    function ids($str) {
        return preg_match('/^(([1-9][0-9]*)(,([1-9][0-9]*))*)$/', $str) ? true : false;
    }
}