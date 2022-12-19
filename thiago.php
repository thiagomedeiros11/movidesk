#!/usr/bin/php -q
<?php

require_once('/var/lib/asterisk/agi-bin/phpagi.php');
    $agi        = new AGI();
    $response1  = $agi->get_variable('URADINAMICA_RELATORIOS');
    $response   = str_replace("'" ,'"' , $response1['data']);
    $dados_info = json_decode(str_replace(';',',', $response), true);
foreach($dados_info as $row):
    $data       = $dados_info['data'];
    $sorteio    = end($dados_info['palavra_origem']);
    $bilhete    = $dados_info['bilheteunico'];
    $empresa    = $dados_info['empresa'];
    $destino    = $dados_info['tag'];
    $origem     = $dados_info['origem'];
endforeach;

$url = "https://api.movidesk.com/public/v1/tickets?token=qwerty";
$id = "&id=$sorteio";
// $id = "&id=7292";

$dados = $url . $id;


$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $dados);
$content = curl_exec($ch);

$dados_info = json_decode(str_replace(';', ',', $content), true);

foreach($dados_info as $row):
    $status = $dados_info['status'];
    $owner = $dados_info['owner'];
endforeach;

$treta = str_replace("'", '"', $owner);                                                                                                                              
$dados_infu = json_encode(str_replace(';', ',', $treta), true); 
$dados_dados = json_decode(str_replace(';', ',', $dados_infu), true);

foreach($dados_dados as $raw):
    $businessName = $dados_dados['businessName'];
    $phone = $dados_dados['phone'];
endforeach;

// print_r($owner);
// print_r($status);

if($status == "Aguardando validação do cliente"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_validacao_do_cliente");
}
if($status == "Em atendimento"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/em_atendimento");
}
if($status == "Aguardando analise da operadora que detem o número"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_analise_da_operadora");
}
if($status == "Aguardando aprovação comercial"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_aprovacao_comercial");
}
if($status == "Aguardando atualização"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_atualizacao");
}
if($status == "Aguardando portabilidade"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_portabilidade");
}
if($status == "Aguardando suporte de terceiros"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_suporte_terceiros");
}
if($status == "Atendimento Agendado"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/atendimento_agendado");
}
if($status == "Atividade Agendada"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/atividade_agendada");
}
if($status == "Atividade Pausada"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/atividade_pausada");
}
if($status == "Projeto aguardando aprovação para faturamento"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_aprovacao_faturamento");
}
if($status == "Projeto pendente de retorno termo de aceite final"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/pendente_termo_aceite");
}
if($status == "Retornar ao Cliente"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/retornar_ao_cliente");
}
if($status == "Novo"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/em_atendimento");
}
if($status == "Aguardando"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/status_atual");
    $agi->stream_file("/etc/asterisk/union/agi/ura/aguardando_atualizacao");
}

if($status == "Resolvido"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/desculpe_nao_encontrei");
    $agi->exec_goto($empresa.'-uradin-11', 2, 1);
    exit;
}
if($status == "Cancelado"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/desculpe_nao_encontrei");
    $agi->exec_goto($empresa.'-uradin-11', 2, 1);
    exit;
}
if($status == "Fechado"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/desculpe_nao_encontrei");
    $agi->exec_goto($empresa.'-uradin-11', 2, 1);
    exit;
}



// ----- //

if($businessName == "Thiago Medeiros"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/thiago_medeiros");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4306");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}
                                                                       
if($businessName == "Victor Ortega"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/victor_ortega");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-2284");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Ricardo Vasconcellos"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/ricardo_vasconcellos");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-2272");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Marcio Pança"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/marcio_panca");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-2292");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Leonardo Vasconcellos"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/leonardo_vasconcellos");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4303");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Leonardo Rodrigues"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/leonardo_rodrigues");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4304");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Kermeson Pereira"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/kermeson_pereira");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4307");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Hudson Amaral"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/hudson_amaral");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-2293");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Guilherme Leao"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/guilherme_leao");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4305");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Felipe Mituyama"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/felipe_mituyama");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-2291");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Diego Renato"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/diego_renato");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4301");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

if($businessName == "Diego Jarillo"){
    $agi->stream_file("/etc/asterisk/union/agi/ura/o_analista_responsavel");
    $agi->stream_file("/etc/asterisk/union/agi/ura/diego_jarillo");
    $agi->stream_file("/etc/asterisk/union/agi/ura/para_falar_com");
    $result = $agi->get_data('beep', 5000, 1);
    $resumo = $result['result'];
    if($resumo != 1){
        $agi->stream_file("/etc/asterisk/union/agi/ura/a_union_agradece");
    }
    if($resumo == 1){
        $dial = $agi->exec("DIAL", "PJSIP/union-4302");
        if($dial['result'] == 0){
            $agi->stream_file("/etc/asterisk/union/agi/ura/analista_ocupado");
        }
    }
}

