<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";
date_default_timezone_set('America/Sao_Paulo');

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

        $stmt = $conecta_db->prepare("UPDATE tb_evento SET nome_evento = ?, endereco = ?, preco_barato = ?, preco_caro = ? WHERE id_evento = ?");
        $stmt->bind_param("ssddi", $nome, $cep, $preco_b, $preco_c, $id);
        $stmt->execute();
        header("Location: admin.php");
        exit;
    }
}

// 2. BUSCA DE DADOS DADOS PARA AS TABELAS
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
    <script src="agenda.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>

<header>
    <?php include 'header.php'; ?>
</header>

<div class="container my-4">
    <h2 class="mb-4"><i class="fa fa-cogs"></i> Painel de Administração</h2>

    <!-- Aba / Navs -->
    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                <i class="fa fa-users"></i> Usuários
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">
                <i class="fa fa-calendar"></i> Eventos
            </button>
        </li>
    </ul>

    <div class="tab-content p-3 border border-top-0 rounded-bottom bg-white" id="adminTabsContent">
        
        <!-- TABELA DE USUÁRIOS -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">
            <div class="table-responsive">
                <table id="tabelaUsuarios" class="table table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $usuarios->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $u['id_user']; ?></td>
                                <td><?php echo htmlspecialchars($u['name_user']); ?></td>
                                <td><?php echo htmlspecialchars($u['email_user']); ?></td>
                                <td>
                                    <?php echo (isset($u['empresa']) && $u['empresa'] == 1) ? '<span class="badge bg-warning text-dark">Empresa</span>' : '<span class="badge bg-secondary">Comum</span>'; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-user-btn" 
                                            data-id="<?php echo $u['id_user']; ?>" 
                                            data-nome="<?php echo htmlspecialchars($u['name_user']); ?>" 
                                            data-email="<?php echo htmlspecialchars($u['email_user']); ?>"
                                            data-empresa="<?php echo isset($u['empresa']) ? $u['empresa'] : 0; ?>">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    
                                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                        <input type="hidden" name="acao" value="excluir_usuario">
                                        <input type="hidden" name="id_user" value="<?php echo $u['id_user']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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
                <table id="tabelaEventos" class="table table-striped table-hover w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CEP</th>
                            <th>Preços</th>
                            <th>Data Abertura</th>
                            <th>Data Encerramento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($e = $eventos->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $e['id_evento']; ?></td>
                                <td><?php echo htmlspecialchars($e['nome_evento']); ?></td>
                                <td><?php echo htmlspecialchars($e['endereco']); ?></td>
                                <td>R$<?php echo $e['preco_barato']; ?> - R$<?php echo $e['preco_caro']; ?></td>
                                <td><?php echo date("d/m/Y", strtotime($e['data_abre'])); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($e['data_fecha'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-event-btn" 
                                            data-id="<?php echo $e['id_evento']; ?>" 
                                            data-nome="<?php echo htmlspecialchars($e['nome_evento']); ?>" 
                                            data-cep="<?php echo htmlspecialchars($e['endereco']); ?>"
                                            data-precob="<?php echo $e['preco_barato']; ?>"
                                            data-precoc="<?php echo $e['preco_caro']; ?>">
                                        <i class="fa fa-pencil"></i>
                                    </button>

                                    <form method="post" action="" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este evento?');">
                                        <input type="hidden" name="acao" value="excluir_evento">
                                        <input type="hidden" name="id_evento" value="<?php echo $e['id_evento']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
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

<!-- MODAL EDICAO DE USUARIO -->
<div class="modal fade" id="modalEditUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
          <input type="hidden" name="acao" value="editar_usuario">
          <input type="hidden" name="id_user" id="edit-user-id">
          <div class="modal-header">
            <h5 class="modal-title">Editar Usuário</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label>Nome</label>
                <input type="text" name="name_user" id="edit-user-nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email_user" id="edit-user-email" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="empresa" id="edit-user-empresa" class="form-check-input" value="1">
                <label class="form-check-label" for="edit-user-empresa">É Empresa?</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL EDICAO DE EVENTO -->
<div class="modal fade" id="modalEditEvent" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="">
          <input type="hidden" name="acao" value="editar_evento">
          <input type="hidden" name="id_evento" id="edit-event-id">
          <div class="modal-header">
            <h5 class="modal-title">Editar Evento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label>Nome do Evento</label>
                <input type="text" name="nome_evento" id="edit-event-nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>CEP</label>
                <input type="text" name="endereco" id="edit-event-cep" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Preço Mínimo (R$)</label>
                    <input type="number" step="0.01" name="preco_barato" id="edit-event-precob" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Preço Máximo (R$)</label>
                    <input type="number" step="0.01" name="preco_caro" id="edit-event-precoc" class="form-control" required>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Salvar Alterações</button>
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
    // Inicialização do DataTables com Tradução em Português
    var configDataTables = {
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    };
    
    $('#tabelaUsuarios').DataTable(configDataTables);
    $('#tabelaEventos').DataTable(configDataTables);

    // Abrir Modal de Edição de Usuário preenchido
    $('.edit-user-btn').click(function() {
        $('#edit-user-id').val($(this).data('id'));
        $('#edit-user-nome').val($(this).data('nome'));
        $('#edit-user-email').val($(this).data('email'));
        $('#edit-user-empresa').prop('checked', $(this).data('empresa') == 1);
        
        var modalUser = new bootstrap.Modal(document.getElementById('modalEditUser'));
        modalUser.show();
    });

    // Abrir Modal de Edição de Evento preenchido
    $('.edit-event-btn').click(function() {
        $('#edit-event-id').val($(this).data('id'));
        $('#edit-event-nome').val($(this).data('nome'));
        $('#edit-event-cep').val($(this).data('cep'));
        $('#edit-event-precob').val($(this).data('precob'));
        $('#edit-event-precoc').val($(this).data('precoc'));
        
        var modalEvent = new bootstrap.Modal(document.getElementById('modalEditEvent'));
        modalEvent.show();
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>