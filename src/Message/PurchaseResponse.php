<?php


namespace Omnipay\Bill99;


use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    public function isSuccessful()
    {
        return false;
    }

    public function getRedirectUrl()
    {
        // TODO: Implement getRedirectUrl() method.
    }

    public function getRedirectData()
    {
        // TODO: Implement getRedirectData() method.
    }

    public function getRedirectMethod()
    {
        // TODO: Implement getRedirectMethod() method.
    }
}
