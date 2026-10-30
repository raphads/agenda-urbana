<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit">
    <title>Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js" defer></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<header style="border-color: #ee603c">
    <?php include 'header.php' ?>
</header>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <h2>Adicionar Evento</h2>
        </div>
        <div class="col-md-4"></div>
    </div>
    
    <form id="eventoadd" method="post" action="cadastar_evento.php" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <label>Nome do Evento</label><br>
                <input type="text" class="form-control" placeholder="Digite o Nome do Evento" name="eventonome" required><br><br>
            </div>    
            <div class="col-md-6">
                <input type="file" name="fileToUpload[]" id="fileToUpload" multiple style="display: none">
                <label for="fileToUpload" style="display:inline-block; width:10px; height:10px; cursor:pointer;">
                    <img src="images/cameraplus.png" id="foto-placeholder" style="height: 100px; width: 100px; background-color: rgb(157, 156, 156); object-fit: cover;">
                </label>
                <!-- Carrossel de pré-visualização -->
                <div id="carouselPreview" class="carousel slide mt-3" data-bs-ride="carousel" style="width:100px; height:100px;">
                    <div class="carousel-inner" id="carousel-inner"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPreview" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPreview" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                        <span class="visually-hidden">Próximo</span>
                    </button>    
                </div>
            </div>
        </div>

        <!-- Campos de Preço adicionados com base na página de exibição -->
        <div class="row">
            <div class="col-md-6">
                <label>Preço Mínimo (De R$)</label><br>
                <input type="number" step="0.01" min="0" class="form-control" placeholder="0.00" name="preco_barato" required><br>
            </div>
            <div class="col-md-6">
                <label>Preço Máximo (Até R$)</label><br>
                <input type="number" step="0.01" min="0" class="form-control" placeholder="0.00" name="preco_caro" required><br>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label>Data de Início</label><br>
                <!-- Corrigido o name para 'data_abre' -->
                <input type="date" class="form-control" required id="data_abre" name="data_abre"><br>
            </div>  
            <div class="col-md-6">
                <label>Data de Encerramento</label><br>
                <!-- Corrigido o name para 'data_fecha' -->
                <input type="date" class="form-control" required id="data_fecha" name="data_fecha"><br>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label>Horário de Abertura</label><br>
                <input type="time" class="form-control" required id="tempo1" name="tempo1"><br>
            </div>
            <div class="col-md-6">
                <label>Horário de Encerramento</label><br>
                <input type="time" class="form-control" required id="tempo2" name="tempo2"><br>
            </div>
        </div>            
         
        <div class="row">
            <div class="col-md-6">
                <label>Latitude</label><br>
                <input type="text" class="form-control" id="lati" name="lati" placeholder="-##.####" required>
            </div>
            <div class="col-md-6">
                <label>Longitude</label><br>
                <input type="text" class="form-control" id="long" name="long" placeholder="-##.####" required>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>CEP</label><br>
                <input type="text" class="form-control" id="cep" name="cep">    
            </div>
            <div class="col-md-4">
                <label>Logradouro</label><br>
                <input type="text" class="form-control" disabled id="rua" name="rua" style="background-color: #bbb">
            </div>
            <div class="col-md-4">
                <label>Bairro</label><br>
                <input type="text" class="form-control" disabled id="bairro" name="bairro" style="background-color: #bbb">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-4">
                <label>Cidade</label><br>
                <input type="text" class="form-control" disabled id="cidade" name="cidade" style="background-color: #bbb">
            </div>
            <div class="col-md-4">
                <label>UF</label><br>
                <input type="text" class="form-control" disabled id="estado" name="estado" style="background-color: #bbb">
            </div>
            <div class="col-md-4">
                <label>Número</label><br>
                <input type="text" class="form-control" required id="numero" name="numero">
            </div>
        </div>

        <div class="row">
            <div class="col-md-8"><br>
                <label>Observações</label><br>
                <textarea cols="25" rows="5" name="obs" maxlength="250" class="form-control" style="width: 85%"></textarea>
            </div>
            <div class="col-md-4"><br><br><br>
                <input type="submit" value="Cadastrar" id="botao" class="btn btn-primary" style="background-color: #17395e; border-radius: 40px; border-color: #ee603c">
            </div>
        </div>
    </form>
</div>

<script>
  const inputFoto = document.getElementById('fileToUpload');
  const carouselInner = document.getElementById('carousel-inner');
  const placeholder = document.getElementById('foto-placeholder');

  inputFoto.addEventListener('change', function(event) {
    carouselInner.innerHTML = ""; // limpa imagens anteriores
    const arquivos = event.target.files;

    if (arquivos.length > 0) {
      placeholder.style.display = "none"; // esconde o placeholder
    }

    document.querySelector('#carouselPreview .carousel-control-prev').style.display = "block";
    document.querySelector('#carouselPreview .carousel-control-next').style.display = "block";

    for (let i = 0; i < arquivos.length; i++) {
      const arquivo = arquivos[i];
      if (arquivo.type.startsWith("image/")) {
        const img = document.createElement("img");
        img.src = URL.createObjectURL(arquivo);
        img.className = "d-block w-100 h-100"; // ocupa todo espaço do carrossel
        img.style.objectFit = "cover";        // mantém proporção sem distorcer

        const item = document.createElement("div");
        item.className = "carousel-item";
        if (i === 0) item.classList.add("active"); // primeira imagem ativa
        item.appendChild(img);

        carouselInner.appendChild(item);
      }
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>