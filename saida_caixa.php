
        <?php
        date_default_timezone_set('America/Sao_Paulo');

        define('JSON_FILE', 'fluxo_caixa.json');
        define('JSON_FILE_FECHADOS', 'fluxo_caixa_fechados.json');
        define('CLOSING_HOUR', 8);
        define('CLOSING_MINUTE', 45);

        include 'functions.php';

        // Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        addEntry();
    } elseif (isset($_POST['edit'])) {
        editEntry();
    } elseif (isset($_POST['close'])) {
        closeCash();
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['delete'])) {
    deleteEntry();
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['edit'])) {
    $editEntry = getEntryById($_GET['edit']);
}
function getEntryById($id) {
    $data = readJsonFile(JSON_FILE);
    foreach ($data as $entry) {
        if ($entry['id'] == $id) {
            return $entry;
        }
    }
    return null;
}

        $today = date('Y-m-d');
        $data = readJsonFile(JSON_FILE);
        $todayEntries = array_filter($data, function($entry) use ($today) {
            return strpos($entry['data_hora'], $today) === 0;
        });

        $editEntry = null;
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['edit'])) {
            $data = readJsonFile(JSON_FILE);
            foreach ($data as $entry) {
                if ($entry['id'] == $_GET['edit']) {
                    $editEntry = $entry;
                    break;
                }
            }
        }
$data = readJsonFile(JSON_FILE);
$totalValor = 0.0;

foreach ($data as $entry) {
    $valor = floatval(str_replace(['.', ','], ['', '.'], $entry['valor'])); // Converte o valor para float
    $totalValor += $valor; // Soma os valores como float
}

        ?>

<!DOCTYPE html>
<html>
<head>
    <title>Fluxo de Caixa</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="scripts.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
    <body>
        <div class="container_addlanc">
        <div class="cont_form">
        <?php if ($editEntry): ?>
        <h2>Editar Lançamento</h2>
            <form method="post" action="" id="editForm">
                    <div class="linha_form">
                    <div class="id_trans">
                        <label for="id_trans">Id Transação:</label>
                        <input type="text" id="id_trans" name="id_trans_edit" value="<?php echo htmlspecialchars($editEntry['id']); ?>" readonly>
                    </div>
                    <div class="descricao">
                        <label for="descricao">Descrição:</label>
                        <input type="text" id="descricao_edit" name="descricao_edit" value="<?php echo htmlspecialchars($editEntry['descricao']); ?>" required style="flex: 1;">
                    </div>
                </div>
                <div class="linha_form">
                    <div class="campo_valor">
                        <label for="valor">Valor:</label>
                        <input type="text" id="valor_edit" name="valor_edit" value="<?php echo htmlspecialchars($editEntry['valor']); ?>" required>
                    </div>
                    <div class="campo_f_pag">
                        <label for="forma_pagamento">Forma de Pagamento:</label>
            <select id="forma_pagamento_edit" name="forma_pagamento_edit" required>
                <option value="Pix" <?php if($editEntry['forma_pagamento'] == 'Pix') echo 'selected'; ?>>Pix</option>
                <option value="Dinheiro" <?php if($editEntry['forma_pagamento'] == 'Dinheiro') echo 'selected'; ?>>Dinheiro</option>
                <option value="Cartão de Crédito" <?php if($editEntry['forma_pagamento'] == 'Cartão de Crédito') echo 'selected'; ?>>Cartão de Crédito</option>
                <option value="Cartão de Débito" <?php if($editEntry['forma_pagamento'] == 'Cartão de Débito') echo 'selected'; ?>>Cartão de Débito</option>
                <option value="Fiado" <?php if($editEntry['forma_pagamento'] == 'Fiado') echo 'selected'; ?>>Fiado</option>
            </select>
                    </div>
                </div>
                <div class="linha_form">
            <div class="categoria">
                <label for="categoria_edit">Categoria:</label>
                <select id="categoria_edit" name="categoria_edit" required>
                    <option value="MERCADO" <?php if($editEntry['categoria'] == 'MERCADO') echo 'selected'; ?>>MERCADO</option>
                    <option value="COMPRAS PESSOAL" <?php if($editEntry['categoria'] == 'COMPRAS PESSOAL') echo 'selected'; ?>>COMPRAS PESSOAL</option>
                    <option value="DROGA" <?php if($editEntry['categoria'] == 'DROGA') echo 'selected'; ?>>DROGA</option>
                    <option value="FORNECEDOR"<?php if($editEntry['categoria'] == 'FORNECEDOR') echo 'selected'; ?>>FORNECEDOR</option>
                    <option value="LANCHONETE"<?php if($editEntry['categoria'] == 'LANCHONETE') echo 'selected'; ?>>LANCHONETE</option>
                    <option value="MOTO"<?php if($editEntry['categoria'] == 'MOTO') echo 'selected'; ?>>MOTO</option>
                    <option value="PADARIA"<?php if($editEntry['categoria'] == 'PADARIA') echo 'selected'; ?>>PADARIA</option>
                    <option value="AGENCIA"<?php if($editEntry['categoria'] == 'AGENCIA') echo 'selected'; ?>>AGENCIA</option>
                </select>
            </div>
            <div class="tipo_compra">
            <label for="tipo_compra_edit">Tipo de Compra:</label>
            <select id="tipo_compra_edit" data-selected="<?php echo htmlspecialchars($editEntry['tipo_compra']); ?>">
                <!-- As opções serão preenchidas pelo JavaScript -->
            </select>

                        </div>
                    </div>

                            <div class="linha_form">
                                <div class="comprador">
                                    <label for="comprador">Comprador:</label>
                                    <select id="comprador_edit" name="comprador_edit" value="<?php echo $editEntry['comprador']; ?>" required>
                                        <option value="CARLOS">CARLOS</option>
                                        <option value="KALINY">KALINY</option>
                                    </select>
                                </div>
                                <div class="user_cad">
                                    <label for="user_cad">Usuário:</label>
                                    <input type="text" id="user_cad" name="user_cad" value="Nome do usuário logado" readonly>

                                </div>
                            </div></div>
        <div class="botao">
            <button type="button" class="custom-button" name="edit" id="confirmEdit">Salvar Alterações</button>

        </div>
            </form>
        <?php else: ?>



                    <h2>Adicionar Lançamento</h2>
                    <div class="cont_form">
                        <form method="POST">
                            <div class="linha_form">
                                <div class="id_trans">
                                    <label for="id_trans">Id Transação:</label>
                                    <input type="text" id="id_trans" name="id_trans" value="<?php echo getNextId();?>" readonly>
                                </div>
                                <div class="descricao">
                                    <label for="descricao">Descrição:</label>
                                    <input type="text" id="descricao" name="descricao" required style="flex: 1;">
                                </div>
                            </div>
                            <div class="linha_form">
                                <div class="campo_valor">
                                    <label for="valor">Valor:</label>
                                    <input type="text" id="valor" name="valor" required>
                                </div>
                                <div class="campo_f_pag">
                                    <label for="forma_pagamento">Forma de Pagamento:</label>
                                    <select id="forma_pagamento" name="forma_pagamento" required>
                                        <option value="Pix">Pix</option>
                                        <option value="Dinheiro">Dinheiro</option>
                                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                                        <option value="Cartão de Débito">Cartão de Débito</option>
                                        <option value="Fiado">Fiado</option>
                                    </select>
                                </div>
                            </div>
                            <div class="linha_form">

                                <div class="categoria">
                                    <label for="categoria">Categoria:</label>
                                    <select id="categoria" name="categoria" required>
                                        <option value="MERCADO">MERCADO</option>
                                        <option value="COMPRAS PESSOAL">COMPRA PESSOAL</option>
                                        <option value="DROGA">DROGA</option>
                                        <option value="FORNECEDOR">FORNECEDOR</option>
                                        <option value="LANCHONETE">LANCHONETE</option>
                                        <option value="MOTO">MOTO</option>
                                        <option value="PADARIA">PADARIA</option>
                                        <option value="AGENCIA">AGENCIA</option>
                                    </select>
                                </div>
                                <div class="tipo_compra">
                                    <label for="tipo_compra">Tipo de Compra:</label>
                                    <select id="tipo_compra" name="tipo_compra" required>
                                        <!-- Subcategorias serão carregadas aqui -->
                                    </select>
                                </div>
                                </div>
                                        </div>
                                        <div class="linha_form">
                                            <div class="comprador">
                                                <label for="comprador">Comprador:</label>
                                                <select id="comprador" name="comprador" required>
                                                    <option value="CARLOS">CARLOS</option>
                                                    <option value="KALINY">KALINY</option>
                                                </select>
                                            </div>
                                            <div class="user_cad">
                                                <label for="user_cad">Usuário:</label>
                                                <input type="text" id="user_cad" name="user_cad" value="Nome do usuário logado" readonly>
                                            </div>
                                        </div>
                                        <div class="botao">
                                            <button type="submit" name="add">Adicionar</button>
                                        </div>
                        </form>
                         <?php endif; ?>
                                </div></div>



            <div class="lanc">
                <h2>Lançamentos do Dia</h2>
                <table border="1" class="lancamentos-table">
                    <tr>
                        <th class="col-id">ID</th>
                        <th class="col-data-hora">Data/Hora</th>
                        <th class="col-descricao">Descrição</th>
                        <th class="col-valor">Valor</th>
                        <th class="col-forma-pagamento">Forma de Pagamento</th>
                        <th class="col-categoria">Categoria</th>
                        <th class="col-tipo-compra">Tipo de Compra</th>
                        <th class="col-comprador">Comprador</th>
                        <th class="col-acoes">Ações</th>
                    </tr>
                    <?php
                    if (!empty($todayEntries)) {
                        foreach ($todayEntries as $entry) {
                            echo "<tr>
                                    <td>{$entry['id']}</td>
                                    <td>{$entry['data_hora']}</td>                        
                                    <td>{$entry['descricao']}</td>
                                    <td>{$entry['valor']}</td>
                                    <td>{$entry['forma_pagamento']}</td>
                                    <td>{$entry['categoria']}</td>
                                    <td>{$entry['tipo_compra']}</td>
                                    <td>{$entry['comprador']}</td>
                                    <td>
                                        <a href='?edit={$entry['id']}' class='action-link'><i class='fas fa-edit'></i> Editar</a> | 
                                        <a href='#' class='delete-link action-link' data-id='{$entry['id']}'><i class='fas fa-trash'></i> Excluir</a>
                                    </td>
                                </tr>";

                        }

                    } else {

                        echo "<tr><td colspan='9'>Nenhum lançamento encontrado</td></tr>";
                    }
                    ?>
                </table>
            <div class="resultado_dia">Valor Gasto Diario: R$  <?php echo number_format($totalValor, 2, ',', '.'); ?></div>
                <div class="cont_btn_fechar">
                    <form method="POST">
                        <div class="btn_fechar_cx"><button type="submit2" name="close" id="close_box">Fechar Caixa</button></div>
                    </form>
                </div>
            </div>

            <!-- Modal de Confirmação -->
            <div id="confirmModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Confirmação de Edição</h2>

                    <div class="botao_modal">
                        <p>Tem certeza que deseja salvar as alterações?</p>
                        <button type="button1" class="btnsim" id="confirmYes">Sim</button>
                        <button type="button2" class="cancel">Não</button>
                    </div>
                </div>
            </div>

                <!-- Modal de Exclusão -->
                <div id="deleteModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Confirmação de Exclusão</h2>

                        <div class="botao_modal2">
                            <p>Tem certeza que deseja excluir este item?</p>
                        <button type="button1" id="deleteYes">Sim</button>
                        <button type="button2" class="cancel">Não</button>
                        </div>
                    </div>
                </div>

                <!-- Novo Modal para mensagem de sucesso -->
                <div id="successModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2 id="titulosuccessMessage">Arquivo Editado</h2>
                        <p id="successMessage">Edição realizada com sucesso!</p>
                    </div>
                </div>

                <div id="successModal2" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h2>Arquivo excluido</h2>
                        <p id="successMessage">Lançamento Excluido com sucesso!</p>
                    </div>
                </div>


            <!-- Modal de Fechamento de caixa -->
            <div id="closeCashSuccessModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Fechamento de caixa</h2>

                    <div class="botao_modal2">
                        <p>Tem certeza que deseja feichar o caixa agora?</p>
                    <button type="button1" id="close_box_yes">Sim</button>
                    <button type="button2" class="cancel">Não</button>
                    </div>
                </div>
            </div>



            <!-- Modal de Confirmação de Fechamento de Caixa -->
            <div id="closeCashSuccessModal2" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Caixa Fechado</h2>
                    <p>O caixa foi fechado com sucesso!</p>
                    <div class="valor_modal"><p>Valor Gasto Diario: R$  <?php echo number_format($totalValor, 2, ',', '.'); ?></p></div>
                    <button id="addNewEntry" class="modal-button">Adicionar Novo Lançamento</button>
                </div>
            </div>



    </body>
    </html>
