<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2021/7/22
 * Time: 18:03
 */

namespace JuMsg;


class MsgWx
{
    // 模板参数替换
    public function _tpl_replace($tpl = [], $d = []) {
        if (!$tpl) {
            return $tpl;
        }

        $params = $tpl['params'] ?? [];
        if (!$params) {
            return $tpl;
        }

        $d = arr_only($d, $params);
        $_search = [];
        $_replace = [];
        foreach ($d as $k => $v) {
            $_search[] = '{$' . $k . '}';
            $_replace[] = $v;
        }

        $tpl_data = $tpl['data'] ?? [];
        foreach ($tpl_data as $k => $v) {
            $tpl_data[$k]['value'] = str_replace($_search, $_replace, $v['value']);
        }
        $tpl['data'] = $tpl_data;
        return $tpl;
    }

//    // 获取最新关注一组appId、openid
//    public function get_open_wx($wx = []) {
//        if (!$wx || !is_array($wx)) {
//            return ['', ''];
//        }
//        $openid = end($wx); //value
//        $appId = key($wx); //key (after calling end)
//        return [$appId, $openid];
//    }

    // 发送模板消息
    public function send($uid, $tpl, $d = []) {

        return $re;
    }

    // 获取带参数二维码
//    public function get_qrcode($u) {
//        if (check::intzs($u['uid']) === false) {
//            return rs('账号UID无效!');
//        }
//        $fws_id = ints($u['fws_id']);
//        $config = $this->get_wx_config($fws_id);
//        if (empty($config['appId'])) {
//            return rs('appId参数丢失!');
//        }
//        if (empty($config['appSecret'])) {
//            return rs('appSecret参数丢失!');
//        }
//        if (DEBUG) {
//            return ['code' => -1, 'msg' => '测试账号不支持此功能!'];
//        }
//        $wx = new wechat($config);
//        return $wx->get_qrcode("{$config['appId']}_{$u['uid']}");
//    }

    /**
     * 微信模板消息
     *
     * @param       $uid    用户id
     * @param       $ywlx   业务类型
     * @param array $d      输入数据
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function send_wx($uid, $ywlx, $d = []) {
        $uid = ints($uid);
        if ($uid <= 0) {
            return r_fail( '帐户ID错误!');
        }

        $msg_tpl = RX('msg','tpl');
        $ywlx = glwb($ywlx);//业务类型
        if(!in_arr($ywlx, array_keys($msg_tpl))){
            return r_fail('业务类型不存在!');
        }

        $set = $msg_tpl[$ywlx]['wx'];//配置内容
        if(empty($set)){
            return r_fail('缺少微信模板消息配置!');
        }

        $openid = $d['openid'] ?? '';
        $appId = $d['appId'] ?? RX('msg','wx_app')['appId'];
        $appSecret = $d['appSecret'] ?? RX('msg','wx_app')['appSecret'];
        if(empty($openid)){
            return r_fail('用户没有绑定微信');
        }
        if ( empty($appId) ||  empty($appSecret) ) {
            return r_fail("账号UID:{$uid}关注的公众号配置信息不存在!");
        }
        if(count(array_intersect($set['params'],array_keys($d))) != count($set['params'])){
            return r_fail("消息参数不全");
        }

        //优先使用传来的template_id，可以支持多公众号
        $set['template_id'] = empty($d['template_id']) ?$set['template_id']: $d['template_id'];
        $msg = $this->_tpl_replace($set, $d);
        unset($msg['params']);
        $msg['touser'] = $openid;
        if ( RX('msg','debug') == 1 ) {//测试发送短信
            $re = r_ok('发送成功');
        }
        else{
            $wx = new Wechat(['appId'=>$appId,'appSecret'=>$appSecret]);
            $re = $wx->sendTplMsgToUser(json_encode($msg, true));
        }
        $re['tpl'] = $msg;

        //添加日志
        $log = [
          'ywlx' => $ywlx,
          'uid' => $uid,
          'tpl' => a2j($set),
          'params' => a2j($d),
          'jg' => a2j($re),
          'zt' => $re['code'] == 1 ? 1 : 2,
          'ip' => getip(),
        ];
        M()->insert('x_msg_wx', $log);
        return rs($re['code'] == 1 ? '发送成功!' : '发送失败!',  $re['code'] == 1 ? 1 : -1);

    }
}