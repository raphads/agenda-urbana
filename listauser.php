<?php
include "conexao.php";

$sql = "SELECT * FROM tb_cadastro";
$resultado = $conecta_db->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de Usuários</title>
   <link rel="icon" href="images/logo-agenda-teste.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Usuários cadastrados</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>
      <?php while($u = $resultado->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $u['id_user']; ?></td>
          <td><?php echo $u['name_user']; ?></td>
          <td><?php echo $u['email_user']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>
