<?php

namespace JuMsg;

class Wechat
{

    const CGI_BIN_QRCODE = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=##TOKEN##';

    const CGI_BIN_SHOWQRCODE = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=##TICKET##';

    const URL_WECHAT_MESSAGE_TEMPLATE_SEND = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=##TOKEN##';
    const URL_WECHAT_GET_ACCESSTOKEN = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=##APPID##&secret=##SECRET##';

    const WECHAT_CREATE_MENU = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s';

    protected $appId;

    protected $appSecret;

    public function __construct($config = [])
    {
        if (!empty($config)) {
            $this->appId     = $config['appId'];
            $this->appSecret = $config['appSecret'];
        }
        else {
            $this->appId     = RX('msg', 'wx_app')['appId'];
            $this->appSecret = RX('msg', 'wx_app')['appSecret'];
        }
    }

    //获取access_token
    public function getWechatAccessToken($t0 = '')
    {
        $hcm = "wechat-{$this->appId}";
        $accessToken = hc_get($hcm);

        if (empty($accessToken) || $t0 == 1) {
            $url = str_replace(['##APPID##','##SECRET##'], [$this->appId, $this->appSecret], self::URL_WECHAT_GET_ACCESSTOKEN);
            $jsondata = chttp(["url" => $url]);
            $data = json_decode($jsondata, true);

            if (empty($data['access_token'])) {
                echo json_encode(rs('授权错误'));
                return;
            }

            $accessToken = $data['access_token'];
            $expiresIn   = $data['expires_in'];
            hc_set($hcm, $accessToken, $expiresIn - 600);
        }
        if (empty($accessToken)) {
            echo json_encode(rs('授权错误'));
            return;
        }

        return $accessToken;
    }

    //发送模板消息
    public function sendTplMsgToUser($data)
    {
        $accessToken = $this->getWechatAccessToken();
        $url = str_replace("##TOKEN##", $accessToken, self::URL_WECHAT_MESSAGE_TEMPLATE_SEND);
        $re          = chttp(["url" => $url, "data" => $data]);
        //Array
        //(
        //    [errcode] => 0
        //    [errmsg] => ok
        //    [msgid] => 1869966693284790273
        //)
        $re = json_decode($re, true);
        if (empty($re['errcode'])) {
            return r_ok('ok', $re);
        }
        return r_fail('发送失败!', -1, $re);
    }



//
//
//    // 创建菜单
//    public function createMenu($data, $accessToken = '')
//    {
//        if (empty($accessToken)) {
//            $accessToken = $this->getWechatAccessToken();
//        }
//        $result = chttp(["url" => sprintf(self::WECHAT_CREATE_MENU, $accessToken), "data" => $data]);
//        return json_decode($result, true);
//    }
//
//    public function api_notice_increment($url, $data = [])
//    {
//        $ch     = curl_init();
//        $header = ['Accept-Charset' => 'utf-8'];
//        curl_setopt($ch, CURLOPT_URL, $url);
//
//        if (!empty($data)) {
//            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        }
//
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
//
//        if (!empty($data)) {
//            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        }
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $tmpInfo = curl_exec($ch);
//
//        if (curl_errno($ch)) {
//            curl_close($ch);
//            return $ch;
//        }
//        else {
//            curl_close($ch);
//            return $tmpInfo;
//        }
//
//    }
//
//
//    //获取用户信息
//    public function getUserInfo($openid)
//    {
//        if (empty($openid)) {
//            echo json_encode(['msg' => "授权错误", 'code' => -1]);
//            exit();
//        }
//
//        $accessToken = $this->getWechatAccessToken();
//        $url         = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $openid . '&lang=zh_CN';
//        $data        = chttp(["url" => $url]);
//        return json_decode($data, true);
//    }
//
//
//    // 发送客服消息
//    public function sendCustomMessageTxt($openid, $content)
//    {
//        $tpl         = '{
//                   "touser":"%s",
//                   "msgtype":"text",
//                   "text":
//                        {
//                            "content":"%s"
//                        }
//                    }';
//        $data        = sprintf($tpl, $openid, $content);
//        $accessToken = $this->getWechatAccessToken();
//        $url         = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $accessToken;
//        $result      = chttp(["url" => $url, "data" => $data]);
//        return json_decode($result, true);
//    }
//
//    /**
//     * 获取JSSDK的jsapi_ticket
//     */
//    public function get_ticket()
//    {
//        $ticketCacheKey = sprintf('brandWechatTicket-%s', $this->appId);
//        $ticket         = hc_get($ticketCacheKey);
//        if ($ticket) {
//            return $ticket;
//        }
//        $accessToken = $this->getWechatAccessToken();
//        $result      = chttp(["url" => "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $accessToken . "&type=jsapi"]);
//        $tickets     = json_decode($result, true);
//        $ticket      = $tickets["ticket"];
//        hc_set($ticketCacheKey, $ticket, 6000);
//        return $ticket;
//    }
//
//    /**
//     * 获取jssdk签名
//     */
//    public function get_signature($data)
//    {
//        $datas = [
//          'jsapi_ticket' => $data["ticket"],
//          'noncestr'     => $data["noncestr"],
//          'timestamp'    => $data["timestamp"],
//          'url'          => $data["url"],
//        ];
//
//        $param = "";
//        foreach ($datas as $k => $v) {
//            $param .= $k . '=' . $v . '&';
//        }
//
//        $p = rtrim($param, '&');
//
//        //计算签名
//        $signature = sha1($p);
//        return $signature;
//    }
//
//    // 获取生成带标记的公众号二维码
//    public function get_qrcode($scene_str, $sx = 0)
//    {
//        $wechatUserQrCacheKey = sprintf('wechatUserQrCacheKey-%s-%s', $this->appId, $scene_str);
//        $wechatUserQr         = hc_get($wechatUserQrCacheKey);
//        if (empty($wechatUserQr) || ints($sx)) {
//            $expire      = 2590000; // 临时二维码过期时间 最大不超过2592000（即30天）
//            $accessToken = $this->getWechatAccessToken();
//
//            $url = str_replace("##TOKEN##", $accessToken, self::CGI_BIN_QRCODE);
//            // 二维码参数
//            $qrcode       = json_encode([
//              'expire_seconds' => $expire,
//              'action_name'    => 'QR_STR_SCENE',
//              'action_info'    => [
//                'scene' => [
//                  'scene_str' => (string)$scene_str,
//                ],
//              ],
//            ]);
//            $res1         = $this->api_notice_increment($url, $qrcode);
//            $data         = json_decode($res1, true);
//            $ticket       = $data['ticket'];
//            $qrUrl        = str_replace("##TICKET##", $ticket, self::CGI_BIN_SHOWQRCODE);
//            $res2         = $this->api_notice_increment($qrUrl);
//            $wechatUserQr = base64_encode($res2);
//            hc_set($wechatUserQrCacheKey, $wechatUserQr, 2589400);
//        }
//
//        if ($wechatUserQr) {
//            return ['code' => 1, 'data' => sprintf('data:image/png;base64,%s', $wechatUserQr)];
//        }
//
//        return rs('请刷新页面重新尝试!');
//
//    }

}