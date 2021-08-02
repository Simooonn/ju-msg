<?php
/**
 * Created by PhpStorm.
 * User: wumengmeng <wu_mengmeng@foxmail.com>
 * Date: 2021/7/22
 * Time: 18:02
 */

namespace JuMsg;


class Setting
{

    /**
     * 创建消息-短信表
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    private function create_x_msg_sms(){
        //如果已有表，先删除，再重新创建
        
        //1.删除表
        $sql = "DROP TABLE IF EXISTS `x_msg_sms`";
        $res = M()->query($sql);
        if($res === true){
            echo "drop table x_msg_sms ok!".'<br>';
        }
        else{
            echo "drop table x_msg_sms fail!".'<br>';
        }
        
        //2.重建表
        $sql = "
CREATE TABLE `x_msg_sms`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '账号UID',
  `ywlx` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '业务类型 配置中tpl下自定义',
  `zt` int(11) NOT NULL DEFAULT 0 COMMENT '结果状态 1成功 2失败',
  `sjhm` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '手机号码',
  `mbid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '短信模板编号',
  `nr` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `jg` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '结果',
  `czip` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作IP',
  `sj` datetime NULL DEFAULT NULL COMMENT '发送时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC";
        $res = M()->query($sql);
        if($res === true){
            echo "create table x_msg_sms ok!".'<br>';
        }
        else{
            echo "create table x_msg_sms fail!".'<br>';
        }
    }

    /**
     * 创建消息-微信表
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    private function create_x_msg_wx(){
        //如果已有表，先删除，再重新创建

        //1.删除表
        $sql = "DROP TABLE IF EXISTS `x_msg_wx`";
        $res = M()->query($sql);
        if($res === true){
            echo "drop table x_msg_wx ok!".'<br>';
        }
        else{
            echo "drop table x_msg_wx fail!".'<br>';
        }

        //2.重建表
        $sql = "
CREATE TABLE `x_msg_wx`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '账号UID',
  `ywlx` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '业务类型 配置中tpl下自定义',
  `zt` tinyint(4) NOT NULL DEFAULT 1 COMMENT '结果状态 1成功 2失败',
  `tpl` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '模板内容',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '模板参数',
  `jg` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '发送结果',
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作IP',
  `add_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `up_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信模板消息' ROW_FORMAT = DYNAMIC";
        $res = M()->query($sql);
        if($res === true){
            echo "create table x_msg_wx ok!".'<br>';
        }
        else{
            echo "create table x_msg_wx fail!".'<br>';
        }
    }

    /**
     * 创建消息-站内信表
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    private function create_x_msg_znx(){
        //如果已有表，先删除，再重新创建

        //1.删除表
        $sql = "DROP TABLE IF EXISTS `x_msg_znx`";
        $res = M()->query($sql);
        if($res === true){
            echo "drop table x_msg_znx ok!".'<br>';
        }
        else{
            echo "drop table x_msg_znx fail!".'<br>';
        }

        //2.重建表
        $sql = "
CREATE TABLE `x_msg_znx`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '账号UID',
  `ywlx` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '业务类型 配置中tpl下自定义',
  `lx` int(11) NOT NULL DEFAULT 0 COMMENT '类型 0=系统消息, 1=业务消息, 2=活动消息',
  `zt` int(11) NOT NULL DEFAULT 0 COMMENT '结果状态 1成功 2失败',
  `bt` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `nr` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `tan` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否弹窗',
  `czuid` int(11) NOT NULL DEFAULT 0 COMMENT '操作用户',
  `czip` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '操作IP',
  `sj` datetime NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC";
        $res = M()->query($sql);
        if($res === true){
            echo "create table x_msg_znx ok!".'<br>';
        }
        else{
            echo "create table x_msg_znx fail!".'<br>';
        }
    }

    /**
     * 创建mysql表
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function create_tables(){
        $this->create_x_msg_sms();
        $this->create_x_msg_wx();
        $this->create_x_msg_znx();
    }


    /**
     * 发布配置文件
     *
     * @author wumengmeng <wu_mengmeng@foxmail.com>
     */
    public function publishFiles(){
        $composer_x_file = __DIR__.'/../../x/msg.php';//composer包内配置文件
        $app_x_file = APP_X.'msg.php';//需要发布到项目内的配置文件
        $res = copy($composer_x_file,$app_x_file);
        if($res === true){
            echo "publish file msg.php ok!".'<br>';
        }
        else{
            echo "publish file msg.php fail!".'<br>';
        }
    }

}