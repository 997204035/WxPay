<?php


namespace Jiangxianli\Wxpay\Notify;


class JsApiNotify {

    use WxPayNotify;

    public function notify($configs=[],$callback,$needSign=false){

        $this->setConfig($configs);

        $this->Handle($needSign,function($data) use ($callback){

                $msg = "OK";

                $result = $this->NotifyProcess($data, $msg);

                call_user_func($callback, $result);

                return $result;
        });

    }
} 