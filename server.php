<?php
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;
use Swoole\Coroutine\Http\Server;
use function Swoole\Coroutine\run;

require_once('init.php');

run(function () use ($conf) {

    //配置SSL证书
    if (  true === $conf['enable_ssl'] ) {
        $server = new Server($conf['domain'], $conf['socket_port'], SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
        $server ->set([
            'daemonize' => false,
            'ssl_cert_file' => $conf['ssl_cert_file'],
            'ssl_key_file'  => $conf['ssl_key_file']
        ]);
    } else {
        $server = new Server($conf['domain'], $conf['socket_port']);
    }

    $server->handle('/', function (Request $request, Response $ws) {
        $ws->upgrade();
        global $wsObjects;
        $objectId = spl_object_id($ws);
        $wsObjects[$objectId] = $ws;
        while (true) {
            $frame = $ws->recv();

			$data = json_decode($frame->data, true);
			if ( $data['color'] == 'black' ) {
				$data['order'] = 'white';
			} else {
				$data['order'] = 'black';
			}

            if ($frame === '') {
                unset($wsObjects[$objectId]);
                $ws->close();
                break;
            } else if ($frame === false) {
                echo 'errorCode: ' . swoole_last_error() . "\n";
                $ws->close();
                break;
            } else {
                if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                    unset($wsObjects[$objectId]);
                    $ws->close();
                    break;
                }
                foreach ($wsObjects as $obj) {
                    $obj->push(json_encode($data));
                }
            }
        }
    });
    $server->start();
});
