<?php
/**
 * 消息配置
 */

return [

	// 微信公众号配置
	'wx_app' => [
		0 => [
			'appId' => 'wxf26abbe5c700397f',
			'appSecret' => 'b16f927913d55e90d82fe60a18e0ab7a',
		]
	],

	//站内信配置
	'znx' => [
		'lx' => ['0' => '系统消息', '1' => '业务消息', '2' => '活动消息'],
		'zt' => ['0' => '未读', '1' => '已读'],
	],

  'sms'=>[
    'url' => 'http://47.111.20.126/api/sms/send',
    'access_key'=>'',
    'qm'=>'野兔媒介',//签名
    'fsjg_sj'=>60,//发送间隔必须超过60秒
    'sjh_max'=>15,//每个手机号码每半小时内最多只能发送15次
    'uid_max'=>7,//每个帐户每半小时最多只能发7次
  ],

	//消息模板配置
	'tpl' => [
		/*'消息类型'=>[
			'znx'=>['lx'=>0,'bt'=>'标题','nr'=>'内容','tan'=>0],//lx=站内信类型,tan=是否弹窗提醒
			'sms'=>['mbid'=>'SMS_20190910165056','xz'=>0,'cs'=>'{$参数1}|{$参数2}'],//mbid=短信模板id,xz=是否限制发送频率,cs=短信内容中参数列表
			'wx'=>[
				'template_id' => '3HxwI0kgh_6fNYmTobOFKPDnG-DIX6e8vjC6aC4OOY0',//模板id
				'params' => ['yw', 'num', 'qian', 'time'],//字段列表
				'touser' => '',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的客户，您的{$yw}订单已创建成功！',
						'color' => '#173177',
					],
					'keyword1' => [ // 订单编号
						'value' => '{$num}',
						'color' => '#173177',
					],
					'keyword2' => [ // 订单金额
						'value' => '{$qian}元',
						'color' => '#173177',
					],
					'keyword3' => [ // 下单时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时支付订单！如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				]
			],*/

		'短信验证码' => ['sms' => ['mbid' => 'SMS_20191022151738', 'cs' => ['code'], 'xz' => 1]],
		'语音验证码' => ['sms' => ['mbid' => 'SMS_20191112164742', 'cs' => ['code'], 'xz' => 1]],

		'创建订单' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '{$yw}订单已创建成功',
				'nr' => '尊敬的客户，您的{$yw}订单已创建成功！<br>订单号:{$ddh}<br>订单金额:{$price}元<br>下单时间:{$time}'
			],
			'sms' => ['mbid' => 'SMS_20210516230522', 'cs' => ['yw', 'ddh', 'price'], 'xz' => 0],
			//您的${yw}订单已创建，订单号${ddh}，订单金额:${price}元，请登录平台查看。
			'wx' => [
				'params' => ['yw', 'ddh', 'qian', 'time'],
				'touser' => '',
				'template_id' => '3HxwI0kgh_6fNYmTobOFKPDnG-DIX6e8vjC6aC4OOY0',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的客户，您的{$yw}订单已创建成功！',
						'color' => '#173177',
					],
					'keyword1' => [ // 订单编号
						'value' => '{$ddh}',
						'color' => '#173177',
					],
					'keyword2' => [ // 订单金额
						'value' => '{$price}元',
						'color' => '#173177',
					],
					'keyword3' => [ // 下单时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时支付订单！如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'审核未通过' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您提交的{$yw}，未通过审核！',
				'nr' => '您提交的{$yw}，未通过审核！<br>失败原因:{$reason}<br>请及时查看处理，感谢您的配合！<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516230956', 'cs' => ['ddxx'], 'xz' => 0],
			//您的订单:${ddxx}，审核不通过，请及时处理。
			'wx' => [
				'params' => ['yw', 'reason', 'time'],
				'touser' => '',
				'template_id' => 'p_Tz0NcIBw_9YTO_YiURmNH9o8ucShj82ZncCf-AK9E',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '您提交的{$yw}，未通过审核！',
						'color' => '#173177',
					],
					'keyword1' => [ // 未通过原因
						'value' => '{$reason}',
						'color' => '#173177',
					],
					'keyword2' => [ // 审核时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时登录平台进行查阅修改后再次提交，感谢您的配合！如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'业务递交' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您的订单{$ddxx}，已递交官方！',
				'nr' => '您提交的{$ddxx}，已递交官方，预计1个工作日后可获取申请回执。<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516231246', 'cs' => ['ddxx'], 'xz' => 0],
			//您的订单:${ddxx}，已递交官方，预计1个工作日后可获取申请回执。
			'wx' => [
				'params' => ['yw', 'num', 'time'],
				'touser' => '',
				'template_id' => '2BR6BANYI9YZmDcklpKOcyG8Vmdy00iDXXfL5oahD9A',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的用户，您的{$yw}，已递交官方！',
						'color' => '#173177',
					],
					'keyword1' => [ // 服务单号
						'value' => '{$num}',
						'color' => '#173177',
					],
					'keyword2' => [ // 申请时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '预计1个工作日后可获取申请回执！请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'添加回执' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您的订单{$ddxx}，已添加回执！',
				'nr' => '您的订单{$ddxx}，已添加回执编号{$bh}，商标局形式审查预计1个月左右。请及时登录平台查阅。<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516231653', 'cs' => ['ddxx', 'bh'], 'xz' => 0],
			//您的订单:${ddxx}，已成功添加回执编号${bh}，请及时查看。
			'wx' => [
				'params' => ['yw', 'project', 'time', 'state'],
				'touser' => '',
				'template_id' => '18TgDqawUFGohznTCeVoZHYtFH96JAhBP_OpJ4pNc2o',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的用户，您的{$yw}，有新的处理进度！',
						'color' => '#173177',
					],
					'keyword1' => [ // 申请项目
						'value' => '{$project}',
						'color' => '#173177',
					],
					'keyword2' => [ // 申请时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'keyword3' => [ // 申请状态
						'value' => '{$state}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '商标局形式审查预计1个月左右！请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'流程提醒' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '流程提醒：${ywm}，有新的状态！',
				'nr' => '您的{$ywm}，{$ywxq}，有新的状态:{$zt}，请及时查看处理。'
			],
			'sms' => ['mbid' => 'SMS_20210516232054', 'cs' => ['ywm', 'zt'], 'xz' => 0],
			//您申请的业务:${ywm}，有新的状态:${zt}，请及时查看处理。
			'wx' => [
				'params' => ['ywm', 'project', 'time', 'state'],
				'touser' => '',
				'template_id' => '18TgDqawUFGohznTCeVoZHYtFH96JAhBP_OpJ4pNc2o',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的用户，您的{$ywm}，有新的处理进度！',
						'color' => '#173177',
					],
					'keyword1' => [ // 申请项目
						'value' => '{$project}',
						'color' => '#173177',
					],
					'keyword2' => [ // 申请时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'keyword3' => [ // 申请状态
						'value' => '{$state}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '预计1个工作日后可获取申请回执。请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'付款成功' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您的订单{$ddh}，支付成功！',
				'nr' => '您的订单{$ddh}，支付成功！<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516233353', 'cs' => ['ddh'], 'xz' => 0],
			//恭喜您，订单支付成功，订单号${ddh}，请关注业务后续进展。
			'wx' => [
				'params' => ['qian', 'payment', 'pay_xq', 'time'],
				'touser' => '',
				'template_id' => '5-c6siddAsJycJfCQPHFPDZJNkqaqE3fFOd8rHUMZJI',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的客户，您的订单已支付成功！',
						'color' => '#173177',
					],
					'keyword1' => [ // 支付金额
						'value' => '{$qian}元',
						'color' => '#173177',
					],
					'keyword2' => [ // 支付方式
						'value' => '{$payment}',
						'color' => '#173177',
					],
					'keyword3' => [ // 支付详情
						'value' => '{$pay_xq}',
						'color' => '#173177',
					],
					'keyword4' => [ // 支付时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'退款提醒' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您的订{$ddxx}，已退款！',
				'nr' => '您的订单{$ddxx}，已退款。<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516233849', 'cs' => ['ddxx'], 'xz' => 0],
			//您的订单${ddxx}，已退款！退款原因请登录平台查看。
			'wx' => [
				'params' => ['yw', 'qian', 'reason', 'time', 'may', 'bz'],
				'touser' => '',
				'template_id' => 'Kq98xsJbtkS05TwXt_F7OlXb4TyTS4YfXKAFQpQ7d-Q',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的用户，您的{$yw}，已退款。',
						'color' => '#173177',
					],
					'keyword1' => [ // 退款金额
						'value' => '{$qian}元',
						'color' => '#173177',
					],
					'keyword2' => [ // 退款原因
						'value' => '{$reason}',
						'color' => '#173177',
					],
					'keyword3' => [ // 退款时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'keyword4' => [ // 退款方式
						'value' => '{$may}',
						'color' => '#173177',
					],
					'keyword5' => [ // 备注
						'value' => '{$bz}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'资金变动' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '资金变动，{$lx}！',
				'nr' => '您的账户资金发生变动,{$lx}，{qian}元！请及时查看。<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516234253', 'cs' => ['lx', 'qian'], 'xz' => 0],
			//您的账户资金发生变动,${lx}，${qian}元！请及时查看。
			'wx' => [
				'params' => ['lx', 'time', 'qian'],
				'touser' => '',
				'template_id' => 'koRXuSnUYsI24BibZ8cjoKYNSAKfYAERs15yKpOcUwM',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '您的账户资金发生变动，详情如下：',
						'color' => '#173177',
					],
					'keyword1' => [ // 变动类型
						'value' => '{$lx}',
						'color' => '#173177',
					],
					'keyword2' => [ // 变动时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'keyword3' => [ // 变动金额
						'value' => '{$qian}元',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'提现提醒' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您的申请提现，{$qian}元',
				'nr' => '您申请提现的{$qian}元，已处理！请及时查看。<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516234433', 'cs' => ['qian'], 'xz' => 0],
			//您申请提现的${qian}元，已处理！请及时查看。
			'wx' => [
				'params' => ['time', 'may', 'qian', 'sxf', 'dz_qian'],
				'touser' => '',
				'template_id' => '3oo2ou3x1NP1oBdWYmdvA8uOgLrligB80fxrGEK9miY',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的用户，您申请的提现金额已处理。',
						'color' => '#173177',
					],
					'keyword1' => [ // 申请时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'keyword2' => [ // 提现方式
						'value' => '{$may}',
						'color' => '#173177',
					],
					'keyword3' => [ // 提现金额
						'value' => '{$qian}',
						'color' => '#173177',
					],
					'keyword4' => [ // 手续费用
						'value' => '{$sxf}',
						'color' => '#173177',
					],
					'keyword5' => [ // 到账金额
						'value' => '{$dz_qian}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '感谢您的使用，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],

		'到期提醒' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '您的商标:{$sbm}，已进入续展期！',
				'nr' => '您的商标:{$sbm}，已进入续展期，到期时间{$dqsj}，请及时处理。<br>如有疑问，请联系您的知产顾问。'
			],
			'sms' => ['mbid' => 'SMS_20210516234734', 'cs' => ['sbm', 'dqsj'], 'xz' => 0],
			//您的商标:${sbm}，已进入续展期，到期时间${dqsj}，请及时处理！
			'wx' => [
				'params' => ['yw', 'time', 'sm'],
				'touser' => '',
				'template_id' => '-HvkAjRVRz1NfZE6kksErgGIMPWWbAPVgN4wOHpsXOU',
				'url' => '',
				'data' => [
					'first' => [
						'value' => '尊敬的客户，{$yw}！',
						'color' => '#173177',
					],
					'keyword1' => [ // 到期时间
						'value' => '{$time}',
						'color' => '#173177',
					],
					'keyword2' => [ // 说明
						'value' => '{$sm}',
						'color' => '#173177',
					],
					'remark' => [
						'value' => '请及时登录平台查阅，如有疑问，请联系您的知产顾问。',
						'color' => '#173177',
					],
				],
			],
		],
		'需求提醒' => [
			'znx' => [
				'lx' => 1,
				'tan' => 0,
				'bt' => '有新的需求，请及时处理！',
				'nr' => '申请人：{$name}，申请时间：{$time}，申请业务：{$sm}。'
			],
			'wx' => [
				'params' => ['name', 'time', 'sm'],
				'touser' => '',
				'template_id' => 'QJWjt24bdRMm-Y6k6QBkTLNtC0BYnAsjjDwwc2LUEPo',
				'url' => '',
				'data' => [
					'first' => [
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
					'remark' => [
						'value' => '请及时登录平台查阅！',
						'color' => '#173177',
					],
				],
			],
		],
	],

];