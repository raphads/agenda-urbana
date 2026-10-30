<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";
date_default_timezone_set('America/Sao_Paulo');

$evento_id = $_GET['id'];
$id_user_logado = $_SESSION['usuario_id'];

// 1. Lógica para Favoritar / Desfavoritar (Alternar)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao_favorito'])) {
    $check_fav = $conecta_db->prepare("SELECT id_fav FROM tb_fav WHERE id_user = ? AND id_evento = ?");
    $check_fav->bind_param("ii", $id_user_logado, $evento_id);
    $check_fav->execute();
    $res_fav = $check_fav->get_result();

    if ($res_fav->num_rows > 0) {
        $del_fav = $conecta_db->prepare("DELETE FROM tb_fav WHERE id_user = ? AND id_evento = ?");
        $del_fav->bind_param("ii", $id_user_logado, $evento_id);
        $del_fav->execute();
    } else {
        $ins_fav = $conecta_db->prepare("INSERT INTO tb_fav (id_user, id_evento) VALUES (?, ?)");
        $ins_fav->bind_param("ii", $id_user_logado, $evento_id);
        $ins_fav->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $evento_id);
    exit;
}

// 2. CORREÇÃO: Verificar se o evento atual é favorito do usuário logado (Isso estava faltando!)
$is_fav_stmt = $conecta_db->prepare("SELECT id_fav FROM tb_fav WHERE id_user = ? AND id_evento = ?");
$is_fav_stmt->bind_param("ii", $id_user_logado, $evento_id);
$is_fav_stmt->execute();
$is_favorito = $is_fav_stmt->get_result()->num_rows > 0;

// Dados do evento
$stmt = $conecta_db->prepare("SELECT * FROM tb_evento WHERE id_evento = ?");
if (!$stmt) {
    die("Erro no prepare1: " . $conecta_db->error);
}
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$evento = $stmt->get_result()->fetch_assoc();

// Fotos
$stmt = $conecta_db->prepare("SELECT caminho FROM fotos_eventos WHERE id_evento = ?");
if (!$stmt) {
    die("Erro no prepare2: " . $conecta_db->error);
}
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$fotos = $stmt->get_result();

// Inserir comentário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['texto'])) {
    $texto = $_POST['texto'];
    $stmt = $conecta_db->prepare("INSERT INTO tb_comentarios (id_user, id_evento, texto) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $id_user_logado, $evento_id, $texto);
    $stmt->execute();
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $evento_id);
    exit;
}

// Buscar comentários
$stmt = $conecta_db->prepare("SELECT c.texto, c.data_comentario, u.name_user, u.email_user
                             FROM tb_comentarios c
                             JOIN tb_cadastro u ON c.id_user = u.id_user
                             WHERE c.id_evento = ?
                             ORDER BY c.data_comentario DESC");
$stmt->bind_param("i", $evento_id);
$stmt->execute();
$comentarios = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
    .checked {
      color: #f97316;
    }
    </style>
</head>
<header>
    <?php include 'header.php' ?>
</header>
<body>
 <div class="container-fluid">
<div class="row">
     <div class="col-md-2"></div>

<div class="col-md-8">
    <!-- Título do Evento + Botão de Favorito com Estrela -->
    <div class="d-flex align-items-center justify-content-between mt-3">
        <h1 class="mb-0"><?php echo htmlspecialchars($evento['nome_evento']); ?></h1>
        
        <form method="post" action="" class="mb-0">
            <input type="hidden" name="acao_favorito" value="1">
            <button type="submit" class="btn btn-link p-0" style="font-size: 2rem; text-decoration: none;" title="<?php echo $is_favorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos'; ?>">
                <?php if ($is_favorito) { ?>
                    <i class="fa fa-star checked"></i>
                <?php } else { ?>
                    <i class="fa fa-star-o text-secondary"></i>
                <?php } ?>
            </button>
        </form>
    </div>
    <hr>
     
<!-- Exibição do Endereço Automático via CEP -->
<p><strong>CEP:</strong> <span id="span-cep"><?php echo htmlspecialchars($evento['endereco']); ?></span></p>
<p><strong>Endereço:</strong> <span id="endereco-completo">Carregando endereço...</span><strong> Nº:</strong><?php echo $evento['num_evento']; ?></p>
<p><strong>Preço:</strong> De R$<?php echo $evento['preco_barato']; ?> até R$<?php echo $evento['preco_caro']; ?></p>

<p><strong>Horário:</strong> 
    <?php echo date("H:i", strtotime($evento['hora_abre'])); ?> - 
    <?php echo date("H:i", strtotime($evento['hora_fecha'])); ?>
</p>
<p><strong>Data:</strong> De 
    <?php echo date("d/m/Y", strtotime($evento['data_abre'])); ?> até 
    <?php echo date("d/m/Y", strtotime($evento['data_fecha'])); ?>
</p>
<p><strong>Observações:</strong> <?php echo nl2br(htmlspecialchars($evento['obs'])); ?></p>

<!-- Galeria de Fotos -->
<h2>Galeria de Fotos</h2>
<div id="carouselevento" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php 
    $active = true;
    while($f = $fotos->fetch_assoc()) { ?>
      <div class="carousel-item <?php if($active){echo 'active'; $active=false;} ?>">
        <img src="<?php echo $f['caminho']; ?>" class="d-block w-100" style="max-height:500px; object-fit:cover;">
      </div>
    <?php } ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselevento" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Anterior</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselevento" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Próximo</span>
  </button>
</div>
</div>
<div class="col-md-2"></div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
    
<!-- Formulário de Comentários -->
<h2>Deixe seu comentário</h2>
<form method="post" action="">
    <textarea name="texto" class="form-control" rows="3" required></textarea><br>
    <input type="submit" value="Enviar" id="botao" class="btn btn-primary">
</form>

<hr class="my-4">

<!-- Lista de Comentários -->
<h2>Comentários</h2>
<?php while($c = $comentarios->fetch_assoc()) { ?>
    <div class="card mb-2">
        <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($c['texto'])); ?></p>
            <small>
                Por <strong><?php echo htmlspecialchars($c['email_user']); ?></strong> 
                em <?php echo date("d/m/Y H:i", strtotime($c['data_comentario'])); ?>
            </small>
        </div>
    </div>
<?php } ?>

</div>
    <div class="col-md-2"></div>
</div>
</div>

<!-- Script para buscar o endereço via CEP automaticamente -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var cepElement = document.getElementById('span-cep');
    var enderecoElement = document.getElementById('endereco-completo');
    
    if (cepElement && enderecoElement) {
        var cep = cepElement.textContent.replace(/\D/g, ''); 
        
        if (cep.length === 8) {
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
                .then(response => response.json())
                .then(dados => {
                    if (!dados.erro) {
                        var enderecoFormatado = dados.logradouro + ", " + dados.bairro + " - " + dados.localidade + "/" + dados.uf;
                        enderecoElement.textContent = enderecoFormatado;
                    } else {
                        enderecoElement.textContent = "CEP não encontrado.";
                    }
                })
                .catch(error => {
                    enderecoElement.textContent = "Erro ao carregar o endereço.";
                });
        } else {
            enderecoElement.textContent = "CEP inválido.";
        }
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>