<?php
preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);// Analysis HTTP_ACCEPT_LANGUAGE property
$lang = $matches[1];// Take the first language settings
$lang = strtolower($lang);// Converted to lowercase
// default lang & type
$lang = $lang;
putenv('LANG=en_US');
setlocale(LC_ALL, $lang);

$lang = isset($_GET['lang']) ? $_GET['lang'] : $lang;

// en_US Languages
if ('en-us' == $lang) {
    putenv('LANG=en_US');
    setlocale(LC_ALL, 'en_US');
// zh_TW Languages
} else if ('zh-tw' == $lang) {
    putenv('LANG=zh_TW');
    setlocale(LC_ALL, 'zh_TW'); // bsd use zh_TW.BIG5
	header('Content-type: text/html; charset=big5');
} else if ('zh-hk' == $lang) {
    putenv('LANG=zh_TW');
    setlocale(LC_ALL, 'zh_TW'); // bsd use zh_TW.BIG5
	header('Content-type: text/html; charset=big5');
// zh_CN Languages
} else if ('zh-cn' == $lang) {
    putenv('LANG=zh_CN');
    setlocale(LC_ALL, 'zh_CN'); // bsd use zh_CN.GBK
	header('Content-type: text/html; charset=gbk');
} else if ('zh-sg' == $lang) {
    putenv('LANG=zh_CN');
    setlocale(LC_ALL, 'zh_CN'); // bsd use zh_CN.GBK
	header('Content-type: text/html; charset=gbk');
}

?>