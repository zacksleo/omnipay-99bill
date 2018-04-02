<?php

namespace Omnipay\Bill99\Message;

use Omnipay\Bill99\PurchaseResponse;

class PurchaseRequest extends AbstractRequest
{
    public function initialize(array $parameters = array())
    {
        foreach ($parameters as $key => $value) {
            if (in_array($key, [
                'payerName',
                'payerContactType',
                'payerContact',
                'orderId',
                'orderAmount',
                'productName',
                'productNum',
                'productId',
                'productDesc',
                'ext1',
                'ext2'
            ])) {
                $this->setParameter($key, $value);
            }
        }
        return parent::initialize($parameters);
    }

    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}