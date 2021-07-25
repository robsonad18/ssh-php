<?php

namespace App\Connect;

/**
 * Responsavel por ações no SSH
 * lembre-se de configurar o SSH2 no PHP
 * @package App\Connect
 */
class SSH
{
	/**
	 * Instancia do recurso de conexão
	 * @var resource
	 */
	private $connection;



	/**
	 * Metodo responsavel por iniciar a conexao SSH
	 * @param string $host 
	 * @param int $port 
	 * @return bool 
	 */
	public function connect(string $host, int $port): bool
	{
		// nova conexão
		// Lembre de abilitar a lib SSH2 dentro do PHP
		$this->connection = ssh2_connect($host, $port);

		// retorna o sucesso 
		return $this->connection ? true : false;
	}



	/**
	 * Metodo responsavel por autentificar a conexao usando usuario e senha
	 * @param string $user 
	 * @param string $pass 
	 * @return bool 
	 */
	public function authPassword(string $user, string $pass): bool
	{
		return $this->connection ? ssh2_auth_password($this->connection, $user, $pass) : false;
	}



	/**
	 * Metodo responsavel por desconectar a conexao atual
	 * @return void 
	 */
	public function disconnect(): bool
	{
		// desconecta
		if ($this->connection) ssh2_disconnect($this->connection);

		// limpa a classe
		$this->connection = null;
		return true;
	}



	/**
	 * Metodo responsavel por obter uma saida de uma stream
	 * @param resource $stream 
	 * @param int $id 
	 * @return void 
	 */
	private function getOutput($stream, int $id): ?string
	{
		// stream de saida
		$streamOut = ssh2_fetch_stream($stream, $id);

		// conteudo da saida
		return stream_get_contents($streamOut);
	}



	/**
	 * Metodo responsavel por executar comandos SSH
	 * @param mixed $command 
	 * @return string 
	 */
	public function exec(string $command, &$stdErr = null): ?string
	{
		// verifica se a conexao existe
		if (!$this->connection) return null;

		// executa o comando SSH
		if (!$stream = ssh2_exec($this->connection, $command)) {
			return null;
		}

		// bloqueia a stream
		stream_set_blocking($stream, true);

		// saida STD IO
		$stdIo = $this->getOutput($stream, SSH2_STREAM_STDIO);

		// saida STD ERR
		$stdErr = $this->getOutput($stream, SSH2_STREAM_STDERR);

		// desbloqueia a stream
		stream_set_blocking($stream, false);

		// retorna o stdio
		return $stdIo;
	}
}
