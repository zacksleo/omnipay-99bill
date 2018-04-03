# omnipay-99bill
99bill driver for the Omnipay PHP payment processing library:  快钱支付SDK

## Quick Start

### 发起支付 (Purchase)

```php
/* @var \Omnipay\Bill99\Gateway $gateway */
$gateway = \Omnipay\Omnipay::create('Bill99');
$gateway->setTestMode(false); // 正式环境
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
$request->setParams(array_merge($_POST, $_GET));
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
        die('success'); //The response should be 'success' only
    } else {
        if (Yii::$app->request->isGet) {
            return $this->redirect("/order/paid");
        }
        die('fail');
    }
} catch (Exception $e) {
    // @todo 这里为支付失败业务逻辑
    die('fail');
}
```