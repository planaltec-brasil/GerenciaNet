<?php
$dataIni = date(DATE_ATOM, mktime(0, 0, 0, date('m'), date('d'), date('Y')));
echo "<br >";
$dataFim = date(DATE_ATOM, mktime(23, 59, 59, date('m'), date('d'), date('Y')));
class Gerencianet {

    private $urlHomologacao = 'https://api-pix-h.gerencianet.com.br';
    private $urlProducao = 'https://api-pix.gerencianet.com.br';

    private $client_Id = 'Client_Id_13b7837a2863d760ebaee942979565de768e8c69';
    private $client_secrets = 'Client_Secret_e954e51aa5500af470362ed278a4898011d44843';
    private $certificadoDigital = 'homologacao.pem';
    
    private $ambiente = 0;
    
    private $token_type = '';
    private $access_token = '';

    function __construct($ambiente = 0) {
        $this->ambiente = $ambiente;

        if($ambiente == 0) {
            $this->certificadoDigital = 'producao.pem';
        } else {
            $this->certificadoDigital = 'homologacao.pem';
        }
    }

//WH = Webhook
    public function exibeInfoWhPix($chave){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/webhook' . $chave;

        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            $chave => 'chave'
        ];
        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;
    }

    public function consultaListaWH($inicio, $fim){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/webhook/';

        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            $inicio => 'inicio',
            $fim => 'fim'
        ];
        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;
    }

    public function cancelaWhPix($chave){
            if($this->token_type == '')
                return false;
    
            $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/webhook/' . $chave;
    
            $header = array(
                "Content-Type: application/json",
                "Authorization: " . $this->token_type . " " . $this->access_token,
            );
    
            $dados = [
            ];
            $response = $this->requisicao($url, $dados, $header, "DELETE");
            return $response;

    }

    public function reqExtratoConciliacao($inicio){
        if($this->token_type == '')
        return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/gn/relatorios/extrato-conciliacao';
        
        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            'dataMovimento' => $inicio,
            'tipoRegistros' => [
                'pixRecebido' => true,
                'pixEnviadoChave' => true,
                'pixEnviadoDadosBancarios' => true
            ],
        ];
        $response = $this->requisicao($url, $dados, $header, 'POST');
        return $response;
    }

    public function solicitaDownloadExtratoConc($id){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/gn/relatorios/'. $id;

        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
        ];
        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;
    }

    public function recebendoCallback(){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/webhook/';

        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            $endToEndId => 'endToEndId',
            $valor => 'valor'
        ];
        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;
    } 

    public function consultaPixUnico($e2e){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/gn/pix/enviados/' .$e2e;
        
        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
        ];
        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;
    }
    
    public function consultaDevolucao($idEnvio, $e2e, $valor){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . "/v2/pix/$e2e/devolucao/$idEnvio";
        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            'valor' => $valor
        ];

        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;
    }

    public function solicitaDevolucao($idEnvio, $e2e, $valor){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . "/v2/pix/$e2e/devolucao/$idEnvio";
        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            'valor' => $valor
        ];

        $response = $this->requisicao($url, $dados, $header, 'PUT');
        return $response;
    }
    
    public function consultaListaPix($inicio, $fim, $status = null){
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/gn/pix/enviados/';
        
        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );

        $dados = [
            'inicio' => $inicio,
            'fim' => $fim

        ];
        $response = $this->requisicao($url, $dados, $header, 'GET');
        return $response;

    }
    
    public function envioPix($idEnvio, $valor = 0, $chave_pagador = '', $info_pagador = '', $chave_favorecido){
        if($this->token_type == '')
            return false;
    
        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/gn/pix/' . $idEnvio;

        $dados = [
            'valor' => $valor,
            'pagador' => [
                'chave' => $chave_pagador,
                'infoPagador' => $info_pagador
            ],
            'favorecido' => [
                'chave' => $chave_favorecido
            ]
        ];

        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
        );
        
        $response = $this->requisicao($url, $dados, $header, 'PUT');
        return $response;
    }

    public function Autenticacao () {
        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/oauth/token';

        $auth = base64_encode("$this->client_Id:$this->client_secrets");

        $header = array(
            "Content-Type: application/json",
            "Authorization: Basic $auth",
        );

        $response = $this->requisicao($url, [ "grant_type" => "client_credentials" ], $header, 'POST');
        
        if(isset($response['body']['token_type'])){
            $this->token_type = $response['body']['token_type'];
            $this->access_token = $response['body']['access_token'];
        } else {
            var_dump($response);
        }

    }

    public function configWebhook($chave) {
        if($this->token_type == '')
            return false;

        $url = ($this->ambiente == 0 ? $this->urlProducao : $this->urlHomologacao) . '/v2/webhook/' . $chave;

        $dados = [
            'webhookUrl' => "https://webhook.site/b9f44ae5-b9b8-4644-ac5c-4e0a4797589e",
            // 'webhookUrl' => "https://gsplanaltec.com/GerenciamentoServicos/APIControle/webhook",
        ];

        $header = array(
            "Content-Type: application/json",
            "Authorization: " . $this->token_type . " " . $this->access_token,
            "x-skip-mtls-checking: true"
        );

        $response = $this->requisicao($url, $dados, $header, 'PUT');
        return $response;
    }

    private function requisicao($url, $data = array(), $header = array(), $type = "POST"){        
        if($type == "GET" && is_array($data) && count($data) > 0)
            $url = $url . "?" . http_build_query($data);
        
        $curl = curl_init($url);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        @curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        // curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($curl, CURLOPT_SSLCERT, realpath($this->certificadoDigital));
        // curl_setopt($curl, CURLOPT_SSLCERTPASSWD, '');
        
        if($type != 'GET') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $curl_response = curl_exec($curl);

        $response = curl_getinfo($curl);
        $response['body'] = json_decode($curl_response, true);
        $response['error'] = curl_error($curl);
        curl_close($curl);

        return $response;
    }

}


$teste = new Gerencianet(1);
$teste->Autenticacao();
$teste->configWebhook('09089356000118');
$ep = $teste->envioPix('51a8ds181818851a651788965sa', '0.01', '09089356000118', "Segue o pagamento da conta", "efipay@sejaefi.com.br");
// $pixUnic = $teste->consultaListaPix($dataIni, $dataFim);
// $recCallback = $teste->recebendoCallback();
var_dump($ep);