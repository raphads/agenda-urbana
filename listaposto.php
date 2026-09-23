<?php
include "conexao.php";

$sql = "SELECT * FROM tb_evento";
$resultado = $conecta_db->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Lista de eventos</title>
   <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h2>Eventos cadastrados</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>CEP</th>
        <th>Número</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Hora abre</th>
        <th>Hora fecha</th>
        <th>Observações</th>
      </tr>
    </thead>
    <tbody>
      <?php while($p = $resultado->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $p['id_evento']; ?></td>
          <td><?php echo $p['nome_evento']; ?></td>
          <td><?php echo $p['endereco']; ?></td>
          <td><?php echo $p['num_evento']; ?></td>
          <td><?php echo $p['latitude']; ?></td>
          <td><?php echo $p['longitude']; ?></td>
          <td><?php echo $p['hora_abre']; ?></td>
          <td><?php echo $p['hora_fecha']; ?></td>
          <td><?php echo $p['obs']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>
