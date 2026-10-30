<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";

date_default_timezone_set('America/Sao_Paulo');


$id_user_logado = $_SESSION['usuario_id'];

// Lógica para remover dos favoritos caso o usuário clique na lixeira
if (isset($_GET['remover'])) {
    $id_fav_remover = $_GET['remover'];
    $stmt_del = $conecta_db->prepare("DELETE FROM tb_fav WHERE id_fav = ? AND id_user = ?");
    $stmt_del->bind_param("ii", $id_fav_remover, $id_user_logado);
    $stmt_del->execute();
    
    header("Location: favoritos.php");
    exit;
}

// Buscar eventos favoritos do usuário logado
$stmt = $conecta_db->prepare("
    SELECT f.id_fav, e.id_evento, e.nome_evento, e.data_abre, e.data_fecha, e.endereco, e.preco_barato, e.preco_caro
    FROM tb_fav f
    JOIN tb_evento e ON f.id_evento = e.id_evento
    WHERE f.id_user = ?
");
$stmt->bind_param("i", $id_user_logado);
$stmt->execute();
$favoritos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Favoritos - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<header>
    <?php include 'header.php'; ?>
</header>

<div class="container my-4">
    <h2>Meus Eventos Favoritos</h2>
    <hr>

    <?php if ($favoritos->num_rows > 0) { ?>
        <div class="row">
            <?php while ($fav = $favoritos->fetch_assoc()) { ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($fav['nome_evento']); ?></h5>
                            <p class="card-text mb-1"><strong>Data:</strong> <?php echo date("d/m/Y", strtotime($fav['data_abre'])); ?> - <?php echo date("d/m/Y", strtotime($fav['data_fecha'])); ?></p>
                            <p class="card-text mb-1"><strong>Preço:</strong> R$<?php echo $fav['preco_barato']; ?> - R$<?php echo $fav['preco_caro']; ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <!-- Ajuste o nome do arquivo abaixo caso sua página de detalhes tenha outro nome (ex: evento.php) -->
                                <a href="evento.php?id=<?php echo $fav['id_evento']; ?>" class="btn btn-primary btn-sm" style="background-color: #17395e; border-color: #ee603c;">Ver Detalhes</a>
                                
                                <a href="favoritos.php?remover=<?php echo $fav['id_fav']; ?>" class="text-danger" title="Remover dos favoritos" onclick="return confirm('Deseja remover este evento dos favoritos?');">
                                    <i class="fa fa-trash" style="font-size: 1.3rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-info" role="alert">
            Você ainda não adicionou nenhum evento aos seus favoritos.
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>