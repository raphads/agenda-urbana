<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";
date_default_timezone_set('America/Sao_Paulo');

$evento_id = $_GET['id'];
$usuario_sessao = $_SESSION['usuario'];

// Identifica o ID real do usuário logado buscando pelo e-mail ou nome na sessão
$stmt_busca_user = $conecta_db->prepare("SELECT id_user FROM tb_cadastro WHERE email_user = ? OR name_user = ?");
$stmt_busca_user->bind_param("ss", $usuario_sessao, $usuario_sessao);
$stmt_busca_user->execute();
$res_user = $stmt_busca_user->get_result();

if ($row_user = $res_user->fetch_assoc()) {
    $id_user_logado = $row_user['id_user'];
} else {
    $id_user_logado = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 0;
}

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

// 2. Verificar se o evento atual é favorito do usuário logado
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
    <title><?php echo htmlspecialchars($evento['nome_evento']); ?> - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            background: #ffffff;
        }
        .star-btn {
            background: none;
            border: none;
            padding: 0;
            font-size: 2.2rem;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .star-btn:hover {
            transform: scale(1.15);
        }
        .checked {
            color: #f97316 !important;
        }
        .info-label {
            font-weight: 600;
            color: #475569;
        }
        .btn-custom-primary {
            background-color: #17395e;
            border-color: #ee603c;
            color: #fff;
            border-radius: 40px;
            padding: 8px 24px;
            font-weight: 600;
        }
        .btn-custom-primary:hover {
            background-color: #102a48;
            border-color: #d94e2b;
            color: #fff;
        }
        .comment-card {
            border-left: 4px solid #17395e;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<header>
    <?php include 'header.php'; ?>
</header>

<div class="container my-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Card Principal do Evento -->
            <div class="card card-custom p-4 p-md-5 mb-4">
                
                <!-- Título + Botão Estrela -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h1 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($evento['nome_evento']); ?></h1>
                    
                    <form method="post" action="" class="mb-0">
                        <input type="hidden" name="acao_favorito" value="1">
                        <button type="submit" class="star-btn" title="<?php echo $is_favorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos'; ?>">
                            <?php if ($is_favorito) { ?>
                                <i class="fa fa-star checked"></i>
                            <?php } else { ?>
                                <i class="fa fa-star-o text-muted"></i>
                            <?php } ?>
                        </button>
                    </form>
                </div>
                
                <hr class="mb-4">
                 
                <!-- Detalhes do Evento -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <p class="mb-2"><i class="fa fa-map-marker text-danger me-2"></i> <span class="info-label">CEP:</span> <span id="span-cep"><?php echo htmlspecialchars($evento['endereco']); ?></span></p>
                        <p class="mb-2"><i class="fa fa-building text-secondary me-2"></i> <span class="info-label">Endereço:</span> <span id="endereco-completo">Carregando endereço...</span> <strong>Nº:</strong> <?php echo htmlspecialchars($evento['num_evento']); ?></p>
                        <p class="mb-2"><i class="fa fa-tag text-success me-2"></i> <span class="info-label">Preço:</span> De R$<?php echo number_format($evento['preco_barato'], 2, ',', '.'); ?> até R$<?php echo number_format($evento['preco_caro'], 2, ',', '.'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2"><i class="fa fa-clock-o text-warning me-2"></i> <span class="info-label">Horário:</span> 
                            <?php echo date("H:i", strtotime($evento['hora_abre'])); ?> - 
                            <?php echo date("H:i", strtotime($evento['hora_fecha'])); ?>
                        </p>
                        <p class="mb-2"><i class="fa fa-calendar text-primary me-2"></i> <span class="info-label">Data:</span> De 
                            <?php echo date("d/m/Y", strtotime($evento['data_abre'])); ?> até 
                            <?php echo date("d/m/Y", strtotime($evento['data_fecha'])); ?>
                        </p>
                    </div>
                </div>

                <?php if (!empty($evento['obs'])) { ?>
                    <div class="alert alert-light border rounded mb-4">
                        <strong class="info-label"><i class="fa fa-info-circle me-1"></i> Observações:</strong><br>
                        <?php echo nl2br(htmlspecialchars($evento['obs'])); ?>
                    </div>
                <?php } ?>

                <!-- Galeria de Fotos -->
                <h3 class="h4 fw-bold text-dark mb-3"><i class="fa fa-picture-o text-primary me-2"></i> Galeria de Fotos</h3>
                <div id="carouselevento" class="carousel slide rounded overflow-hidden shadow-sm mb-4" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    <?php 
                    $active = true;
                    while($f = $fotos->fetch_assoc()) { ?>
                      <div class="carousel-item <?php if($active){echo 'active'; $active=false;} ?>">
                        <img src="<?php echo htmlspecialchars($f['caminho']); ?>" class="d-block w-100" style="max-height:480px; object-fit:cover;">
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

            <!-- Card de Comentários -->
            <div class="card card-custom p-4 p-md-5">
                
                <h3 class="h4 fw-bold text-dark mb-3"><i class="fa fa-commenting-o text-primary me-2"></i> Deixe seu comentário</h3>
                <form method="post" action="" class="mb-4">
                    <textarea name="texto" class="form-control mb-3" rows="3" placeholder="Escreva aqui seu comentário sobre o evento..." required></textarea>
                    <button type="submit" id="botao" class="btn btn-custom-primary">Enviar Comentário</button>
                </form>

                <hr class="my-4">

                <h3 class="h4 fw-bold text-dark mb-4"><i class="fa fa-comments text-secondary me-2"></i> Comentários</h3>
                <?php if ($comentarios->num_rows > 0) { ?>
                    <?php while($c = $comentarios->fetch_assoc()) { ?>
                        <div class="card comment-card mb-3 bg-light border-0 shadow-sm">
                            <div class="card-body">
                                <p class="card-text mb-2"><?php echo nl2br(htmlspecialchars($c['texto'])); ?></p>
                                <small class="text-muted">
                                    <i class="fa fa-user me-1"></i> Por <strong><?php echo htmlspecialchars($c['name_user'] ? $c['name_user'] : $c['email_user']); ?></strong> 
                                    em <?php echo date("d/m/Y H:i", strtotime($c['data_comentario'])); ?>
                                </small>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-muted fst-italic">Nenhum comentário enviado ainda. Seja o primeiro a comentar!</p>
                <?php } ?>

            </div>

        </div>
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