// Função para abrir o modal e definir o ID do livro corretamente
$('#modalEmprestarLivro').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Botão que abriu o modal
    var livroId = button.data('id'); // Recupera o ID do livro do botão
    var modal = $(this);

    // Atualiza o campo hidden com o ID do livro
    modal.find('#livro_id').val(livroId);
});
// Função para submeter o formulário de empréstimo
function emprestarLivro() {
    const usuarioId = document.getElementById('usuarioSelect').value;
    const livroId = document.getElementById('livro_id').value;

    if (usuarioId && livroId) {
        const form = document.getElementById('formEmprestar');
        form.action = `/emprestar/${livroId}/${usuarioId}`;
        form.submit();
    } else {
        alert('Por favor, selecione um usuário e verifique o ID do livro.');
    }
}
function emprestarLivrosession(idLivro) {
    // Obtém o ID do usuário da sessão (exemplo: `<?php echo $_SESSION['user_id']; ?>`)
    const idUsuario = document.getElementById('usuarioSession').value;
    if (idLivro) {
        const form = document.getElementById('formEmprestarsession');
        form.action = `/emprestar/${idLivro}/${idUsuario}`;
        form.submit();
    } else {
        alert('Por favor, selecione um usuário e verifique o ID do livro.');
    }

}
setTimeout(function() {
    var alert = document.getElementById("alertadevolucao");
    if (alert) {
        alert.classList.remove('show'); // Remove a classe 'show' para ocultar
        alert.classList.add('fade'); // Adiciona a classe 'fade' para animação
    }
}, 5000); // 5000 milissegundos (5 segundos)

