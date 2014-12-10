<?php

if (!function_exists('ll_is_mobile'))  {

	function ll_is_mobile() {

		$mobilefound = false;
		$text = $_SERVER['HTTP_USER_AGENT'];

		$var[0] = 'Mozilla/4.';
		$var[1] = 'Mozilla/3.0';
		$var[2] = 'AvantGo';
		$var[3] = 'ProxiNet';
		$var[4] = 'Danger hiptop 1.0';
		$var[5] = 'DoCoMo/';
		$var[6] = 'Google CHTML Proxy/';
		$var[7] = 'UP.Browser/';
		$var[8] = 'SEMC-Browser/';
		$var[9] = 'J-PHONE/';
		$var[10] = 'PDXGW/';
		$var[11] = 'ASTEL/';
		$var[12] = 'Mozilla/1.22';
		$var[13] = 'Handspring';
		$var[14] = 'Windows CE';
		$var[15] = 'PPC';
		$var[16] = 'Mozilla/2.0';
		$var[17] = 'Blazer/';
		$var[18] = 'Palm';
		$var[19] = 'WebPro/';
		$var[20] = 'EPOC32-WTL/';
		$var[21] = 'Tungsten';
		$var[22] = 'Netfront/';
		$var[23] = 'Mobile Content Viewer/';
		$var[24] = 'PDA';
		$var[25] = 'MMP/2.0';
		$var[26] = 'Embedix/';
		$var[27] = 'Qtopia/';
		$var[28] = 'Xiino/';
		$var[29] = 'BlackBerry';
		$var[30] = 'Gecko/20031007';
		$var[31] = 'MOT-';
		$var[32] = 'UP.Link/';
		$var[33] = 'Smartphone';
		$var[34] = 'portalmmm/';
		$var[35] = 'Nokia';
		$var[36] = 'Symbian';
		$var[37] = 'AppleWebKit/413';
		$var[38] = 'UPG1 UP/';
		$var[39] = 'RegKing';
		$var[40] = 'STNC-WTL/';
		$var[41] = 'J2ME';
		$var[42] = 'Opera Mini/';
		$var[43] = 'SEC-';
		$var[44] = 'ReqwirelessWeb/';
		$var[45] = 'AU-MIC/';
		$var[46] = 'Sharp';
		$var[47] = 'SIE-';
		$var[48] = 'SonyEricsson';
		$var[49] = 'Elaine/';
		$var[50] = 'SAMSUNG-';
		$var[51] = 'Panasonic';
		$var[52] = 'Siemens';
		$var[53] = 'Sony';
		$var[54] = 'Verizon';
		$var[55] = 'Cingular';
		$var[56] = 'Sprint';
		$var[57] = 'AT&T;';
		$var[58] = 'Nextel';
		$var[59] = 'Pocket PC';
		$var[60] = 'T-Mobile';    
		$var[61] = 'Orange';
		$var[62] = 'Casio';
		$var[63] = 'HTC';
		$var[64] = 'Motorola';
		$var[65] = 'Samsung';
		$var[66] = 'NEC';
		$var[67] = 'iPhone';
		$var[68] = 'iPod';
		$var[69] = 'Android';
		$var[70] = 'webOS';
		$var[71] = 'IEmobile';

		$result = count($var);

		for ($i=0;$i<$result;$i++)
		{    
			$ausg = stristr($text, $var[$i]);    
			if(strlen($ausg)>0 )
			{   
				$msie = stristr($text, 'compatible; MSIE');
				if (strlen($msie)==0 ) {
					$mobilefound = true;
				}
			}
    
		}

	return $mobilefound;

	}

}
?>
