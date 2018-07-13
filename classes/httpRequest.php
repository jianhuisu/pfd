<?php
/**
 * User: sujianhui
 * Date: 2017-8-29
 * Time: 16:24
 */
namespace classes;

class httpRequest
{
    public function requestUsePost($url,$xmlPackage,$useCert = false)
    {
        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);
        curl_setopt($con, CURLOPT_POSTFIELDS, $xmlPackage);
        curl_setopt($con, CURLOPT_POST,true);

        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);

//        curl_setopt($con,CURLOPT_SSL_VERIFYPEER,TRUE);
//        curl_setopt($con,CURLOPT_SSL_VERIFYHOST,2);//严格校验

        //设置header
        curl_setopt($con, CURLOPT_HEADER, FALSE);

        //要求结果为字符串且输出到屏幕上
        curl_setopt($con, CURLOPT_RETURNTRANSFER, TRUE);

        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($con,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($con,CURLOPT_SSLCERT, wePay::getClientCert());
            curl_setopt($con,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($con,CURLOPT_SSLKEY, wePay::getClientKey());
        }


        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_TIMEOUT,(int)100);

        $result =  curl_exec($con);
        $error = curl_error($con);
        curl_close($con);

        if(empty($error)){
            $return = $result;
        }else{
            $return = array('error'=>$error);
        }

        return $return;
    }

    public function requestUseGet($url){

        $con = curl_init((string)$url);
        curl_setopt($con, CURLOPT_HEADER, false);

        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($con, CURLOPT_TIMEOUT,(int)100);

        $result =  curl_exec($con);
        $error = curl_error($con);
        curl_close($con);

        if(empty($error)){
            $return = $result;
        }else{
            $return = array('error'=>$error);
        }

        return $return;
    }
}