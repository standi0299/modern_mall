<?php
        /*--------------------------------------------------
          Socket ��� ��ҿ�û sample page
          author:
          updated date: 2013.06.13
        --------------------------------------------------*/
        /*
         * ������ ���� ȯ�� ���� ����
         */
        include './branch_config.php';

        /*
         * ��ȣȭ/��ȣȭ ���� ���� include
         */
        include './cipher_receive.php';

        /**********************************************************************************************************************
        *
        * ���� ���� �κ�
        *
        ************************************************************************************************************************/

        //��û ���� ����
        $cancel_str = create_cancel_msg($_POST['card_num'],$_POST['ack_num']);

        //��ȣȭ
        $encrypted_msg = encrypt_tworld($cancel_str,$config['sklte_key']);

        //���� ��û
        $receive_data = send_cancel_action($config,$encrypted_msg);

        //��ȣȭ
        $decrypted_msg = decrypt_tworld($receive_data,$config['sklte_key']);

        //���� �м�
        $reponse_str = analysis_response_data($decrypted_msg);

        //��� ���
        foreach($reponse_str as $rval){
            echo $rval['title'].":".$rval['value']."<br>";
        }



        /**********************************************************************************************************************/

        /*
         *
         *  ���� ��� ��û �Լ�
         *
         *
         */
        function send_cancel_action($config,$encrypted_msg){

            /*--------------------------------------------------

              Socket Client PHP Page

              html���� �Է� ���� ���� �ڵ������� ���� �̿��Ͽ�
              ������ ������ ��û�ϴ� PHP ������ �̴�.

            --------------------------------------------------*/

            /*-------------------------------------------------
              �޽��� ����
              - html ���� �Է� ���� ���� ������ ���� �������� ����  Data�� �����Ѵ�.
              - html �Է°�

                ... �߷� ...

            -----------------------------------------------------*/
            $cancelCode = ''; //��� ��û ��� �ڵ�
            /*---------------------------------------------------------------------
                // ���� ��û ���� ����
            ---------------------------------------------------------------------*/

            $price = "000000001000";
            $message = "0420";                      // �޼��� Ÿ��
            $trans_date = date("YmdHis");           // ���������Ͻ� : YYYYMMDDHHMMSS
            $card_num = '';
            $userid = '';
            //... �߷� ...

            $msg = $message.$price.$trans_date.$card_num."\n";

            /*---------------------------------------------------------------------
                // ���� �޽��� ȭ�� ���
            ---------------------------------------------------------------------*/
            echo "<<   ���۰�    >>    <br>";
            echo "�޼���Ÿ��:       ".$message."<br>";              //�޼���Ÿ��
            echo "�����������ID:   ".$userid."<br>";               // ����� ID


            //... �߷� ...

            /*---------------------------------------------------------------------------------------------------
               // �޽��� ��ȣȭ
               Triple DES �˷θ����� �̿�
               192bit Ű�� �����Ͽ� �޽����� ��ȣȭ �Ѵ�.
               $key = "012345678901234567890123";
               $ encrypted_msg = function encrypt($msg, $key);


               *********************************************************************************************
                1.  ��ȣȭ �˰����� 8byte ������ �������Ƿ� �� ���� �޽���(116 byte)�� 4byte�� �� �ٿ���
                        ��ȣȭ �� �� ������ ������.
                    (��ȣȭ �Լ����� ó��)
               *********************************************************************************************

            ----------------------------------------------------------------------------------------------------*/



            /*---------------------------------------------------------------------
                // Ŭ���̾�Ʈ ���� ����
            ---------------------------------------------------------------------*/

            $fp = fsockopen($config['sk_server_ip_addr'],$config['sk_server_port'],$errno,$errstr,5);

            if(!$fp){
                    echo "$errstr ($errno)<br>\n";

            } else {
                    /*---------------------------------------------------------------------
                        // �޼��� ����
                    ---------------------------------------------------------------------*/
                    $send_msg = fputs($fp, $msg);

                    $len = strlen($msg);
                    echo "Send msg Size : ".$len."<br><Br>";
                    echo "send = ".$send_msg."<br><Br>";
                    /*---------------------------------------------------------------------
                        // ���� ���� �޼��� ����
                    ---------------------------------------------------------------------*/
                    echo "<br><br>  <<   �����      >>  <br>";
                    $receive_msg = fgets($fp, 4096);
                    $len = strlen($receive_msg);
                    echo "Receive msg Size : ".$len."<br><Br>";
                    echo "receive = ".$receive_msg."<br><Br>";
                    /*---------------------------------------------------------------------
                        // ���� ��� ó��
                          - ���� ��� ���� �ڵ� �� "00" �� ��� ����, ���� �����
                            ������ ��� ���� Data ��ü ������ �ʿ���
                            ������ ��� ���� �ڵ� �� �޽��� ó��
                            (�ŷ� ���� �� �ŷ����� ��ǥ ����)
                    ---------------------------------------------------------------------*/
                    $messageType= substr($receive_msg, 0, 4);   // �޼��� Ÿ��
                    $price      = substr($receive_msg, 4,12);
                    $transDate  = substr($receive_msg,16,14);

                    //... �߷� ...

                    /*---------------------------------------------------------------------
                        // ���� �޽��� ȭ�� ���
                    ---------------------------------------------------------------------*/
                    echo "<<    ���Ű�    >>    <br>";
                    echo "�޼���Ÿ��:       ".$message."<br>";              //�޼���Ÿ��
                    echo "�����������ID:   ".$userid."<br>";               // ����� ID


                    //... �߷� ...


                    if ($cancelCode == "00") {
                        echo "<br><br>��ҵǾ����ϴ�. �����ڵ� :*".$cancelCode."*";
                    }
                    else {
                        echo "�̹� ��ҵǾ����ϴ�. ����ڵ� :*".$cancelCode."*";
                    }
            }

            /*---------------------------------------------------------------------
                // Ŭ���̾�Ʈ ���ϴݱ�
            ---------------------------------------------------------------------*/
            fclose($fp);


        }

?>

