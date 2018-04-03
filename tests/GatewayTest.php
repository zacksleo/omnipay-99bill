<?php

namespace tests;

use Omnipay\Bill99\Gateway;
use Omnipay\Tests\GatewayTestCase;

/**
 * Class GatewayTest
 * @package tests
 * @author zacksleo <zacksleo@gmail.com>
 * @property Gateway $gateway
 */
class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testPurchase()
    {
        $request = $this->gateway->purchase(array('amount' => '10.00'));
        $this->assertInstanceOf('Omnipay\Bill99\Message\PurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }

    public function testCompletePurchase()
    {
        $request = $this->gateway->completePurchase(array('amount' => '10.00'));
        $this->assertInstanceOf('Omnipay\Bill99\Message\CompletePurchaseRequest', $request);
        $this->assertSame('10.00', $request->getAmount());
    }
}
