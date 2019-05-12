<?php

$url = 'https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types';

$ch = curl_init();
$timeout = 30;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$data = curl_exec($ch);
curl_close($ch);

$apache = array();

$data = str_replace("\r","",$data);
$data = explode("\n",$data);

foreach ($data as $line) {
    $line = trim($line);
    if (substr($line,0,1)=='#' || $line=='') {
	continue;
    }
    $line = preg_replace("#\s+#","\t",$line);
    $line = trim($line);
    $info = explode("\t",$line);
    $info[0] = preg_replace('#^([^/]+)/#','',$info[0]);
    $info[0] = preg_replace('#^x-#','',$info[0]);
    $info[0] = preg_replace('#^vnd\.#','',$info[0]);
    if (count($info)>2) {
	$tot = count($info);
	for ($i=1;$i<$tot;$i++) {
	    $apache['.'.$info[$i]] = array( $info[0], preg_replace('#([^A-Za-z0-9]+)#',' ',$info[0]) );
	}
    } else {
	$apache['.'.$info[1]] = array( $info[0], preg_replace('#([^A-Za-z0-9]+)#',' ',$info[0]) );
    }
}

// print_r($apache);die;

$url = 'https://www.freeformatter.com/mime-types-list.html';

$ch = curl_init();
$timeout = 30;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$data = curl_exec($ch);
curl_close($ch);

$search_start = '<div id="mime-types-list">';
$search_end   = '</div>';

$start = stripos($data,$search_start)+strlen($search_start);

$data  = substr($data,$start);

$end   = stripos($data,$search_end);

$data  = substr($data,0,$end-1);

$data  = trim($data);
$data  = preg_replace('#\s+#',' ',$data);
$data  = str_replace('> <','><',$data);

$data  = preg_replace('#<h2(.*)</h2>#i','',$data);
$data  = preg_replace('#<table([^>]*)>#i','',$data);
$data  = preg_replace('#<thead([^>]*)>#i','',$data);
$data  = str_ireplace('</thead>','',$data);
$data  = str_ireplace('</table>','',$data);
$data  = str_ireplace('<tbody>','',$data);
$data  = str_ireplace('</tbody','',$data);

$lista = explode('</tr>',$data);
unset($lista[0]);
unset($lista[1]);


// print_r($lista);die;

$array = array();

$extra = array(
 '.tgz' => array( 'application/x-gzip','Slackware Package TGZ',false),
 '.txz' => array( 'application/x-xz','Slackware Package TXZ',false),
 '.xz'  => array( 'application/x-xz','Xz compressed file',false),
 '.msp' => array( 'application/octet-stream', 'Microsoft Patch File',false),
 '.msu' => array( 'application/octet-stream', 'Microsoft Update File', false),
 '.bld' => array( 'application/octet-stream', 'Energy Pro File', false),
 '.m4r' => array( 'audio/aac', 'iPhone ringtone', false),
 '.dot' => array( 'application/msword', 'Microsoft Word Template', false),
 '.gpx' => array('application/gpx+xml', 'GPS eXchange Format', false),
 '.woff2' => array('application/font-woff2', 'Woff2 Font', false),
 '.notebook' => array('application/x-smarttech-notebook','Notebook Smart board', false),
 '.gallery' => array('application/x-smarttech-notebook','Gallery Smart board', false),
 '.mobi' => array('application/x-mobipocket-ebook','Mobi EBook',false),
 '.pages' => array('application/x-iwork-pages-sffpages','Apple Pages document',false),
 '.numbers' => array('application/x-iwork-numbers-sffnumbers','Apple Numbers spreadsheet',false),
 '.keynote' => array('application/x-iwork-keynote-sffkey','Apple Keynote Presentation',false),
 '.key' => array('application/x-iwork-keynote-sffkey','Apple Keynote Presentation',false),
 '.msg' => array('application/vnd.ms-outlook','Outlook mail message',false),
 '.heic' => array('image/heic','High Efficiency Image File Format (HEIC)',false),
 '.heif' => array('image/heif','High Efficiency Image File Format (HEIF)',false)
);

$exts = array();

$fixes = array(
    '.exe' => array( 'mime_type' => 'application/vnd.microsoft.portable-executable', 'application'=>'' )
);

foreach ($lista as $elemento) {

	$elemento = str_ireplace('<td>','',$elemento);
	$elemento = str_ireplace('<tr>','',$elemento);
	$elemento = trim($elemento);
	if (!$elemento) {
		continue;
	}
	$parti = explode('</td>',$elemento);
	if (!isset($parti[1])) { continue; }
	$applicazione = trim($parti[0]);
	$tipo         = trim($parti[1]);
	$estensioni   = explode(',',str_replace(' ','',$parti[2]));
	
	foreach ($estensioni as $ext) {
	    if (isset($fixes[$ext])) {
		$applicazione = $fixes[$ext]['application'] ? : $applicazione;
		$tipo         = $fixes[$ext]['mime_type'] ? : $tipo;
	    }
	}
	
	$tmp = new \stdClass();
	$tmp->application = $applicazione;
	$tmp->mime_type   = $tipo;
	$tmp->extensions  = $estensioni;
	
	$exts = array_merge($exts,$estensioni);
	
	$array[] = $tmp;
	
}


asort($exts);

foreach ($extra as $ext=>$dati) {
	if (!in_array($ext,$exts)) {
	    if (isset($fixes[$ext])) {
		$dati[1] = $fixes[$ext]['application'] ? : $dati[1];
		$dati[0] = $fixes[$ext]['mime_type'] ? : $dati[0];
	    }
	    $tmp = new \stdClass();
	    $tmp->application = $dati[1];
	    $tmp->mime_type   = $dati[0];
	    $tmp->extensions  = array( $ext );
	    $array[] = $tmp;
	}
}

foreach ($apache as $ext=>$dati) {
    if (!in_array($ext,$exts)) {
	if (isset($fixes[$ext])) {
	    $dati[1] = $fixes[$ext]['application'] ? : $dati[1];
	    $dati[0] = $fixes[$ext]['mime_type'] ? : $dati[0];
	}
	$tmp = new \stdClass();
	$tmp->application = $dati[1];
	$tmp->mime_type   = $dati[0];
	$tmp->extensions  = array( $ext );
	$array[] = $tmp;
    }
}

function doSort($a,$b) {
		if ($a->application < $b->application) return -1;
		if ($a->application > $b->application) return +1;
		return 0;
}

usort($array,'doSort');

// print_r($array);die;

file_put_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'mime-list.txt',serialize($array));

?>