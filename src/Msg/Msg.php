<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2021/7/22
 * Time: 18:02
 */

namespace JuMsg;


class Msg
{
    public function init(){
        //创建mysql表
        (new Setting())->create_tables();

        //配置文件
        (new Setting())->publishFiles();

        exit('-- init end --');
    }

    // 异步消息消费
    public function customer_queue() {
        // step1 加锁防并发
        $hcm = 'msg_customer_queue';
        $lock = lock($hcm, 3600);
        if (!$lock) {
            _log('log/msg', sprintf('%s 存在互斥锁，终止脚本', $hcm));
            return r_fail('系统繁忙!');
        }

        // step2 查询要执行任务列表
        $tasks = M()->select('x_msg_queue', 'id,ywlx,fs,uid,data', ['zt' => 1], 'id ASC', 100);
        if (empty($tasks)) {
            unlock($hcm, $lock);
            return r_fail('没有任务!');
        }

        // step3 更新状态为处理中
        $ids = array_column($tasks, 'id');
        if ($ids) {
            $res = M()->update('x_msg_queue', ['zt' => 2], ['id' => $ids],1);
            if(!$res){
                unlock($hcm, $lock);
                return r_fail('处理失败!');
            }
        }

        // step5 发送消息
        foreach ($tasks as $task) {
            $_uid = ints($task['uid']);
            $data = j2a($task['data']);
            $re = $this->send($task['ywlx'], $_uid, j2a($task['fs']), $data);
            if ($re['code'] == 1) {
                $log = "msg_queue_{$task['id']}，发送成功";
            } else {
                $j_data = a2j($task);
                $log = "msg_queue_{$task['id']}，发送失败，记录信息：{$j_data}，错误信息：{$re['msg']}";
                _log('log/msg_fail',$log);
            }
            _log('log/msg',$log);
            $res = M()->update('x_msg_queue', ['zt' => 3], ['id' => $task['id']],1);
            if(!$res){
                return r_fail('发送失败!');
            }
        }
        unlock($hcm, $lock);
        return r_fail('ok!');
    }

    /**
     * 发送消息(异步)
     * 批量添加没有意义，不同用户对应的数据可能是不一样的，既然已经做成异步，单个添加效果是一样的
     *
     * @param       $ywlx       业务类型
     * @param       $uid        用户id
     * @param       $fs         发送方式(znx=站内信 sms=短信 wx=微信模板消息)  ['znx','sms','wx'] 可多选，比如站内信和短信一起发，传['znx','sms']
     * @param array $d
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function async_send($ywlx, $uid, $fs, $d = []) {
        $d = glwb($d);
        $uid = ints($uid);
        $msg_tpl = RX('msg','tpl');
        if(!in_arr($ywlx, array_keys($msg_tpl))){
            return r_fail('业务类型不存在!');
        }
        if(!musta($fs)){
            return r_fail('发送方式不能为空!');
        }
        if ( in_arr('znx',$fs) || in_arr('wx',$fs) ) {
            if ($uid <= 0) {
                return r_fail( '帐户ID错误!');
            }
        }

        M()->insert('x_msg_queue', [
          'ywlx' => $ywlx,
          'fs' =>  a2j($fs),
          'uid' => $uid,
          'data' => a2j($d),
          'ip' => getip(),
        ]);
        return  r_ok('ok');
    }


    /**
     *  发送消息(同步)
     *
     * @param       $ywlx     业务类型
     * @param       $uid    用户id
     * @param       $fs     ['znx','sms','wx'] 可多选，比如站内信和短信一起发，传['znx','sms']
     * @param array $d      消息内容用到的参数
     *
     * @return array|mixed
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function send($ywlx,$uid, $fs,  $d = []) {
        $d = glwb($d);
        $uid = ints($uid);
        $msg_tpl = RX('msg','tpl');
        if(!in_arr($ywlx, array_keys($msg_tpl))){
            return r_fail('业务类型不存在!');
        }
        if(!musta($fs)){
            return r_fail('发送方式不能为空!');
        }

        //站内信
        if (in_arr('znx',$fs)) {
            $re['znx'] = (new MsgZnx())->send_znx($uid, $ywlx, $d);
        }

        //微信模板消息
        if (in_arr('wx',$fs)) {
            $re['wx'] = (new MsgWx())->send_wx($uid, $ywlx, $d);
        }

        //发短信
        if (in_arr('sms',$fs)) {
            $re['sms'] = (new MsgSms())->send_sms($uid, $ywlx, $d);
        }

        //单一发送方式回传处理
        if (count($fs) == 1) {
            return $re[array_shift($fs)];
        }
        return r_ok( '发送完成!',$re);
    }

}