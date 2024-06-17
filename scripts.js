$(document).ready(function() {
    var confirmModal = $('#confirmModal');
    var deleteModal = $('#deleteModal'); // Novo modal para exclusão
    var successModal = $('#successModal');
    var successModal2 = $('#successModal2'); // Novo modal para sucesso de exclusão
    var closeCashSuccessModal = $('#closeCashSuccessModal');
    var closeCashSuccessModal2 = $('#closeCashSuccessModal2');
    var modalOverlay = $('#modalOverlay');
    var confirmYes = $('#confirmYes');
    var deleteYes = $('#deleteYes'); // Botão de confirmação de exclusão
    var close_box_yes = $('#close_box_yes'); // 

    // Função para exibir o modal de edição
    $('#confirmEdit').click(function() {
        var editData = {
            idTransEdit: $('#id_trans').val(),
            descricaoEdit: $('#descricao_edit').val(),
            valorEdit: $('#valor_edit').val(),
            formaPagamentoEdit: $('#forma_pagamento_edit').val(),
            tipoCompraEdit: $('#tipo_compra_edit').val(),
            categoriaEdit: $('#categoria_edit').val(),
            compradorEdit: $('#comprador_edit').val()
        };
        confirmModal.show();
        modalOverlay.show(); // Mostrar overlay
    });
    $('.close, .cancel').click(function() {
        closeCashSuccessModal2.hide();
            window.location.href = 'saida_caixa.php'; // Redireciona para a página de fluxo de caixa após sucesso
        });
    // Fechar o modal e redirecionar ao clicar no botão
    $('.close, .cancel').click(function() {
        confirmModal.hide();
        deleteModal.hide();
        successModal.hide();
        successModal2.hide();
        closeCashSuccessModal.hide();
        modalOverlay.hide(); // Esconder overlay
    });

    // Função para fechar o modal de sucesso
    $('.closeSuccess').click(function() {
        successModal.hide();
        successModal2.hide();
        closeCashSuccessModal.hide();
        closeCashSuccessModal2.hide();
        modalOverlay.hide(); // Esconder overlay
    });

    // Função para enviar os dados do formulário após confirmação de edição
    confirmYes.click(function() {
        $.ajax({
            url: 'functions.php',
            method: 'POST',
            data: {
                idTransEdit: $('#id_trans').val(),
                descricaoEdit: $('#descricao_edit').val(),
                valorEdit: $('#valor_edit').val(),
                formaPagamentoEdit: $('#forma_pagamento_edit').val(),
                tipoCompraEdit: $('#tipo_compra_edit').val(),
                categoriaEdit: $('#categoria_edit').val(),
                compradorEdit: $('#comprador_edit').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    confirmModal.hide();
                    successModal.show();
                    $('#successMessage').text('Edição realizada com sucesso!');
                    setTimeout(function() {
                        window.location.href = 'saida_caixa.php';
                    }, 1000);
                } else {
                    confirmModal.hide();
                    successModal.show();
                    $('#successMessage').text(response.message);
                }
                modalOverlay.hide(); // Esconder overlay
            },
            error: function(xhr, status, error) {
                confirmModal.hide();
                successModal.show();
                console.error('Erro na solicitação:', error);
                $('#successMessage').text('Erro na solicitação: ' + error);
                modalOverlay.hide(); // Esconder overlay
            }
        });
    });

    // Função para enviar os dados do formulário após confirmação de exclusão
    deleteYes.click(function() {
        var deleteData = { delete: deleteModal.data('id') };

        $.ajax({
            url: 'functions.php',
            method: 'GET',
            data: deleteData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    deleteModal.hide();
                    successModal2.show();
                    $('#titulosuccessMessage').text('Arquivo Excluido');
                    $('#successMessage').text('Lançamento Excluido com sucesso!');
                    setTimeout(function() {
                        window.location.href = 'saida_caixa.php'; // Redireciona para a página de fluxo de caixa após sucesso
                    }, 1000);
                } else {
                    deleteModal.hide();
                    successModal2.show();
                    $('#successMessage').text('Exclusão não realizada: ' + response.message); // Ajustado aqui
                }
                modalOverlay.hide(); // Esconder overlay
            },
            error: function(xhr, status, error) {
                deleteModal.hide();
                successModal2.show();
                console.error('Erro na solicitação:', error);
                $('#successMessage').text('Erro na solicitação: ' + error);
                modalOverlay.hide(); // Esconder overlay
            }
        });
    });

    // Função para exibir o modal de exclusão
    $('.delete-link').click(function(event) {
        event.preventDefault(); // Evita o comportamento padrão do link
        let entryId = $(this).data('id'); // Pega o ID do lançamento
        deleteModal.data('id', entryId).show(); // Armazena o ID do lançamento no modal e exibe
        modalOverlay.show(); // Mostrar overlay
    });

    // Função para calcular o total
    function calculateTotal() {
        var totalValor = 0.0;

        // Percorrer todas as células de valor e somar os valores
        $('td.valor').each(function() {
            // Obtém o texto do valor, substitui o ponto por nada e a vírgula por ponto
            var valorText = $(this).text().replace('.', '').replace(',', '.');
            var valor = parseFloat(valorText); // Converte para número float
            if (!isNaN(valor)) {
                totalValor += valor; // Soma os valores
            }
        });

        // Exibir o total
        $('#totalValor').text('Total de Valor: R$ ' + totalValor.toFixed(2).replace('.', ','));
    }

    // Chamar a função ao carregar a página
    calculateTotal();

    // Chamar a função após adicionar ou editar um lançamento (exemplo de uso com AJAX)
    // Adapte conforme necessário para o seu código de AJAX
    $('#confirmYes').click(function() {
        // Após a operação de edição ser bem-sucedida
        calculateTotal();
    });

    $('#deleteYes').click(function() {
        // Após a operação de exclusão ser bem-sucedida
        calculateTotal();
    });
    $('.btn_fechar_cx button').click(function(event) {
        event.preventDefault(); // Evita o comportamento padrão do botão
         closeCashSuccessModal.show();
         });
    // Função para fechar o caixa e exibir o modal de sucesso
    $('#close_box_yes').click(function(event) {
        event.preventDefault(); // Evita o comportamento padrão do botão
        $.ajax({
            url: 'functions.php',
            method: 'POST',
            data: { close: true },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    closeCashSuccessModal.hide();
                    closeCashSuccessModal2.show();
                    modalOverlay.show(); // Mostrar overlay
                  
                } else {
                    console.error('Erro ao fechar o caixa:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na solicitação:', error);
            }
        });
    });

    // Evento para o botão de adicionar novo lançamento
    $('#addNewEntry').click(function() {
        setTimeout(function() {
            window.location.href = 'saida_caixa.php';
        });
        modalOverlay.hide(); // Esconder overlay
    });

    // Preencher subcategorias de acordo com a categoria selecionada
    const subcategories = {
        "MERCADO": ["Alimentação Básica", "Limpeza", "Higiene Pessoal"],
        "COMPRAS PESSOAL": ["Vestuário", "Acessórios", "Produtos de Beleza", "Higiene"],
        "DROGA": ["Medicamentos", "Suplementos", "Produtos de Saúde"],
        "FORNECEDOR": ["Matéria-Prima", "Tecido", "Matéria Kit", "Equipamentos", "Impressão", "Prensa", "Suprimentos", "Papel", "Tinta"],
        "LANCHONETE": ["Alimentação Rápida", "Bebidas", "Snacks"],
        "MOTO": ["Combustível", "Manutenção", "Acessórios"],
        "PADARIA": ["Pães", "Bolos", "Doces"],
        "AGENCIA": ["Serviços", "Honorários"]
    };

    $('#categoria').on('change', function() {
        var categoriaSelecionada = $(this).val();
        var tipoCompra = $('#tipo_compra');

        tipoCompra.empty();

        if (subcategories.hasOwnProperty(categoriaSelecionada)) {
            subcategories[categoriaSelecionada].forEach(function(subcategoria) {
                tipoCompra.append(new Option(subcategoria, subcategoria));
            });
        }
    });

    $('#categoria_edit').on('change', function() {
        var categoriaSelecionada = $(this).val();
        var tipoCompraEdit = $('#tipo_compra_edit');

        tipoCompraEdit.empty();

        if (subcategories.hasOwnProperty(categoriaSelecionada)) {
            subcategories[categoriaSelecionada].forEach(function(subcategoria) {
                tipoCompraEdit.append(new Option(subcategoria, subcategoria));
            });
        }
    });

    // Trigger the change event to populate subcategories on page load
    $('#categoria').trigger('change');
    $('#categoria_edit').trigger('change');
});
