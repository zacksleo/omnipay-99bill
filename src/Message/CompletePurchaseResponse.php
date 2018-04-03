<?php

namespace Omnipay\Bill99\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class CompletePurchaseResponse
 * @package Omnipay\Bill99\Message
 * @author zacksleo <zacksleo@gmail.com>
 */
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
