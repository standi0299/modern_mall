<?php
  /* ============================================================================== */
  /* =   PAGE : 라이브버리 PAGE                                                   = */
  /* = -------------------------------------------------------------------------- = */
  /* =   Copyright (c)  2010.02   KCP Co., Ltd.   All Rights Reserved.            = */
  /* = -------------------------------------------------------------------------- = */
  /* +   이 모듈에 대한 수정을 금합니다.                                          + */
  /* ============================================================================== */

  /* ============================================================================== */
  /* +   SOAP 연동 CALSS                                                          + */
  /* ============================================================================== */

require_once (dirname(__FILE__).'/../../../../lib/nusoap/lib/nusoap.php');  
  
class   ApproveReq
{
    public  $accessCredentialType;    // AccessCredentialType
    public  $baseRequestType;         // BaseRequestType
    public  $escrow;                  // boolean
    public  $orderID;                 // string
    public  $paymentAmount;           // string
    public  $paymentMethod;           // string
    public  $productName;             // string
    public  $returnUrl;               // string
    public  $siteCode;                // string
}

class ApproveRes
{
    public  $approvalKey;             // string
    public  $baseResponseType;        // BaseResponseType
    public  $payUrl;                  // string
}

class approve
{
    public  $req;                     // ApproveReq
}

class approveResponse
{
    public  $return;                  // ApproveRes
}

class AccessCredentialType
{
    public $accessLicense;            // string
    public $signature;                // string
    public $timestamp;                // string
}

class BaseRequestType
{
    public  $detailLevel;             // string
    public  $requestApp;              // string
    public  $requestID;               // string
    public  $userAgent;               // string
    public  $version;                 // string
}

class BaseResponseType
{
    public  $detailLevel;             // string
    public  $error;                   // ErrorType
    public  $messageID;               // string
    public  $release;                 // string
    public  $requestID;               // string
    public  $responseType;            // string
    public  $timestamp;               // string
    public  $version;                 // string
    public  $warningList;             // ErrorType
}

class ErrorType
{
    public  $code;                    // string
    public  $detail;                  // string
    public  $message;                 // string
}

class PayService extends  SoapClient
{
    private   static    $classmap = array(
                                          'ApproveReq' => 'ApproveReq',
                                          'ApproveRes' => 'ApproveRes',
                                          'approve' => 'approve',
                                          'approveResponse' => 'approveResponse',
                                          'AccessCredentialType' => 'AccessCredentialType',
                                          'BaseRequestType' => 'BaseRequestType',
                                          'BaseResponseType' => 'BaseResponseType',
                                          'ErrorType' => 'ErrorType',
                                         );

    var   $chatsetType;
    var   $accessCredentialType;
    var   $baseRequestType;
    var   $approveReq;
    var   $approveResponse;
    var   $resCD;
    var   $resMsg;


    public  function  PayService( $wsdl = "", $options = array() )
    {
        foreach( self::$classmap as $key => $value )
        {
            if ( !isset( $options[ 'classmap' ][ $key ] ) )
            {
                $options[ 'classmap' ][ $key ] = $value;
            }
        }
    
        parent::__construct( $wsdl, $options );
        
        $accessCredentialType = null;
        $baseRequestType      = null;
        $approveReq           = null;
        $resCD                = "95XX";
        $resMsg               = "연동 오류";
    }

    public  function  setCharSet( $charsetType )
    {
        $this->chatsetType = $charsetType;
    }

    public  function  setAccessCredentialType( $accessLicense,
                                               $signature,
                                               $timestamp )
    {
        $this->accessCredentialType = new AccessCredentialType();

        $this->accessCredentialType->accessLicense  = $accessLicense;
        $this->accessCredentialType->signature      = $signature;
        $this->accessCredentialType->timestamp      = $timestamp;
    }

    public  function  setBaseRequestType( $detailLevel,
                                          $requestApp,
                                          $requestID,
                                          $userAgent,
                                          $version   )
    {
        $this->baseRequestType = new BaseRequestType();

        $this->baseRequestType->detailLevel      = $detailLevel;
        $this->baseRequestType->requestApp       = $requestApp;
        $this->baseRequestType->requestID        = $requestID;
        $this->baseRequestType->userAgent        = $userAgent;
        $this->baseRequestType->version          = $version;
    }

    public  function  setApproveReq( $escrow,
                                     $orderID,
                                     $paymentAmount,
                                     $paymentMethod,
                                     $productName,
                                     $returnUrl,
                                     $siteCode )
    {
        $this->approveReq = new ApproveReq();

        $productName_utf8 = ( $this->chatsetType == "euc-kr" ) ? iconv( "EUC-KR", "UTF-8", $productName ) : $productName;
        
        $this->approveReq->accessCredentialType = $this->accessCredentialType;
        $this->approveReq->baseRequestType      = $this->baseRequestType;
        $this->approveReq->escrow               = $escrow;
        $this->approveReq->orderID              = $orderID;
        $this->approveReq->paymentAmount        = $paymentAmount;
        $this->approveReq->paymentMethod        = $paymentMethod;
        $this->approveReq->productName          = $productName_utf8;
        $this->approveReq->returnUrl            = $returnUrl;
        $this->approveReq->siteCode             = $siteCode;
    }

    public  function  approve()
    {
      global $g_wsdl;
      try{
        $approve = new approve();
        
        $approve->req = $this->approveReq;
        
        //$clientApproveResponse = $this->__soapCall( "approve", array( $approve ));
        //var_dump($clientApproveResponse);
        
        $client = new nusoap_client($g_wsdl,true);
        $client->xml_encoding = "UTF-8";
        $client->soap_defencoding = "UTF-8";
        $client->decode_utf8 = false;
                
        $clientApproveResponse = $client->call("approve", array($approve));
        
        //array to approveResponse object convert     //20150107    chunter
        $this->approveResponse->return->approvalKey = $clientApproveResponse['return']['approvalKey'];
        $this->approveResponse->return->payUrl = $clientApproveResponse['return']['payUrl'];
        
        $this->approveResponse->return->baseResponseType->detailLevel = $clientApproveResponse['return']['baseResponseType']['detailLevel'];
        $this->approveResponse->return->baseResponseType->messageID = $clientApproveResponse['return']['baseResponseType']['messageID'];
        $this->approveResponse->return->baseResponseType->release = $clientApproveResponse['return']['baseResponseType']['release'];
        $this->approveResponse->return->baseResponseType->requestID = $clientApproveResponse['return']['baseResponseType']['requestID'];
        $this->approveResponse->return->baseResponseType->responseType = $clientApproveResponse['return']['baseResponseType']['responseType'];
        $this->approveResponse->return->baseResponseType->timestamp = $clientApproveResponse['return']['baseResponseType']['timestamp'];
        $this->approveResponse->return->baseResponseType->version = $clientApproveResponse['return']['baseResponseType']['version'];
        $this->approveResponse->return->baseResponseType->warningList = $clientApproveResponse['return']['baseResponseType']['warningList'];
                
        $this->approveResponse->return->baseResponseType->error->code = $clientApproveResponse['return']['baseResponseType']['error']['code'];
        $this->approveResponse->return->baseResponseType->error->detail = $clientApproveResponse['return']['baseResponseType']['error']['detail'];
        $this->approveResponse->return->baseResponseType->error->message = $clientApproveResponse['return']['baseResponseType']['error']['message'];
        
        
        /*
        array(1) {
         ["return"]=> array(3) 
         {
           ["approvalKey"]=> string(0) "" 
           ["baseResponseType"]=> array(9) 
            {
              ["detailLevel"]=> string(1) "0" 
              ["error"]=> array(3) 
                {
                  ["code"]=> string(4) "1044" 
                  ["detail"]=> string(0) "" 
                  ["message"]=> string(57) "해당 단말기는 서비스 불가 단말기 입니다." 
                } 
              ["messageID"]=> string(16) "T000017F198InO76" 
              ["release"]=> string(3) "0.1" 
              ["requestID"]=> string(25) "TEST201501071420598849525" 
              ["responseType"]=> string(5) "ERROR" 
              ["timestamp"]=> string(28) "2015-01-07T15:01:09.985Z6091" 
              ["version"]=> string(3) "0.1" 
              ["warningList"]=> NULL 
            } 
           ["payUrl"]=> string(0) "" 
         } 
        } NULL 
        */
        
        
        //$this->approveResponse = $client->call("approve", array($approve));
        //$this->approveResponse = (object)$clientApproveResponse;
        //$this->approveResponse = (array)$this->approveResponse;        
        //echo "<BR>";
        //var_dump($clientApproveResponse);        
        //exit;
        
        $this->resCD  = $this->approveResponse->return->baseResponseType->error->code;
        $this->resMsg = $this->approveResponse->return->baseResponseType->error->message;

        return  $this->approveResponse->return;
      } catch(exception $ex)
      {
        print_r($ex);
      }
    }        
}