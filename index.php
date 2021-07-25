<?php

require __DIR__ . '/vendor/autoload.php';

use App\Connect\SSH;

$obSSH = new SSH;

// conexao
if (!$obSSH->connect('', 2222)) {
	die('Conexão falhou');
}

// autentificação
if (!$obSSH->authPassword('', '')) {
	die('Conexão falhou');
}

// executa comandos
$stdIo = $obSSH->exec('ls -l', $stdErr);
echo "STDERR:" . $stdErr;
echo "STDIO:" . $stdIo;

// desconecta
$obSSH->disconnect();
