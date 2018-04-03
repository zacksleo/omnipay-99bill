<?php

namespace Omnipay\Bill99\Message;

/**
 * Class PurchaseRequest
 * @package Omnipay\Bill99\Message
 * @author zacksleo <zacksleo@gmail.com>
 */
class PurchaseRequest extends AbstractRequest
{
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}
