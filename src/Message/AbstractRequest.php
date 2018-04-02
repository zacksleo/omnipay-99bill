<?php

namespace Omnipay\Bill99\Message;

use Omnipay\Bill99\Common\Signer;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $method = 'POST';
    protected $privateKey;
    protected $encryptKey;

    protected $productionEndpoint = 'https://www.99bill.com/gateway/recvMerchantInfoAction.htm';
    protected $sandBoxEndpoint = 'https://sandbox.99bill.com/gateway/recvMerchantInfoAction.htm ';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->sandBoxEndpoint : $this->productionEndpoint;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->getParameter('orderTime');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setTimestamp($value)
    {
        return $this->setParameter('orderTime', $value);
    }

    public function getData()
    {
        $this->validateParams();
        $this->setDefaults();
        $this->convertToString();
        $data = $this->parameters->all();
        $data['signMsg'] = $this->sign($data));
        return $data;
    }

    protected function setDefaults()
    {
        if (!$this->getTimestamp()) {
            $this->setTimestamp(date('YmdHis'));
        }
    }

    protected function convertToString()
    {
        foreach ($this->parameters->all() as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $this->parameters->set($key, json_encode($value));
            }
        }
    }


    protected function sign($params)
    {
        $signer = new Signer($params);
        $signer->setIgnores(['signMsg']);
        $sign = $signer->signWithRSA($this->getPrivateKey());
        return $sign;
    }

    /**
     * @return mixed
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setPrivateKey($value)
    {
        $this->privateKey = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignType()
    {
        return $this->getParameter('sign_type');
    }

    /**
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function validateParams()
    {
        $this->validate(
            'merchantAcctId',
            'pageUrl',
            'bgUrl',
            'language',
            'orderId',
            'orderAmount'
        );
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint();
        $body = http_build_query($data);

        $response = $this->httpClient->post($url)/**/
        ->setBody($body, 'application/x-www-form-urlencoded')/**/
        ->send()->getBody();
        $response = $this->decode($response);
        return $response;
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }
}
