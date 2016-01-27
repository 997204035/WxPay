<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			{{$jsApiParameters}},
			function(res){
				WeixinJSBridge.log(res.err_msg);
				if(res.err_msg == 'get_brand_wcpay_request:ok'){
                                //redirect to xx page
                }else if(res.err_msg == 'get_brand_wcpay_request:cancel'){
                    //redirect to xx page
                }else if(res.err_msg == 'get_brand_wcpay_request:fail'){
                    //redirect to xx page
                }else{
                    alert(res.err_code+res.err_desc+res.err_msg);
                }

                window.location.href = "{{ $callBackUrl }}";
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}

	callpay();

	</script>

</head>
<body>
</body>
</html>