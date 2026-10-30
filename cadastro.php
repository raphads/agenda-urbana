<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit">
    <title>Agenda Urbana</title>
    <link rel="icon" href="images/iconlogo.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container-fluid">
    <div class="row">
      <div class="col-md-3">
        <br><br><br><br><br><br>
        <form id="logingform" method="post" action="cadastrar_conta.php">
            <label>Usuário</label><br>
            <input type="text" placeholder="Digite seu Usuário" required name="nome" class="form-control"><br>
            
            <label>Email</label><br>
            <input type="email" placeholder="Digite seu Email" required name="email" class="form-control"><br>
            
            <label>Senha</label><br>
            <input type="password" placeholder="***" required id="senha1" class="form-control"><br>
            
            <label>Confirmar Senha</label><br>
            <input type="password" placeholder="***" required id="senha2" name="senha" class="form-control"><br>
            
            <!-- Novo Checkbox para Empresa -->
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="empresa" name="empresa" value="1">
                <label class="form-check-label" for="empresa">Empresa?</label>
            </div>

            <button type="submit" id="botao" class="btn btn-primary" style="background-color: #17395e; border-radius: 40px; border-color: #ee603c">Cadastrar</button>
        </form>
        <a href="index.php" style="color: #ed683f;">Voltar</a>
      </div>

    <div class="col-md-9">
        <img src="images/logoagenda.jfif" width="100%" height="100%">
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>