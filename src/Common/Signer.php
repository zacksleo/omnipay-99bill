<?php

namespace Omnipay\Bill99\Common;

use Exception;

/**
 * Sign Tool for Alipay
 * Class Signer
 * @package Omnipay\Bill99\Common
 * @author lokielse<lokielse@gmail.com>
 */
class Signer
{
    const ENCODE_POLICY_QUERY = 'QUERY';
    const ENCODE_POLICY_JSON = 'JSON';

    const KEY_TYPE_PUBLIC = 1;
    const KEY_TYPE_PRIVATE = 2;

    protected $ignores = ['signMsg'];

    protected $sort = true;

    protected $encodePolicy = self::ENCODE_POLICY_QUERY;

    /**
     * @var array
     */
    private $params;


    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    public function getContentToSign()
    {
        $params = $this->getParamsToSign();

        if ($this->encodePolicy == self::ENCODE_POLICY_QUERY) {
            return urldecode(http_build_query($params));
        } elseif ($this->encodePolicy == self::ENCODE_POLICY_JSON) {
            return json_encode($params);
        } else {
            return null;
        }
    }


    /**
     * @return mixed
     */
    public function getParamsToSign()
    {
        $params = $this->params;
        $this->unsetKeys($params);
        if ($this->sort) {
            $params = $this->sort($params);
        }
        $params = $this->filter($params);
        return $params;
    }

    /**
     * @param $params
     */
    protected function unsetKeys(&$params)
    {
        foreach ($this->getIgnores() as $key) {
            unset($params[$key]);
        }
    }

    /**
     * @return array
     */
    public function getIgnores()
    {
        return $this->ignores;
    }

    /**
     * @param array $ignores
     *
     * @return $this
     */
    public function setIgnores($ignores)
    {
        $this->ignores = $ignores;

        return $this;
    }


    private function filter($params)
    {
        return array_filter($params, 'strlen');
    }


    protected function sort($params)
    {
        if (isset($params['payAmount'])) {
            return $this->sortReturn();
        }
        return $this->sortRequest($params);
    }

    /**
     * 对发送到快钱的参数排序
     * @param $params
     * @return array
     */
    protected function sortRequest($params)
    {
        return [
            'inputCharset' => $params['inputCharset'],
            'pageUrl' => $params['pageUrl'],
            'bgUrl' => $params['bgUrl'],
            'version' => $params['version'],
            'language' => $params['language'],
            'signType' => $params['signType'],
            'merchantAcctId' => $params['merchantAcctId'],
            'payerName' => $params['payerName'],
            'payerContactType' => $params['payerContactType'],
            'payerContact' => $params['payerContact'],
            'payerIdType' => $params['payerIdType'],
            'payerId' => $params['payerId'],
            'payerIP' => $params['payerIP'],
            'orderId' => $params['orderId'],
            'orderAmount' => $params['orderAmount'],
            'orderTime' => $params['orderTime'],
            'orderTimestamp' => $params['orderTimestamp'],
            'productName' => $params['productName'],
            'productNum' => $params['productNum'],
            'productId' => $params['productId'],
            'productDesc' => $params['productDesc'],
            'ext1' => $params['ext1'],
            'ext2' => $params['ext2'],
            'payType' => $params['payType'],
            'bankId' => $params['bankId'],
            'period' => $params['period'],
            'cardIssuer' => $params['cardIssuer'],
            'cardNum' => $params['cardNum'],
            'remitType' => $params['remitType'],
            'remitCode' => $params['remitCode'],
            'redoFlag' => $params['redoFlag'],
            'pid' => $params['pid'],
            'submitType' => $params['submitType'],
            'orderTimeOut' => $params['orderTimeOut'],
            'extDataType' => $params['extDataType'],
            'extDataContent' => $params['extDataContent'],
        ];
    }

    /**
     * 对快钱返回到商户参数进行排序
     * @param $params
     * @return array
     */
    protected function sortReturn($params)
    {
        return [
            'merchantAcctId' => $params['merchantAcctId'],
            'version' => $params['version'],
            'language' => $params['language'],
            'signType' => $params['signType'],
            'payType' => $params['payType'],
            'period' => $params['period'],
            'period' => $params['period'],
            'bankId' => $params['bankId'],
            'bankId' => $params['bankId'],
            'orderId' => $params['orderId'],
            'orderTime' => $params['orderTime'],
            'orderTime' => $params['orderTime'],
            'orderAmount' => $params['orderAmount'],
            'bindCard' => $params['bindCard'],
            'bindMobile' => $params['bindMobile'],
            'bindMobile' => $params['bindMobile'],
            'dealId' => $params['dealId'],
            'bankDealId' => $params['bankDealId'],
            'dealTime' => $params['dealTime'],
            'payAmount' => $params['payAmount'],
            'fee' => $params['fee'],
            'ext1' => $params['ext1'],
            'ext2' => $params['ext2'],
            'payResult' => $params['payResult'],
            'errCode' => $params['errCode'],
        ];
    }


    public function signWithRSA($privateKey, $alg = OPENSSL_ALGO_SHA1)
    {
        $content = $this->getContentToSign();

        $sign = $this->signContentWithRSA($content, $privateKey, $alg);

        return $sign;
    }


    public function signContentWithRSA($content, $privateKey, $alg = OPENSSL_ALGO_SHA1)
    {
        $privateKey = $this->prefix($privateKey);
        $privateKey = $this->format($privateKey, self::KEY_TYPE_PRIVATE);
        $res = openssl_pkey_get_private($privateKey);

        $sign = null;

        try {
            openssl_sign($content, $sign, $res, $alg);
        } catch (Exception $e) {
            if ($e->getCode() == 2) {
                $message = $e->getMessage();
                $message .= "\n应用私钥格式有误，见 https://github.com/lokielse/omnipay-alipay/wiki/FAQs";
                throw new Exception($message, $e->getCode(), $e);
            }
        }

        openssl_free_key($res);
        $sign = base64_encode($sign);

        return $sign;
    }


    /**
     * Prefix the key path with 'file://'
     *
     * @param $key
     *
     * @return string
     */
    private function prefix($key)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN' && is_file($key) && substr($key, 0, 7) != 'file://') {
            $key = 'file://' . $key;
        }

        return $key;
    }


    /**
     * Convert key to standard format
     *
     * @param $key
     * @param $type
     *
     * @return string
     */
    public function format($key, $type)
    {
        if (is_file($key)) {
            $key = file_get_contents($key);
        }

        if (is_string($key) && strpos($key, '-----') === false) {
            $key = $this->convertKey($key, $type);
        }

        return $key;
    }


    /**
     * Convert one line key to standard format
     *
     * @param $key
     * @param $type
     *
     * @return string
     */
    public function convertKey($key, $type)
    {
        $lines = [];

        if ($type == self::KEY_TYPE_PUBLIC) {
            $lines[] = '-----BEGIN PUBLIC KEY-----';
        } else {
            $lines[] = '-----BEGIN RSA PRIVATE KEY-----';
        }

        for ($i = 0; $i < strlen($key); $i += 64) {
            $lines[] = trim(substr($key, $i, 64));
        }

        if ($type == self::KEY_TYPE_PUBLIC) {
            $lines[] = '-----END PUBLIC KEY-----';
        } else {
            $lines[] = '-----END RSA PRIVATE KEY-----';
        }

        return implode("\n", $lines);
    }


    public function verifyWithMD5($content, $sign, $key)
    {
        return md5($content . $key) == $sign;
    }


    public function verifyWithRSA($content, $sign, $publicKey, $alg = OPENSSL_ALGO_SHA1)
    {
        $publicKey = $this->prefix($publicKey);
        $publicKey = $this->format($publicKey, self::KEY_TYPE_PUBLIC);
        $res = openssl_pkey_get_public($publicKey);
        if (!$res) {
            $message = "The public key is invalid";
            $message .= "\n公钥格式有误，见 https://github.com/lokielse/omnipay-alipay/wiki/FAQs";
            throw new Exception($message);
        }
        $result = (bool)openssl_verify($content, base64_decode($sign), $res, $alg);
        openssl_free_key($res);
        return $result;
    }


    /**
     * @param boolean $sort
     *
     * @return Signer
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }
}
