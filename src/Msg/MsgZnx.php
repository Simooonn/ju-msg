<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2021/7/22
 * Time: 18:03
 */

namespace JuMsg;


class MsgZnx
{
    //消息列表
    public function msg_list($uid,$d=[]){
        $uid=ints($uid);//uid
        $d=glwb($d);
        $zt=$d['zt'];//状态 0=未读,1=已读
        $where=[];
        if($uid!=""){
            $uid=ints($uid);
            if($uid<=0){
                return ['code'=>-1000,'msg'=>'请先登录!'];
            }
            $where[]="uid='{$uid}'";
        }

        if($zt!=""){
            $zt=ints($zt)==0 ? 0 : 1;
            $where[]="zt = '{$zt}'";
        }

        $where=implode(" and ",$where);
        $limit = ints($d['limit'])<=0 ? 10 : ints($d['limit']);
        if($limit>200){
            return ['code'=>-1,'msg'=>'每页最多200条!'];
        }
        $page = ints($d['page']) <= 0 ? 1 : ints($d['page']);//分页

        $rs=M()->s_page('x_msg_znx', 'id,zt,lx,bt,sj,uid,nr,tan', $where, ['id','desc'] , $limit, $page);
        $re=['code'=>1,'msg'=>'查询完成','page'=>$page,'pagesize'=>$limit];
        $re=array_merge($re,$rs);
        //数据处理
        if($re['count']>0){
            $msg_set=RX('msg','znx');
            $ids=[];
            $msg_zt=$msg_set['zt'];
            $msg_lx=$msg_set['lx'];
            foreach($re['data'] as &$v){
                $v=[
                  'id'=>$v['id'],
                  'zt'=>$v['zt'],
                  'uid'=>$v['uid'],
                  'zt_txt'=>$msg_zt[$v['zt']] ?: '未知',
                  'lx'=>$v['lx'],
                  'lx_txt'=>$msg_lx[$v['lx']] ?: '未知',
                  'bt'=>$v['bt'],
                  'sj'=>sjzh($v['sj'],2),
                  'nr'=>glwb_f($v['nr']),
                  'tan'=>ints($v['tan']),
                ];
                if($v['zt']==0 && $v['uid']==x_userid() && $v['tan'] != 1){
                    $ids[]=$v['id'];
                }
            }
            //设为已读
            if(count($ids)>0 && $_ENV['dlfs'] != 2){
                $this->up_znx_zt(x_userid(),$ids);
            }
        }
        return $re;
    }

    //设为已读,支持批量(数组)
    public function up_znx_zt($uid,$id){
        $uid=ints($uid);
        $id=glwb($id);
        if(is_array($id)){
            $ids=[];
            foreach($id as $v){
                $ids[]=ints($v);
            }
            $ids=array_x($ids);
        }
        else{
            $ids=[ints($id)];
        }
        if(count($ids)>0){
            M()->update('x_msg_znx',['zt'=>1],['uid'=>$uid,'id'=>$ids]);
            $this->qchc_wdznx_num($uid);
        }
        return ['code' => 1, 'msg' => '设置成功!'];
    }

    //删除消息(支持批量)
    public function del_znx($uid,$id){
        $uid = ints($uid);
        $id = glwb($id);
        if(is_array($id)){
            $ids=[];
            foreach($id as $v){
                $ids[]=ints($v);
            }
            $ids=array_x($ids);
        }else{
            $ids=[ints($id)];
        }
        if(count($ids)>0){
            M()->delete('x_msg_znx', ['uid'=>$uid,'id'=>$ids]);
            $this->qchc_wdznx_num($uid);
        }
        return ['code' => 1, 'msg' => '删除完成!'];
    }

    //删除指定状态
    public function del_znx_zt($uid,$zt){
        $uid=ints($uid);
        $zt=ints($zt);
        M()->delete('x_msg_znx',['uid'=>$uid,'zt'=>$zt]);
        $this->qchc_wdznx_num($uid);
        return ['code' => 1, 'msg' => '删除完成!'];
    }

    //获取未读消息数
    public function get_znx_num($uid){
        $uid=ints($uid);
        $hcm="msg_wdznx_{$uid}";
        $num=S($hcm);
        if($num!=""){
            return $num;
        }
        $num=M()->count("x_msg_znx",['uid'=>$uid,'zt'=>0]);
        S($hcm,$num,120);
        return $num;
    }

    //获取最新弹窗消息
    public function get_znx_tan($uid){
        $uid=ints($uid);
        $nr='';
        if($uid > 0){
            $nr=M()->select("x_msg_znx",'id,bt,nr,sj',['uid'=>$uid,'zt'=>0,'tan'=>1], ['id','desc'], 1);
        }
        if(empty($nr)){
            return rs('无数据');
        }
        $nr=glwb_f($nr[0]);
        return rs('查询成功',1,$nr);
    }

    /**
     * 清除消息数量缓存
     *
     * @param $uid
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function qchc_wdznx_num($uid){
        S("msg_wdznx_".ints($uid),null);
    }

    /**
     * 发送站内消息
     *
     * @param       $uid    用户id
     * @param       $ywlx   业务类型
     * @param array $d      输入数据
     *
     * @return array
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function send_znx($uid, $ywlx, $d = []) {
        $uid = ints($uid);
        if ($uid <= 0) {
            return r_fail( '帐户ID错误!');
        }

        $msg_tpl = RX('msg','tpl');
        $ywlx = glwb($ywlx);//业务类型
        if(!in_arr($ywlx, array_keys($msg_tpl))){
            return r_fail('业务类型不存在!');
        }

        $set = $msg_tpl[$ywlx]['znx'];//配置内容
        if(empty($set)){
            return r_fail('缺少站内信配置!');
        }

        $d = glwb($d);
        $lx = ints($set['lx']);//0=系统消息, 1=业务消息, 2=活动消息
        $bt = mb_th($set['bt'], $d);//类型
        $nr = mb_th($set['nr'], $d);//内容
        $tan = $set['tan'] == 1 ? 1 : 0;//是否弹窗 1=是
        $data = [
          'uid' => $uid,
          'ywlx' => $ywlx,
          'lx' => $lx,
          'zt' => 1,
          'bt' => $bt,
          'nr' => $nr,
          'czuid' => x_userid(),
          'czip' => getip(),
          'sj' => ymdhis(),
          'tan' => $tan,
        ];
        $id = M()->insert("x_msg_znx", $data, 1);
        if ($id > 0) {
            $this->qchc_wdznx_num($uid);
            return r_ok( "发送消息给ID{$uid}成功!",$id);
        }
        else {
            return r_fail( "发送消息给ID{$uid}失败!");
        }
    }
}