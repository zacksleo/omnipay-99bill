<?php

namespace Omnipay\Bill99\Message;

use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        return true;
    }
}
