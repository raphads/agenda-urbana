<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";
date_default_timezone_set('America/Sao_Paulo');

// Certifique-se de que a sessão possui o ID do usuário logado
$id_user_logado = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : 1; 

// Busca o nome real do usuário logado no banco de forma segura
$stmt_nome = $conecta_db->prepare("SELECT name_user FROM tb_cadastro WHERE id_user = ?");
$stmt_nome->bind_param("i", $id_user_logado);
$stmt_nome->execute();
$res_nome = $stmt_nome->get_result();

if ($row = $res_nome->fetch_assoc()) {
    $nome_usuario_real = $row['name_user'];
} else {
    $nome_usuario_real = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Administrador';
}

// 1. PROCESSAMENTO DE AÇÕES VIA POST (Edição e Exclusão)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    
    // --- USUÁRIOS ---
    if ($_POST['acao'] === 'excluir_usuario') {
        $id = $_POST['id_user'];
        $stmt = $conecta_db->prepare("DELETE FROM tb_cadastro WHERE id_user = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    }
    
    if ($_POST['acao'] === 'editar_usuario') {
        $id = $_POST['id_user'];
        $nome = $_POST['name_user'];
        $email = $_POST['email_user'];
        $empresa = isset($_POST['empresa']) ? 1 : 0;

        $stmt = $conecta_db->prepare("UPDATE tb_cadastro SET name_user = ?, email_user = ?, empresa = ? WHERE id_user = ?");
        $stmt->bind_param("ssii", $nome, $email, $empresa, $id);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    }

    // --- EVENTOS ---
    if ($_POST['acao'] === 'excluir_evento') {
        $id = $_POST['id_evento'];
        
        // Remove fotos vinculadas primeiro
        $stmtFoto = $conecta_db->prepare("DELETE FROM fotos_eventos WHERE id_evento = ?");
        $stmtFoto->bind_param("i", $id);
        $stmtFoto->execute();

        // Remove comentários vinculados
        $stmtCom = $conecta_db->prepare("DELETE FROM tb_comentarios WHERE id_evento = ?");
        $stmtCom->bind_param("i", $id);
        $stmtCom->execute();

        // Remove do evento
        $stmt = $conecta_db->prepare("DELETE FROM tb_evento WHERE id_evento = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        header("Location: admin.php");
        exit;
    }

    if ($_POST['acao'] === 'editar_evento') {
        $id = $_POST['id_evento'];
        $nome = $_POST['nome_evento'];
        $cep = $_POST['endereco'];
        $preco_b = $_POST['preco_barato'];
        $preco_c = $_POST['preco_caro'];
        $data_a = $_POST['dataa']; 
        $data_b = $_POST['datab']; 

        $stmt = $conecta_db->prepare("UPDATE tb_evento SET nome_evento = ?, endereco = ?, preco_barato = ?, preco_caro = ?, data_abre = ?, data_fecha = ? WHERE id_evento = ?");
        $stmt->bind_param("ssddssi", $nome, $cep, $preco_b, $preco_c, $data_a, $data_b, $id);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    }
}

// 2. BUSCA DE DADOS PARA AS TABELAS
$usuarios = $conecta_db->query("SELECT * FROM tb_cadastro ORDER BY id_user DESC");
$eventos = $conecta_db->query("SELECT * FROM tb_evento ORDER BY id_evento DESC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    
    <link rel="stylesheet" href="style.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
        }
        .admin-header {
            background: linear-gradient(135deg, #17395e, #102a48);
            color: white;
            padding: 25px 0;
            border-bottom: 4px solid #ee603c;
            margin-bottom: 30px;
        }
        .nav-tabs .nav-link {
            color: #17395e;
            font-weight: 600;
            border: none;
            padding: 12px 25px;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
        }
        .nav-tabs .nav-link.active {
            color: #ee603c !important;
            background-color: #ffffff;
            border-bottom: 3px solid #ee603c;
        }
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
        table.dataTable {
            border-collapse: collapse !important;
        }
    </style>
</head>
<body>

<header>
    <?php include 'header.php'; ?>
</header>

<!-- Cabeçalho Visual da Página Admin -->
<div class="admin-header shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0"><i class="fa fa-cogs"></i> Painel de Administração</h1>
            <p class="text-white-50 mb-0">Gerencie os usuários e eventos cadastrados na plataforma</p>
        </div>
        <div>
            <span class="badge bg-light text-dark p-2 px-3 rounded-pill">
                <i class="fa fa-user-circle text-primary"></i> Olá, <?php echo htmlspecialchars($nome_usuario_real); ?>
            </span>
        </div>
    </div>
</div>

<div class="container my-4">
    <!-- Abas / Navs -->
    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                <i class="fa fa-users me-2"></i> Usuários
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">
                <i class="fa fa-calendar me-2"></i> Eventos
            </button>
        </li>
    </ul>

    <div class="card card-custom p-4 bg-white rounded-bottom-4 rounded-end-4 border-top-0">
        <div class="tab-content" id="adminTabsContent">
            
            <!-- TABELA DE USUÁRIOS -->
            <div class="tab-pane fade show active" id="users" role="tabpanel">
                <div class="table-responsive">
                    <table id="tabelaUsuarios" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($u = $usuarios->fetch_assoc()) { ?>
                                <tr>
                                    <td><strong>#<?php echo $u['id_user']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($u['name_user']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email_user']); ?></td>
                                    <td>
                                        <?php echo (isset($u['empresa']) && $u['empresa'] == 1) ? '<span class="badge bg-warning text-dark px-2 py-1">Empresa</span>' : '<span class="badge bg-secondary px-2 py-1">Comum</span>'; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-custom-primary edit-user-btn me-1" 
                                                data-id="<?php echo $u['id_user']; ?>" 
                                                data-nome="<?php echo htmlspecialchars($u['name_user']); ?>" 
                                                data-email="<?php echo htmlspecialchars($u['email_user']); ?>"
                                                data-empresa="<?php echo isset($u['empresa']) ? $u['empresa'] : 0; ?>"
                                                title="Editar Usuário">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        
                                        <form method="post" action="" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            <input type="hidden" name="acao" value="excluir_usuario">
                                            <input type="hidden" name="id_user" value="<?php echo $u['id_user']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir Usuário"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABELA DE EVENTOS -->
            <div class="tab-pane fade" id="events" role="tabpanel">
                <div class="table-responsive">
                    <table id="tabelaEventos" class="table table-hover align-middle w-100">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CEP</th>
                                <th>Preços</th>
                                <th>Datas</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($e = $eventos->fetch_assoc()) { ?>
                                <tr>
                                    <td><strong>#<?php echo $e['id_evento']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($e['nome_evento']); ?></td>
                                    <td><?php echo htmlspecialchars($e['endereco']); ?></td>
                                    <td>R$<?php echo number_format($e['preco_barato'], 2, ',', '.'); ?> - R$<?php echo number_format($e['preco_caro'], 2, ',', '.'); ?></td>
                                    <td><?php echo date("d/m/Y", strtotime($e['data_abre'])); ?> até <?php echo date("d/m/Y", strtotime($e['data_fecha'])); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-custom-primary edit-event-btn me-1" 
                                                data-id="<?php echo $e['id_evento']; ?>" 
                                                data-nome="<?php echo htmlspecialchars($e['nome_evento']); ?>" 
                                                data-cep="<?php echo htmlspecialchars($e['endereco']); ?>"
                                                data-precob="<?php echo $e['preco_barato']; ?>"
                                                data-precoc="<?php echo $e['preco_caro']; ?>"
                                                data-dataa="<?php echo $e['data_abre']; ?>"
                                                data-datab="<?php echo $e['data_fecha']; ?>"
                                                title="Editar Evento">
                                            <i class="fa fa-pencil"></i>
                                        </button>

                                        <form method="post" action="" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                                            <input type="hidden" name="acao" value="excluir_evento">
                                            <input type="hidden" name="id_evento" value="<?php echo $e['id_evento']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir Evento"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL EDICAO DE USUARIO -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-custom">
      <form method="post" action="">
          <input type="hidden" name="acao" value="editar_usuario">
          <input type="hidden" name="id_user" id="edit-user-id">
          <div class="modal-header bg-light">
            <h5 class="modal-title"><i class="fa fa-user-edit text-primary"></i> Editar Usuário</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Nome</label>
                <input type="text" name="name_user" id="edit-user-nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email_user" id="edit-user-email" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="empresa" id="edit-user-empresa" class="form-check-input" value="1">
                <label class="form-check-label fw-bold" for="edit-user-empresa">É Empresa?</label>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-custom-accent">Salvar Alterações</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDICAO DE EVENTO -->
<div class="modal fade" id="modalEditEvent" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content card-custom">
      <form method="post" action="">
          <input type="hidden" name="acao" value="editar_evento">
          <input type="hidden" name="id_evento" id="edit-event-id">
          <div class="modal-header bg-light">
            <h5 class="modal-title"><i class="fa fa-calendar-check-o text-primary"></i> Editar Evento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold">Nome do Evento</label>
                <input type="text" name="nome_evento" id="edit-event-nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">CEP</label>
                <input type="text" name="endereco" id="edit-event-cep" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Preço Mínimo (R$)</label>
                    <input type="number" step="0.01" min="0" name="preco_barato" id="edit-event-precob" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Preço Máximo (R$)</label>
                    <input type="number" step="0.01" min="0" name="preco_caro" id="edit-event-precoc" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Data Abertura</label>
                    <input type="date" name="dataa" id="edit-event-dataa" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Data Encerramento</label>
                    <input type="date" name="datab" id="edit-event-datab" class="form-control" required>
                </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-custom-accent">Salvar Alterações</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- JScripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var configDataTables = {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    };
    
    $('#tabelaUsuarios').DataTable(configDataTables);
    $('#tabelaEventos').DataTable(configDataTables);

    // Abrir Modal de Edição de Usuário
    $('.edit-user-btn').click(function() {
        $('#edit-user-id').val($(this).data('id'));
        $('#edit-user-nome').val($(this).data('nome'));
        $('#edit-user-email').val($(this).data('email'));
        $('#edit-user-empresa').prop('checked', $(this).data('empresa') == 1);
        
        var modalUser = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modalUser.show();
    });

    // Abrir Modal de Edição de Evento
    $('.edit-event-btn').click(function() {
        $('#edit-event-id').val($(this).data('id'));
        $('#edit-event-nome').val($(this).data('nome'));
        $('#edit-event-cep').val($(this).data('cep'));
        $('#edit-event-precob').val($(this).data('precob'));
        $('#edit-event-precoc').val($(this).data('precoc'));
        $('#edit-event-dataa').val($(this).data('dataa'));
        $('#edit-event-datab').val($(this).data('datab'));
        
        var modalEvent = new bootstrap.Modal(document.getElementById('modalEditEvent'));
        modalEvent.show();
    });
});
</script>
</body>
</html>