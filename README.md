##### 1.composer引入文件 
进入项目目录下执行命令
>composer require ju/msg

##### 2.初始化数据和配置
> 执行代码 (new \JuMsg\Msg())->init();

【注】如果之前项目没使用过composer，
在ju.php文件

```
$router=new router();//加载路由
```
这段代码上方添加一行代码，引入composer管理（放在这里是为了不让composer里使用的函数和框架里的函数重复定义引起冲突）

```
//引入composer管理
require ROOT.'vendor/autoload.php';
```


##### 3.配置信息
执行步骤2后，app/x目录下会生成msg.php文件，进入文件里配置相关信息

微信消息
> 登录微信公众平台=>基本配置=>IP白名单 配置，配置好ip才能正常发送模板消息

> 单公众号：需要在wx_app下配置appId和appSecret，配置后默认给这个公众号下对应的用户发送模板消息

> 多公众号：在发送消息时，数据里传入appId和appSecret，从而实现多公众号发送模板消息

短信
> 开通聚短信功能，需要在sms下配置url和access_key

> 单签名：需要在sms下配置qm

> 多签名：在发送消息时，数据里传入qm，从而实现多签名

模板设置
> tpl下有参考案例，按需配置，比如某个消息类型只发送短信，只要配置sms即可

```
'消息类型' => [
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
    ],
```

##### 4.发送消息
4.1.直接发送

> 直接发送 

```
//短信
$data = [
  'sjhm'=>'17721086033',
  'code'=>888888,
  //'qm'=>'其他签名',//可传可不传，传入后，可支持多签名
];
(new \JuMsg\Msg())->send('登录注册',0,['sms'],$data);

//站内信
$data = [
  'uid'=>'17721086033',
];
(new \JuMsg\Msg())->send('登录注册',10001,['znx'],$data);


//微信消息模板
$data = [
  'uid'=>'17721086033',
  'openid'=>'o95aL6cCD52j9CYOTdiLCAZFpO0k',
  'order_sn'=>'NO9527',
  'name'=>'纪苏',
  'sjhm'=>'17721086032',
  'ddjg'=>998,
  'zfzt'=>'支付成功',
  
/*  'appId'     => '###请替换成自己的微信公众号appId###',//登录微信公众平台=>基本配置=>appId
  'appSecret' => '###请替换成自己的微信公众号appSecret###',//登录微信公众平台=>基本配置=>appSecret
  */
  //appId和appSecret可传可不传，不传使用msg.php里的默认配置，传入后优先使用传入的数据，用以支持多公众号
];
(new \JuMsg\Msg())->send('登录注册',10001,['wx'],$data);
```

4.2.异步发送（先异步发送记录数据，再去队列消费数据真实发送）

> 异步发送 （记录数据，等待后台队列消费）

```
$data = [
  'sjhm'=>'17721086033',
  'code'=>888888,
  'uid'=>'17721086033',
  'openid'=>'o95aL6cCD52j9CYOTdiLCAZFpO0k',
  'order_sn'=>'NO9527',
  'name'=>'纪苏',
  'ddjg'=>998,
  'zfzt'=>'支付成功',
];
(new \JuMsg\Msg())->async_send('登录注册',10018,['sms','znx','wx'],$data);
```

> 后台消费（消费队列真实发送） 
```
(new \JuMsg\Msg())->customer_queue();
```