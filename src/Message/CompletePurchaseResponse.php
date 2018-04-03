<?php

namespace Omnipay\Bill99\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        return true;
    }

    public function isPaid()
    {
        $data = $this->data;
        if ($data['paid']) {
            return true;
        } else {
            return false;
        }
    }
}
