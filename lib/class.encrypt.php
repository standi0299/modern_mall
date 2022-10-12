<?
  //데이타 암호화 및 복호화 Oasis 암복호화 키를 맞쳤다.
  // 키 변경시 오아시스 데이타 암복호화 못함.
  
  
  //사용법
  //암호화
  //$sensitiveData = "123|3434|4545"
  //$objEncManager = new DataEncryptor();
  //$objEncManager->setBlockSize(32);     //암호화 최대길이
  //$encryptedData = $objEncManager->mcryptEncryptString( $sensitiveData );
  //echo "Enc Data: " . $encryptedData . "_<br><br>";
    
  //복호화
  //$objEncManager = new DataEncryptor();
  //$decryptedData = $objEncManager->mcryptDecryptString( $encryptedData, $objEncManager->MCRYPT_IV );    
  //echo "Decrypt Data: " . $decryptedData . "<br><br>";
   
  class DataEncryptor
  {
    const MY_MCRYPT_CIPHER        = MCRYPT_RIJNDAEL_128;
    const MY_MCRYPT_MODE          = MCRYPT_MODE_CBC;
    public $MCRYPT_KEY  = "AAICAQAIAwUABggCBQcEBAIGBAMDBAcBAAUEBwIDBAU=";
    public $MCRYPT_IV   = 'BQQDAgcEBQABBwQDAwQGAw=='; 
    public $block_size = 32;
    
    public function __construct()
    {        
      $this->MCRYPT_KEY = base64_decode($this->MCRYPT_KEY);
    }
    
    
    function setBlockSize($size)
    {
      if ($size > 0)
        $this->block_size = $size;
    }
    
    function _pad($text)
    {
      $length = strlen($text);
      
      if ($length % $this->block_size == 0) {
        return $text;
      } else {                
        $pad = $this->block_size - ($length % $this->block_size);
        return str_pad($text, $length + $pad, chr($pad));
      }
    }


    public function mcryptEncryptString($stringToEncrypt, $iv, $base64encoded = true )
    {
        // Set the initialization vector
        $iv_size      = mcrypt_get_iv_size( self::MY_MCRYPT_CIPHER, self::MY_MCRYPT_MODE );
        //$iv           = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
		
		    if ( $base64encoded ) {          
          $iv              = base64_decode( $iv );
        }
        $stringToEncrypt = $this->_pad($stringToEncrypt);

        $encryptedData = mcrypt_encrypt( self::MY_MCRYPT_CIPHER, $this->MCRYPT_KEY, $stringToEncrypt , self::MY_MCRYPT_MODE , $iv );

        // Data may need to be passed through a non-binary safe medium so base64_encode it if necessary. (makes data about 33% larger)
        if ( $base64encoded ) {
            $encryptedData = base64_encode( $encryptedData );
            //$this->MCRYPT_IV  = base64_encode( $iv );
        } else {
            //$this->MCRYPT_IV = $iv;
        }

        // Return the encrypted data
        return rtrim($encryptedData);
    }


    /**
     * Accepts a plaintext string and returns the encrypted version
     */
    public function mcryptDecryptString( $stringToDecrypt, $iv, $base64encoded = true )
    {
        // The data may have been base64_encoded so decode it if necessary (must come before the decrypt)
        if ( $base64encoded ) {
          $stringToDecrypt = base64_decode( $stringToDecrypt );
          $iv              = base64_decode( $iv );
        }

        $decryptedData = mcrypt_decrypt( self::MY_MCRYPT_CIPHER, $this->MCRYPT_KEY, $stringToDecrypt, self::MY_MCRYPT_MODE, $iv );
        return rtrim( $decryptedData ); // the rtrim is needed to remove padding added during encryption
    }
  }


?>