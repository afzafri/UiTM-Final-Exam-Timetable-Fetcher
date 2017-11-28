<?php 

// Function for performing cURL request
function fetchPage($url)
{
	$ch = curl_init(); // initialize curl object
	curl_setopt($ch, CURLOPT_URL, $url); // set url
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // receive server response
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disable SSL verification (THIS IS NOT PROPER/SAFE)
	$result = curl_exec($ch); // execute curl, fetch webpage content
	$httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE); // receive http response status
	$err = curl_error($ch);
	curl_close($ch);  // close curl

	$pageData = array();
	$pageData['result'] = $result;
	$pageData['httpstatus'] = $httpstatus;
	$pageData['error'] = $err;

	return $pageData; // return array
}

// Function for fetching new URL
function getNewUrl()
{
	$baseurl = "http://istudent.uitm.edu.my/nsp/examttable";
	$dir = "/JWP_CURRENT_B/index.html";
	   
	// need to fetch the new redirected url first, this is because the url will change each semester
	$pageData = fetchPage($baseurl.$dir);

	// use regex to to parse html and get the new redirected url
	$patern = '#window.location="..([\w\W]*?)";#';
	preg_match_all($patern, $pageData['result'], $parsed); 
	$newdir = implode(str_replace(['window.location="..','";','index.html'], "", $parsed[0]),"");

	// new url
	$newbaseurl = $baseurl.$newdir;

	return $newbaseurl;
}

// --- List all Programmes ---
// usage: api.php?option=listprogrammes
if($_GET['option'] == "listprogrammes")
{
	$newbaseurl = getNewUrl();

	// fetch list of Programmes
	$pageData = fetchPage($newbaseurl."/list.html");

	// use regex to to parse html 
	$patern = '#<UL>([\w\W]*?)</UL>#';
	preg_match_all($patern, $pageData['result'], $parsed); 

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

// --- List all Courses ---
// usage: api.php?option=listcourses
if($_GET['option'] == "listcourses")
{
	$newbaseurl = getNewUrl();

	// fetch list of Courses
	$pageData = fetchPage($newbaseurl."/list.html");

	// use regex to to parse html 
	$patern = '#<UL>([\w\W]*?)</UL>#';
	preg_match_all($patern, $pageData['result'], $parsed); 

	$paternli = '#<A([\w\W]*?)<\/A>#';
	preg_match_all($paternli, $parsed[0][1], $parsed); 

	$coursearray = array();

	for($i=0;$i<count($parsed[0]);$i++)
	{
		// check if not contain courses code, skip
		if (strpos($parsed[0][$i], '<A NAME="') !== false) 
		{
		    //do nothing
		}
		else
		{
			$paternhref = '#"([\w\W]*?)"#';
			preg_match_all($paternhref, $parsed[0][$i], $linkparsed); 

			$coursearray[$i] = strip_tags($parsed[0][$i]);
		}
	}

	// return list in JSON
	echo json_encode($coursearray);
}

// --- Fetch Timetable ---
// usage: api.php?option=timetable&progcode=PROGRAMMESCODE
if($_GET['option'] == "timetable")
{
	$newbaseurl = getNewUrl();

	// the programmes code url
	$progurl = $_GET['progcode'];

	// fetch the timetable
	$pageData = fetchPage($newbaseurl."/".$progurl);

	// use regex to to parse html, get the table 
	$patern = '#<TABLE([\w\W]*?)<\/TABLE>#';
	preg_match_all($patern, $pageData['result'], $parsed); 

	print_r($parsed[0]);
}



?>