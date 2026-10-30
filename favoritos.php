<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";
date_default_timezone_set('America/Sao_Paulo');

$id_user_logado = $_SESSION['usuario_id'];

// Busca o nome real do usuário logado no banco de forma segura
$stmt_nome = $conecta_db->prepare("SELECT name_user FROM tb_cadastro WHERE id_user = ?");
$stmt_nome->bind_param("i", $id_user_logado);
$stmt_nome->execute();
$res_nome = $stmt_nome->get_result();

if ($row = $res_nome->fetch_assoc()) {
    $nome_usuario_real = $row['name_user'];
} else {
    $nome_usuario_real = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuário';
}

// Lógica para remover dos favoritos
if (isset($_GET['remover'])) {
    $id_fav_remover = $_GET['remover'];
    $stmt_del = $conecta_db->prepare("DELETE FROM tb_fav WHERE id_fav = ? AND id_user = ?");
    $stmt_del->bind_param("ii", $id_fav_remover, $id_user_logado);
    $stmt_del->execute();
    
    header("Location: favoritos.php");
    exit;
}

// Buscar eventos favoritos do usuário logado (incluindo horários e endereço)
$stmt = $conecta_db->prepare("
    SELECT f.id_fav, e.id_evento, e.nome_evento, e.data_abre, e.data_fecha, e.hora_abre, e.hora_fecha, e.endereco, e.preco_barato, e.preco_caro
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
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
        }
        .page-header {
            background: linear-gradient(135deg, #17395e, #102a48);
            color: white;
            padding: 25px 0;
            border-bottom: 4px solid #ee603c;
            margin-bottom: 30px;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .btn-custom-primary {
            background-color: #17395e;
            border-color: #17395e;
            color: #fff;
        }
        .btn-custom-primary:hover {
            background-color: #102a48;
            border-color: #102a48;
            color: #fff;
        }
        .btn-custom-accent {
            background-color: #ee603c;
            border-color: #ee603c;
            color: #fff;
        }
        .btn-custom-accent:hover {
            background-color: #d94e2b;
            border-color: #d94e2b;
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <?php include 'header.php'; ?>
</header>

<!-- Cabeçalho Visual da Página -->
<div class="page-header shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0"><i class="fa fa-star text-warning"></i> Meus Eventos Favoritos</h1>
            <p class="text-white-50 mb-0">Acompanhe e gerencie os eventos que você salvou</p>
        </div>
        <div>
            <span class="badge bg-light text-dark p-2 px-3 rounded-pill">
                <i class="fa fa-user-circle text-primary"></i> Olá, <?php echo htmlspecialchars($nome_usuario_real); ?>
            </span>
        </div>
    </div>
</div>

<div class="container my-4">
    <?php if ($favoritos->num_rows > 0) { ?>
        <div class="row">
            <?php while ($fav = $favoritos->fetch_assoc()) { 
                // Formatando dados para o link do Google Agenda
                $g_titulo = urlencode($fav['nome_evento']);
                $g_inicio = date("Ymd\THis", strtotime($fav['data_abre'] . ' ' . $fav['hora_abre']));
                $g_fim = date("Ymd\THis", strtotime($fav['data_fecha'] . ' ' . $fav['hora_fecha']));
                $g_local = urlencode($fav['endereco']);
                
                $url_google_agenda = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$g_titulo}&dates={$g_inicio}/{$g_fim}&location={$g_local}";
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card card-custom h-100 bg-white">
                        <div class="card-body d-flex flex-column justify-content-between p-4">
                            <div>
                                <h5 class="card-title text-dark fw-bold mb-3"><?php echo htmlspecialchars($fav['nome_evento']); ?></h5>
                                <p class="card-text mb-2 text-muted small"><i class="fa fa-calendar text-primary me-2"></i><strong>Data:</strong> <?php echo date("d/m/Y", strtotime($fav['data_abre'])); ?> até <?php echo date("d/m/Y", strtotime($fav['data_fecha'])); ?></p>
                                <p class="card-text mb-2 text-muted small"><i class="fa fa-clock-o text-primary me-2"></i><strong>Horário:</strong> <?php echo date("H:i", strtotime($fav['hora_abre'])); ?> - <?php echo date("H:i", strtotime($fav['hora_fecha'])); ?></p>
                                <p class="card-text mb-3 text-muted small"><i class="fa fa-tag text-primary me-2"></i><strong>Preço:</strong> R$<?php echo number_format($fav['preco_barato'], 2, ',', '.'); ?> - R$<?php echo number_format($fav['preco_caro'], 2, ',', '.'); ?></p>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                                <a href="evento.php?id=<?php echo $fav['id_evento']; ?>" class="btn btn-sm btn-custom-primary">
                                    <i class="fa fa-eye"></i> Detalhes
                                </a>
                                
                                <!-- Botão do Google Agenda -->
                                <a href="<?php echo $url_google_agenda; ?>" target="_blank" class="btn btn-sm btn-custom-accent" title="Adicionar ao Google Agenda">
                                    <i class="fa fa-calendar-plus-o"></i> Agenda
                                </a>

                                <a href="favoritos.php?remover=<?php echo $fav['id_fav']; ?>" class="btn btn-sm btn-outline-danger" title="Remover dos favoritos" onclick="return confirm('Deseja remover este evento dos favoritos?');">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <div class="alert alert-warning text-center p-4 card-custom" role="alert">
            <i class="fa fa-info-circle fa-2x mb-2 text-warning"></i>
            <h4 class="alert-heading">Nenhum favorito encontrado!</h4>
            <p class="mb-0">Você ainda não adicionou nenhum evento aos seus favoritos. Navegue pelos eventos e clique na estrela para salvá-los aqui.</p>
        </div>
    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>