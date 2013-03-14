 <?php
    function create_tracking_task($email, $itemId){
        if ($email == '') return -1;
		
        $pathDelim = strpos($email, '/');
        if ($pathDelim !== false) return -2;
        
        $emailFld = 'tasks/'.$email;
        if (!is_dir($emailFld) && !@mkdir($emailFld)) return -3;
        
        $itemFile = $emailFld.'/'.$itemId;
        if (is_file($itemFile)) return -4;

        $file = @fopen($itemFile, 'w');
        if ($file === false) return -5;        
        fwrite($file, '0');
        fclose($file);

		return 0;
    }    

    $tracking_data = '&nbsp;';

    $sPage = file_get_contents("st/getInfo.st");

    $itemId = $_POST['itemid'];
    if ( $itemId != '' ) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,"http://www.posta.md:8081/IPSWeb_item_events.asp?itemid=$itemId&Submit=Accept");
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $buffer = curl_exec($ch);
        curl_close($ch);

        $err_msg = 'Informatie nu exista, va rugam sa verificati daca id-ul a fost introdus corect!';
        $err_msg_pos = strpos($buffer, $err_msg);
        
        if ($err_msg_pos === false){
              $errId = create_tracking_task($_POST['email'], $itemId);
              print $errId;
              
              $data_lc_start = '<table width="95%" border="0" cellpadding="0" cellspacing="0">';
              $data_lc_end = '</table>';
 
              $tracking_data = $buffer;
              $data_lc_pos = strpos($tracking_data, $data_lc_start);
              $tracking_data = substr($tracking_data, $data_lc_pos);
              $data_lc_pos = strpos($tracking_data,  $data_lc_end);              
              $tracking_data = substr($tracking_data, 0, $data_lc_pos + strlen($data_lc_end));

              $tracking_data = str_replace('<a href="IPSWeb_submit.htm">','<a href="index.php">', $tracking_data);
        }
        else {
              $tracking_data = $err_msg;
        }
    }

    $sPage = str_replace('[@DATA]', $tracking_data, $sPage);
    print $sPage;
?>
