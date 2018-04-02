<?php

namespace Omnipay\Bill99;

use Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway
{
    public function getName()
    {
        return "99Bill Gateway";
    }

    public function getDefaultParameters()
    {
        return [
            //编码方式，1代表 UTF-8; 2 代表 GBK; 3代表 GB2312 默认为1,该参数必填。
            'inputCharset' => '1',
            //网关版本，固定值：v2.0,该参数必填。
            'version' => 'v2.0',
            //签名类型,该值为4，代表PKI加密方式,该参数必填。
            'signType' => '4',
            //支付方式，一般为00，代表所有的支付方式。如果是银行直连商户，该值为10，必填。
            'payType' => '00',
            //银行代码，如果payType为00，该值可以为空；如果payType为10，该值必须填写，具体请参考银行列表。
            'bankId' => "",
            //同一订单禁止重复提交标志，实物购物车填1，虚拟产品用0。1代表只能提交一次，0代表在支付不成功情况下可以再提交。可为空。
            'redoFlag' => '0',
            //快钱合作伙伴的帐户号，即商户编号，可为空。
            'pid' => '',
        ];
    }

    public function setMchId($mchId)
    {
        $this->setParameter('merchantAcctId', $mchId);
    }


    public function getMchId()
    {
        return $this->getParameter('merchantAcctId');
    }

    /**
     * @return mixed
     */
    public function getReturnUrl()
    {
        return $this->getParameter('pageUrl');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('pageUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return $this->getParameter('bgUrl');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setNotifyUrl($value)
    {
        return $this->setParameter('bgUrl', $value);
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->getParameter('language');
    }

    /**
     * @param $language
     * @return $this
     */
    public function setLanguage($language)
    {
        return $this->setParameter('language', $language);
    }

    /**
     * @param  array $parameters
     * @return \Omnipay\Bill99\Message\PurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Bill99\Message\PurchaseRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Bill99\Message\CompletePurchaseRequest', parameters);
    }
}
