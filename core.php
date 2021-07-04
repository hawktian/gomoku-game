<?php
header('Content-Type: application/json');
$request = file_get_contents('php://input');

if ( is_array($data = json_decode($request, true)) ) {
    if ( $data['act'] == 'initPK' ) {
        $PK = [];
        $PK['ID'] = time().mt_rand(10000,999999);
        $PK['color'] = 'black';
        $PK['order'] = 'black';
        echo json_encode($PK);
    }
}

