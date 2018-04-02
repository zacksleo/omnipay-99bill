<?php


namespace Omnipay\Bill99\Message;


class CompletePurchaseRequest extends PurchaseRequest
{
    public function getData()
    {

    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
