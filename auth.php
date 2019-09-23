<?php
/**
 * Скрипт авторизации для админки
 */
$realm = 'Секретная фраза';

$users = array('admin' => 'mypass', 'guest' => 'guest');


if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die('Необходимо авторизироваться!'); //Текст, отправляемый в том случае, если пользователь нажал кнопку Cancel
}


// анализируем переменную PHP_AUTH_DIGEST
if (!($auth_data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) || !isset($users[$auth_data['username']])) die('Неправильные данные!');


// генерируем корректный ответ
$A1 = md5($auth_data['username'] . ':' . $realm . ':' . $users[$auth_data['username']]);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$auth_data['uri']);
$valid_response = md5($A1.':'.$auth_data['nonce'].':'.$auth_data['nc'].':'.$auth_data['cnonce'].':'.$auth_data['qop'].':'.$A2);

if ($auth_data['response'] != $valid_response) die('Неправильные данные!');

// ok, логин и пароль верны
// echo 'Вы вошли как: ' . $auth_data['username'];
if(isset($_GET['debug'])) var_dump($auth_data);


// функция разбора заголовка http auth
function http_digest_parse($txt) {
    // защита от отсутствующих данных
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $auth_data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $auth_data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $auth_data;
}
?>