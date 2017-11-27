<?php 

function getNewUrl()
{
	$baseurl = "http://istudent.uitm.edu.my/nsp/examttable";
	$dir = "/JWP_CURRENT_B/index.html";
	   
	// need to fetch the new redirected url first, this is because the url will change each semester
	$ch = curl_init(); # initialize curl object
	curl_setopt($ch, CURLOPT_URL, $baseurl.$dir); # set url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # disable SSL verification (THIS IS NOT PROPER/SAFE)
	$result = curl_exec($ch); # execute curl, fetch webpage content
	$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
	$err = curl_error($ch);
	curl_close($ch);  # close curl

	# use regex to to parse html and get the new redirected url
	$patern = '#window.location="..([\w\W]*?)";#';
	preg_match_all($patern, $result, $parsed); 
	$newdir = implode(str_replace(['window.location="..','";','index.html'], "", $parsed[0]),"");

	// new url
	$newbaseurl = $baseurl.$newdir;

	return $newbaseurl;
}

if($_GET['option'] == "listprogrammes")
{
	$newbaseurl = getNewUrl();

	// fetch list of Programmes
	$ch = curl_init(); # initialize curl object
	curl_setopt($ch, CURLOPT_URL, $newbaseurl."/list.html"); # set url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # disable SSL verification (THIS IS NOT PROPER/SAFE)
	$result = curl_exec($ch); # execute curl, fetch webpage content
	$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
	$err = curl_error($ch);
	curl_close($ch);  # close curl

	# use regex to to parse html 
	$patern = '#<UL>([\w\W]*?)</UL>#';
	preg_match_all($patern, $result, $parsed); 

	$paternli = '#<A([\w\W]*?)<\/A>#';
	preg_match_all($paternli, $parsed[0][0], $parsed); 

	$progarray = array();

	for($i=0;$i<count($parsed[0]);$i++)
	{
		// check if not contain programmes code, skip
		if (strpos($parsed[0][$i], '<A NAME="') !== false) 
		{
		    //do nothing
		}
		else
		{
			$paternhref = '#"([\w\W]*?)"#';
			preg_match_all($paternhref, $parsed[0][$i], $linkparsed); 

			$progarray[$i]['code'] = strip_tags($parsed[0][$i]);
			$progarray[$i]['url'] = str_replace('"', '', $linkparsed[0][0]);
		}
	}

	// return list in JSON
	echo json_encode($progarray);
}

if($_GET['option'] == "timetable")
{
	$newbaseurl = getNewUrl();

	// the programmes code url
	$progurl = $_GET['progcode'];

	// fetch the timetable
	$ch = curl_init(); # initialize curl object
	curl_setopt($ch, CURLOPT_URL, $newbaseurl."/".$progurl); # set url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); # receive server response
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); # disable SSL verification (THIS IS NOT PROPER/SAFE)
	$result = curl_exec($ch); # execute curl, fetch webpage content
	$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); # receive http response status
	$err = curl_error($ch);
	curl_close($ch);  # close curl

	print_r($result);
}



?>