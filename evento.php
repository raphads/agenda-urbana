<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
?>


<?php
include "conexao.php";

date_default_timezone_set('America/Sao_Paulo');

$posto_id = $_GET['id'];

// Dados do posto
$stmt = $conecta_db->prepare("SELECT * FROM tb_posto WHERE id_posto = ?");
if (!$stmt) {
    die("Erro no prepare1: " . $conecta_db->error);
}
$stmt->bind_param("i", $posto_id);
$stmt->execute();
$posto = $stmt->get_result()->fetch_assoc();

// Fotos
$stmt = $conecta_db->prepare("SELECT caminho FROM fotos_postos WHERE id_posto = ?");
if (!$stmt) {
    die("Erro no prepare2: " . $conecta_db->error);
}
$stmt->bind_param("i", $posto_id);
$stmt->execute();
$fotos = $stmt->get_result();
/*
// Avaliações
$stmt = $conecta_db->prepare("SELECT a.nota, a.comentario, u.nome, a.data 
                              FROM avaliacoes a 
                              JOIN usuarios u ON a.usuario_id = u.id_usuario 
                              WHERE a.id_posto = ?");
if (!$stmt) {
    die("Erro no prepare3: " . $conecta_db->error);
}
$stmt->bind_param("i", $posto_id);
$stmt->execute();
$avaliacoes = $stmt->get_result();

// Comentários
$stmt = $conecta_db->prepare("SELECT c.texto, c.data, u.nome 
                              FROM comentarios c 
                              JOIN usuarios u ON c.usuario_id = u.id_usuario 
                              WHERE c.id_posto = ?");
if (!$stmt) {
    die("Erro no prepare4: " . $conecta_db->error);
}
$stmt->bind_param("i", $posto_id);
$stmt->execute();
$comentarios = $stmt->get_result();
*/
// Inserir comentário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['texto'])) {
    $id_user = $_SESSION['usuario_id'];
    $texto = $_POST['texto'];

$id_user = $_SESSION['usuario_id']; // esse valor deve ser o id_user do cadastro
$stmt = $conecta_db->prepare("INSERT INTO tb_comentarios (id_user, id_posto, texto) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $id_user, $posto_id, $texto);
$stmt->execute();

}

// Buscar comentários
$stmt = $conecta_db->prepare("SELECT c.texto, c.data_comentario, u.name_user, u.email_user
                              FROM tb_comentarios c
                              JOIN tb_cadastro u ON c.id_user = u.id_user
                              WHERE c.id_posto = ?
                              ORDER BY c.data_comentario DESC");
$stmt->bind_param("i", $posto_id);
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
<body>
 <div class="container-fluid">
<div class="row">
      <div class="col-md-2">
   
      </div>

<div class="col-md-8">
<h1><?php echo $posto['nome_posto']; ?></h1>
 
<p><strong>CEP:</strong> <?php echo $posto['endereco']; ?></p>
<p><strong>Preço:</strong> <?php echo $posto['endereco']; ?></p>
<p><strong>Horário:</strong> 
    <?php echo date("H:i", strtotime($posto['hora_abre'])); ?> - 
    <?php echo date("H:i", strtotime($posto['hora_fecha'])); ?>
</p>
<p><strong>Data:</strong> De 
    <?php echo date("H:i", strtotime($posto['hora_abre'])); ?> até 
    <?php echo date("H:i", strtotime($posto['hora_abre'])); ?>
</p>
<p><strong>Observações:</strong> <?php echo $posto['obs']; ?></p>

<!-- Galeria de Fotos -->
<h2>Galeria de Fotos</h2>
<div id="carouselPosto" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php 
    $active = true;
    while($f = $fotos->fetch_assoc()) { ?>
      <div class="carousel-item <?php if($active){echo 'active'; $active=false;} ?>">
        <img src="<?php echo $f['caminho']; ?>" class="d-block w-100" style="max-height:500px; object-fit:cover;">
      </div>
    <?php } ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselPosto" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
    <span class="visually-hidden">Anterior</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselPosto" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
    <span class="visually-hidden">Próximo</span>
  </button>
</div>
</div>
<div class="col-md-2">

    </div>

<div class="row">
    <div class="col-md-2">

    </div>
    <div class="col-md-8">

    
<!-- Formulário de Comentários -->
<h2>Deixe seu comentário</h2>
<form method="post" action="">
    <textarea name="texto" class="form-control" rows="3" required></textarea><br>
    <input type="submit" value="Enviar" id="botao">
</form>

<!-- Lista de Comentários -->
<h2>Comentários</h2>
<?php while($c = $comentarios->fetch_assoc()) { ?>
    <div class="card mb-2">
        <div class="card-body">
            <p><?php echo nl2br(htmlspecialchars($c['texto'])); ?></p>
            <small>
                Por <strong><?php echo $c['email_user']; ?></strong> 
                em <?php echo date("d/m/Y H:i", strtotime($c['data_comentario'])); ?>
            </small>
        </div>
       
        <div class="col-md-2">

    </div>
</div>
<?php } ?>

</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>