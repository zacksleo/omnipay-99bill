# omnipay-99bill

99bill driver for the Omnipay PHP payment processing library:  快钱支付SDK

## Quick Start  快速开始

[Omnipay](https://github.com/omnipay/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements WechatPay support for Omnipay.

### Install 安装

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```shell

$ composer require zacksleo/omnipay-99bill -vvv

```

### Purchase 发起支付

```php
/* @var \Omnipay\Bill99\Gateway $gateway */
$gateway = \Omnipay\Omnipay::create('Bill99');
$gateway->setPrivateKey('the_app_private_key'); //私钥内容
$gateway->setReturnUrl('https://www.example.com/return');
$gateway->setNotifyUrl('https://www.example.com/return');
$gateway->setMchId('merchatn-id'); //商户号
$request = $gateway->purchase([
    //支付人姓名,可以为空。
    'payerName' => '',
    //支付人联系类型，1 代表电子邮件方式；2 代表手机联系方式。可以为空。
    'payerContactType' => 1,
    //支付人联系方式，与payerContactType设置对应，payerContactType为1，则填写邮箱地址；payerContactType为2，则填写手机号码。可以为空。
    'payerContact' => '2532987@qq.com',
    //商户订单号，以下采用时间来定义订单号，商户可以根据自己订单号的定义规则来定义该值，不能为空。
    'orderId' => date('YmdHis') . mt_rand(1000, 9999),
    //订单金额，金额以“分”为单位，商户测试以1分测试即可，切勿以大金额测试。该参数必填。    
    'orderAmount' => 1,
    //商品名称，可以为空。
    'productName' => 'product-name',
    //商品数量，可以为空。
    'productNum' => '',
    //商品代码，可以为空。
    'productId' => '55558888',
    //商品描述，可以为空。
    'productDesc' => '',
    //扩展字段1，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
    'ext1' => '',
    //扩展自段2，商户可以传递自己需要的参数，支付完快钱会原值返回，可以为空。
    'ext2' => '',
]);
/* @var \Omnipay\Bill99\Message\PurchaseResponse $response */
$response = $request->send();
$response->redirect();
exit;
```

### 处理支付回调

```php
/* @var \Omnipay\Bill99\Gateway $gateway */
$gateway = Omnipay::create('Bill99');
$gateway->setPrivateKey('the_app_private_key'); //私钥内容
$gateway->setReturnUrl('https://www.example.com/return');
$gateway->setNotifyUrl('https://www.example.com/return');
/**
 * 先从快钱下载好.cer文件(假设为certificate.cer)，然后使用以下命令生成pem文件，里面即为public key(certificate_publickey.pem)
 * openssl x509 -inform PEM -in certificate.cer -pubkey -noout > certificate_publickey.pem
 * @see https://stackoverflow.com/questions/28060159/how-to-extract-the-rsa-public-key-from-a-cer-and-store-it-in-a-pem-using-opens 
 */
$gateway->setBill99PublicKey('99bill_publickey'); //从快钱下载并提取出的public key
/* @var $request \Omnipay\Bill99\Message\CompletePurchaseRequest */
$request = $gateway->completePurchase();
$request->setParams(array_merge($_GET));
try {
    /* @var $response \Omnipay\Bill99\Message\CompletePurchaseResponse */
    $response = $request->send();
    if ($response->isPaid()) {
        $data = $response->getData();        
        /**
         * var_dump($data);
         * @todo 这里为支付成功的业务逻辑
         * $data['orderId']  订单号
         * 订单金额 $data['orderAmount']/100,
         * 快钱交易号 $data['dealId'],         
        */
        /**
         * 这里需要注意，如果同步回调和同步回调（两者均为GET）在同一处处理，
         * 需要通过一定方式区分是异步还是同步，两者返回信息不同，如用户登录状态($_SESSION[uid])
         */
        //异步回调，该返回值为快钱必需
        die("<result>1</result><redirecturl>$url</redirecturl>");
        //同步回调
        // redirect跳转页面...
    } else {
       // @todo 支付失败的业务逻辑
        die("<result>0</result><redirecturl>$url</redirecturl>");
    }
} catch (Exception $e) {
    // @todo 这里为支付异常业务逻辑
    die("<result>0</result><redirecturl>$url</redirecturl>");
}
```

## Advance Config  其他配置

### 配置测试环境

```php
$gateway->setTestMode(true); // 测试环境

```
