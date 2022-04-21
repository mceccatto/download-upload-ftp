<?php

//EXIBE OS ERROS ENCONTRADOS NO CODIGO ENQUANTO O SCRIPT ESTA SENDO EXECUTADO
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

//CLASSE PARA EXECUCAO DO DOWNLOAD OU UPLOAD
class Execucao {

    private $dominio;
    private $porta;
    private $usuario;
    private $senha;
    private $conexao;
    private $dataLog;

    function dadosConexao($dominio,$porta,$usuario,$senha) {
        $this->dominio = $dominio;
        $this->porta = $porta;
        $this->usuario = $usuario;
        $this->senha = $senha;
    }

    function testeConexao() {
        $log = fopen('logs.log', 'a');
        $this->conexao = ftp_connect($this->dominio, $this->porta);
        if(!$this->conexao) {
            fwrite($log, $this->dataLog." - O servidor FTP não foi encontrado!\n");
            fclose($log);
            return false;
        }
    }

    function testeAutenticacao() {
        $log = fopen('logs.log', 'a');
        if(!ftp_login($this->conexao, $this->usuario, $this->senha)) {
            fwrite($log, $this->dataLog." - Usuário e/ou senha incorreto(s)!\n");
            fclose($log);
            return false;
        }
    }

    function executarUpload($destino,$arquivo) {
        $log = fopen('logs.log', 'a');
        $this->dataLog = date('d/m/Y H:i:s');
        ftp_pasv($this->conexao, true);
        if(ftp_put($this->conexao, $destino, $arquivo, FTP_BINARY)) {
            fwrite($log, $this->dataLog." - Upload efetuado com sucesso!\n");
            fclose($log);
            ftp_close($this->conexao);
        } else {
            fwrite($log, $this->dataLog." - Falha ao efetuar o upload!\n");
            fclose($log);
            ftp_close($this->conexao);
            return false;
        }
    }

    function executaDownload($destino,$arquivo) {
        $log = fopen('logs.log', 'a');
        $this->dataLog = date('d/m/Y H:i:s');
        ftp_pasv($this->conexao, true);
        if(ftp_get($this->conexao, $destino, $arquivo, FTP_BINARY)) {
            fwrite($log, $this->dataLog." - Download efetuado com sucesso!\n");
            fclose($log);
            ftp_close($this->conexao);
        } else {
            fwrite($log, $this->dataLog." - Falha ao efetuar o download!\n");
            fclose($log);
            ftp_close($this->conexao);
            return false;
        }
    }
}

//INFORMACOES NECESSARIAS PARA EFETUAR A CONEXAO FTP
$dominio = 'exemplo.com.br';
$porta = 21;
$usuario = 'usuario';
$senha = 'senha';

//INFORMACOES REFERENTES AO ARQUIVO PARA UPLOAD
$destinoUpload = '/caminho-ftp/exemplo.txt';
$arquivoUpload = 'exemplo.txt';

//INFORMACOES REFERENTES AO ARQUIVO PARA DOWNLOAD
$destinoDownload = 'exemplo.txt';
$arquivoDownload = '/caminho-ftp/exemplo.txt';

if($arquivoUpload) {
    $upload = new Execucao();
    $upload->dadosConexao($dominio,$porta,$usuario,$senha);
    $upload->testeConexao();
    $upload->testeAutenticacao();
    $upload->executarUpload($destinoUpload,$arquivoUpload);
}

if($arquivoDownload) {
    $download = new Execucao();
    $download->dadosConexao($dominio,$porta,$usuario,$senha);
    $download->testeConexao();
    $download->testeAutenticacao();
    $download->executaDownload($destinoDownload,$arquivoDownload);
}