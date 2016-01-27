<?php

namespace Jiangxianli\Wxpay\Pay;
use Jiangxianli\Wxpay\PayApi;
use Jiangxianli\Wxpay\Data\WxPayUnifiedOrder;
use Jiangxianli\Wxpay\Exception\WxPayException;
use Jiangxianli\Wxpay\Data\WxPayJsApiPay;
use Illuminate\View\View;

class JsApiPay {

    use PayApi;

    /**
     * 支付请求并返回支付页面
     * @return mixed
     * @throws WxPayException
     */
    public function pay($configs = []){

        $this->setConfig($configs);

        //①、获取用户openid
        $openId = $this->GetOpenid();

        //②、统一下单
        $input  = new WxPayUnifiedOrder();
        $input->SetBody($this->configs['body']);
        $input->SetAttach($this->setDefaultValue('attach',''));
        $input->SetOut_trade_no($this->configs['out_trade_no'] );
        $input->SetTotal_fee($this->configs['total_fee']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($this->setDefaultValue('goods_tag',''));
        $input->SetNotify_url($this->configs['notify_url']);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = $this->unifiedOrder($input);
//        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//        printf_info($order);
        $jsApiParameters = $this->GetJsApiParameters($order);

//        //获取共享收货地址js函数参数
//        $editAddress = $this->GetEditAddressParameters();

        //③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
        /**
         * 注意：
         * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
         * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
         * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
         */

        $callBackUrl = $this->configs['call_back_url'];

        return \View::make('JWxPay::jsApiPay',compact('jsApiParameters','callBackUrl'))->render();

    }


    /**
     *
     * 获取jsapi支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetJsApiParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }
        $jsapi = new WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr($this->getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign($this->configs['key']));
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }

} 