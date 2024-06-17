<?php



if (!defined('JSON_FILE')) {
    define('JSON_FILE', 'fluxo_caixa.json');
}

if (!defined('JSON_FILE_FECHADOS')) {
    define('JSON_FILE_FECHADOS', 'fluxo_caixa_fechados.json');
}

function readJsonFile($file) {
    if (!file_exists($file)) {
        return [];
    }
    $jsonData = file_get_contents($file);
    return json_decode($jsonData, true);
}

function writeJsonFile($file, $data) {
    $jsonData = json_encode($data, JSON_PRETTY_PRINT);
    if ($jsonData === false) {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao converter dados para JSON.']);
        exit;
    }
    file_put_contents($file, $jsonData);
}

function getNextId() {
    $dataFluxoCaixa = readJsonFile(JSON_FILE);
    $dataFluxoCaixaFechados = readJsonFile(JSON_FILE_FECHADOS);

    // Obter o último ID nos lançamentos de fluxo caixa
    $lastEntry = end($dataFluxoCaixa);
    $lastIdFluxoCaixa = isset($lastEntry['id']) ? intval($lastEntry['id']) : 0;

    // Obter o último ID nos lançamentos fechados
    $lastEntryFechados = end($dataFluxoCaixaFechados);
    $lastIdFluxoCaixaFechados = isset($lastEntryFechados['id']) ? intval($lastEntryFechados['id']) : 0;

    // Retornar o próximo ID, considerando o maior dos dois
    return str_pad(max($lastIdFluxoCaixa, $lastIdFluxoCaixaFechados) + 1, 5, '0', STR_PAD_LEFT);
}

function addEntry() {
    $data = readJsonFile(JSON_FILE);
    $newEntry = [
        'id' => getNextId(),
        'data_hora' => date('Y-m-d H:i:s'),
        'descricao' => $_POST['descricao'],
        'valor' => $_POST['valor'],
        'forma_pagamento' => $_POST['forma_pagamento'],
        'tipo_compra' => $_POST['tipo_compra'],
        'categoria' => $_POST['categoria'],
        'comprador' => $_POST['comprador']
    ];
    $data[] = $newEntry;
    writeJsonFile(JSON_FILE, $data);
}

function editEntry() {
    $idTransEdit = $_POST['idTransEdit'];
    $descricaoEdit = $_POST['descricaoEdit'];
    $valorEdit = $_POST['valorEdit'];
    $formaPagamentoEdit = $_POST['formaPagamentoEdit'];
    $tipoCompraEdit = $_POST['tipoCompraEdit'];
    $categoriaEdit = $_POST['categoriaEdit'];
    $compradorEdit = $_POST['compradorEdit'];

    $data = readJsonFile(JSON_FILE);
    $found = false;

    foreach ($data as &$entry) {
        if ($entry['id'] == $idTransEdit) {
            $entry['descricao'] = $descricaoEdit;
            $entry['valor'] = $valorEdit;
            $entry['forma_pagamento'] = $formaPagamentoEdit;
            $entry['tipo_compra'] = $tipoCompraEdit;
            $entry['categoria'] = $categoriaEdit;
            $entry['comprador'] = $compradorEdit;
            $found = true;
            break;
        }
    }

    if ($found) {
        writeJsonFile(JSON_FILE, $data);
        echo json_encode(['status' => 'success', 'message' => 'Entry updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro: ID de transação não encontrado!']);
    }
}

function deleteEntry() {
    if (isset($_GET['delete'])) {
        $data = readJsonFile(JSON_FILE);
        $data = array_filter($data, function($entry) {
            return $entry['id'] != $_GET['delete'];
        });

        writeJsonFile(JSON_FILE, array_values($data));
        echo json_encode(['status' => 'success', 'message' => 'Entry deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID de exclusão não fornecido!']);
    }
}

// Função para fechar o caixa automaticamente
function closeCash() {
    $data = readJsonFile(JSON_FILE);
    $today = date('Y-m-d');
    $closedEntries = array_filter($data, function($entry) use ($today) {
        return strpos($entry['data_hora'], $today) === 0;
    });

    // Salve os lançamentos fechados no arquivo de fechados
    $closedData = readJsonFile(JSON_FILE_FECHADOS);
    $closedData = array_merge($closedData, $closedEntries);
    writeJsonFile(JSON_FILE_FECHADOS, $closedData);

    // Remova os lançamentos do dia do fluxo caixa atual
    $data = array_filter($data, function($entry) use ($today) {
        return strpos($entry['data_hora'], $today) !== 0;
    });

    writeJsonFile(JSON_FILE, array_values($data));

    echo json_encode(['status' => 'success', 'message' => 'Caixa fechado com sucesso!']);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idTransEdit'])) {
        editEntry();
    } elseif (isset($_POST['descricao'])) {
       
    } elseif (isset($_POST['close'])) {
        closeCash();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ação POST não reconhecida!']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['delete'])) {
        deleteEntry();
    } 
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitação não suportado!']);
}

?>
