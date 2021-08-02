<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2021/7/22
 * Time: 18:02
 */

namespace JuMsg;


class MsgSms
{

    /**
     * 发短信
     *
     * @param       $uid    用户id
     * @param       $ywlx   业务类型
     * @param array $d      输入数据
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function send_sms($uid, $ywlx, $d) {
        $msg_tpl = RX('msg','tpl');
        $ywlx = glwb($ywlx);//业务类型
        if(!in_arr($ywlx, array_keys($msg_tpl))){
            return r_fail('业务类型不存在!');
        }

        $set = $msg_tpl[$ywlx]['sms'];//配置内容
        if(empty($set)){
            return r_fail('缺少短信配置!');
        }

        $set['xz'] = $d['sms_xz'] ?? $set['xz'];//自定义限制开关
        $uid = ints($uid);
        $d = glwb($d);
        $sjhm = $d['sjhm'];//手机号码
        $qm = $d['qm'] ?? RX('msg','sms')['qm'];//签名,若都存在，优先使用传来的签名，可以支持多签名
//        $set['mbid'] = !empty($d['mbid']) ? $d['mbid'] : $set['mbid'];//同上，支持多模板[注是同一类型的短信，支持不同的模板编号]
          
        //参数校验
        $rule = [
          ['must', $set['mbid'], '缺少短信模板ID!'],
          ['shouji', $sjhm, '手机号码错误!']
        ];
        $check = check($rule);
        if ($check !== true) {
            return r_fail($check);
        }

        //并发锁
        $hcm = $uid > 0 ? "xt_sms_{$uid}" : "xt_sms2_{$sjhm}";
        $suo = lock($hcm, 60);
        if ($suo == 0) {
            return r_fail(  '系统繁忙,请重试!');
        }

        //频率限制
        if (ints($set['xz']) == 1 && $uid == 0) {
            //1.对每个手机号码判断，发送间隔必须超过60秒
            $scfasj = M()->get_field('x_msg_sms', 'sj', "sjhm='{$sjhm}' and zt=1 order by id desc");
            if ($scfasj == "") {
                $scfasj = "2011-1-1";
            }
            $fsjg_sj = ints(RX('msg','sms')['fsjg_sj']) > 0 ? ints(RX('msg','sms')['fsjg_sj']) : 60;
            if (time() - strtotime($scfasj) < $fsjg_sj) {
                unlock($hcm, $suo);
                return r_fail(  '发送间隔太短!');
            }

            //2.对每个手机号码判断，每个手机号码每半小时内只能发送15次
            $sjh_max = ints(RX('msg','sms')['sjh_max']) > 0 ? ints(RX('msg','sms')['sjh_max']) : 15;
            $time30 = date('Y-m-d H:i:s', time() - 1800);
            if (M()->count('x_msg_sms', "sjhm='{$sjhm}' and sj>'{$time30}'") >= $sjh_max) {
                unlock($hcm, $suo);
                return r_fail(  '号码发送频繁，请稍后再试!');
            }
        }
        if (ints($set['xz']) == 1 && $uid > 0) {
            //3.对帐户进行判断，每个帐户每半小时只能发7次
            $uid_max = ints(RX('msg','sms')['uid_max']) > 0 ? ints(RX('msg','sms')['uid_max']) : 7;
            if (M()->count('x_msg_sms', "uid='{$uid}' and sj>'{$time30}'") >= $uid_max) {
                unlock($hcm, $suo);
                return r_fail(  '帐户发送频繁，请稍后再试!');
            }
        }

        $params = arr_only($d, $set['params']);
        if(count($params) != count($set['params'])){
            unlock($hcm, $suo);
            return r_fail(  '短信缺少参数!');
        }

        //发送短信
        if ( RX('msg','debug') == 1 ) {//测试发送短信
            $sendx =r_ok('短信发送成功');
        } else {
            $api = RX('msg', 'sms');
            $send_data = [
              'url' => $api['url'],
              'do' => 'POST',
              'data' => [
                'access_key' => $api['access_key'],
                'code' => $set['mbid'],
                'sign' => $qm,
                'mobile' => $sjhm,
                'Timestamp'=>time(),
                'data' => $params,//短信内容参数
              ],
            ];
            $send = chttp($send_data);
            $error = '';
            $sendx = j2a($send, 1);
        }
        if (instr($sendx['msg'], '短信发送成功') > 0) {
            $zt = 1;
        } else {
            $zt = 2;
            if (instr($sendx['msg'], '天级流控') > 0) {
                $fun = implode(",", func_get_args());
                $error = ":达到运营商每日最大发送数量!";
                xlog(__METHOD__, "发送短信失败:" . glwb(str_replace('"', '', $send)) . "|{$fun}", $sjhm, 0);
            }
        }

        //添加日志
        $log_data = [
          'zt' => $zt,
          'uid' => $uid,
          'sjhm' => $sjhm,
          'mbid' => $set['mbid'],
          'nr' => a2j($params),
          'jg' => a2j(j2a($send)),
          'czip' => getip(),
          'sj' => ymdhis(),
        ];
        M()->insert('x_msg_sms', $log_data, 1);
        unlock($hcm, $suo);
        return rs($zt == 1 ? '发送成功' : '发送失败' . $error, $zt != 1 ? -1 : 1);

    }
}