<?php

namespace Omnipay\Bill99\Message;

use Omnipay\Bill99\Common\Signer;

/**
 * Class CompletePurchaseRequest
 * @package Omnipay\Bill99\Message
 * @author zacksleo <zacksleo@gmail.com>
 */
class CompletePurchaseRequest extends PurchaseRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validateParams();

        return $this->getParams();
    }


    public function validateParams()
    {
        $this->validate('params');
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->getParameter('params');
    }

    public function sendData($data)
    {
        $signer = new Signer($data);
        $signer->setSort(true);
        $content = $signer->getContentToSign();
        $match = (new Signer())->verifyWithRSA($content, $sign, $this->getBill99PublicKey());
        if ($match && isset($data['payResult']) && $data['payResult'] == '10') {
            $data['paid'] = true;
        } else {
            $data['paid'] = false;
        }
        return $this->response = new CompletePurchaseResponse($this, $responseData);
    }
}
