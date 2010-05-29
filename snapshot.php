<?php
/**
 * @author: Wang, Qing
 * @date: 2010-05-28
 * @version: 1.0
 */
/**
 * usage:
	$url = 'http://www.baidu.com/'; // Grab Baidu  
	echo snapshot($url);  // Output results as picture address  
	echo snapshot($url, './baidu.png'); // Save pictures to a local baidu.png, the output image size  
*/

/**
 *  Build Web page snapshots  
 * @param   string  $site    Destination address  
 * @param   string  $path    Save address, null is not saved  
 * @param   integer $dealy   Delay  
 * @return  mixed    According to the parameter returns  
 */
function snapshot($site, $path = '', $dealy = 0)
{
    $url   = 'http://ppt.cc/yo2/catch.php';
    $query = 'url=' . $site . '&delay=' . $dealy . '&rnd=' . mt_rand(1, 9);
    $ch    = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);


    if (strlen($data) != 32) {
        exit(' Invalid URL  ');
    }


    $file = $data{0} . '/' . $data{1} . '/' . $data{2} . '/';
    $file = 'http://cache.ppt.cc/' . $file . 'src_' . $data . '.png';


    if (!empty($path)) {
        $data = file_get_contents($file);
        return file_put_contents($path, $data);
    }
    return $file;
}
