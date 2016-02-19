<?php

function MakeDbID() 
{
	$seedstr	=	explode(" ",microtime(),5);
	$seed 		=	$seedstr[0]*100000;
	srand($seed);
	$random 	=	rand(10000,99999);
	$id 		= 	date("YmdHis", time()).$random;
	return $id;
}
function ClinentIp()
{
	$client_ip	=	$_SERVER['REMOTE_ADDR'];
	return $client_ip;
}





?>