<?php
require_once('conf.php');
if (  FALSE === $conf['enable_ssl'] ) {
    $host['http'] = 'http://'.$conf['domain'].':'.$conf['port'];
    $host['websocket'] = 'ws://'.$conf['domain'].':'.$conf['socket_port'];
} else {
    $host['http'] = 'https://'.$conf['domain'].':'.$conf['port'];
    $host['websocket'] = 'wss://'.$conf['domain'].':'.$conf['socket_port'];
}
