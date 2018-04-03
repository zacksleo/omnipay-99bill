<?php

namespace Omnipay\Bill99\Message;

class PurchaseRequest extends AbstractRequest
{
    public function sendData($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }
}