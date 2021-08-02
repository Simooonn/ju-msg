<?php
/**
 * 消息配置
 */

return [
  'debug'  => 1,//是否测试 1测试 0正式，测试模式下，不真实发送短信和微信模板消息

  // 微信公众号配置
  'wx_app' => [
    'appId'     => '###请替换成自己的微信公众号appId###',//登录微信公众平台=>基本配置=>appId
    'appSecret' => '###请替换成自己的微信公众号appSecret###',//登录微信公众平台=>基本配置=>appSecret
  ],

  //站内信配置
  'znx'    => [
    'lx' => ['0' => '系统消息', '1' => '业务消息', '2' => '活动消息'],
    'zt' => ['0' => '未读', '1' => '已读'],
  ],

  'sms' => [
    'url'        => 'http://47.111.20.126/api/sms/send',//聚短信接口
    'access_key' => '###请替换成自己的聚短信access_key###',//聚短信access_key
    'qm'         => '###请替换成自己的聚短信签名###',//签名
    'fsjg_sj'    => 60,//发送间隔必须超过60秒
    'sjh_max'    => 15,//每个手机号码每半小时内最多只能发送15次
    'uid_max'    => 7,//每个帐户每半小时最多只能发7次
  ],

  //消息模板配置
  'tpl' => [
    /*'消息类型' => [
        //站内信
        'znx' => ['lx' => 0, 'bt' => '用户{$uid}', 'nr' => '用户{$uid}已登录', 'tan' => 0],//bt=标题,nr=内容,lx=站内信类型,tan=是否弹窗提醒

        //短信
        'sms' => ['mbid' => 'SMS_20190910165056', 'xz' => 0, 'params' => ['参数1', '参数2']],//mbid=短信模板id,xz=是否限制发送频率,params=短信内容中参数列表

        //微信模板消息
        'wx'  => [
          'params'      => ['name', 'time', 'sm'],
          'touser'      => '',
          'template_id' => 'QJWjt24bdRMm-Y6k6QBkTLNtC0BYnAsjjDwwc2LUEPo',
          'url'         => '',
          'data'        => [
            'first'    => [
              'value' => '有新的客户成功提交需求，请及时处理！',
              'color' => '#173177',
            ],
            'keyword1' => [ // 申请人
                            'value' => '{$name}',
                            'color' => '#173177',
            ],
            'keyword2' => [ // 申请时间
                            'value' => '{$time}',
                            'color' => '#173177',
            ],
            'keyword3' => [ // 申请业务
                            'value' => '{$sm}',
                            'color' => '#173177',
            ],
            'remark'   => [
              'value' => '请及时登录平台查阅！',
              'color' => '#173177',
            ],
          ],
        ],
    ],*/

    '登录注册' => [
      'znx' => [
        'lx'  => 0,
        'tan' => 0,
        'bt'  => '用户{$uid}',
        'nr'  => '用户{$uid}已登录',
      ],//lx=站内信类型,tan=是否弹窗提醒
      'sms' => ['mbid' => 'SMS_20191112114503', 'xz' => 0, 'params' => ['code']],
      'wx'  => [
        'params'      => ['name', 'time', 'sm'],
        'touser'      => '',
        'template_id' => 'QJWjt24bdRMm-Y6k6QBkTLNtC0BYnAsjjDwwc2LUEPo',
        'url'         => '',
        'data'        => [
          'first'    => [
            'value' => '有新的客户成功提交需求，请及时处理！',
            'color' => '#173177',
          ],
          'keyword1' => [ // 申请人
                          'value' => '{$name}',
                          'color' => '#173177',
          ],
          'keyword2' => [ // 申请时间
                          'value' => '{$time}',
                          'color' => '#173177',
          ],
          'keyword3' => [ // 申请业务
                          'value' => '{$sm}',
                          'color' => '#173177',
          ],
          'remark'   => [
            'value' => '请及时登录平台查阅！',
            'color' => '#173177',
          ],
        ],
      ],
    ],
  ],

];