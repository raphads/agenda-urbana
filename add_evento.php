<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit">
    <title>Pernoite Amiga</title>
    <link rel="icon" href="images/iconlogo.png">
    <link rel="stylesheet" href="style.css">
    <script src="pernoite.js" defer></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
   


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


</head>

<body>
<div class="container-fluid">
<div class="row">
        <div class="col-md-4"><div class="menu-icon" onclick="toggleMenu()">&#9776;</div>
        <nav class="sidebar" id="sidebar">
        <ul>
            <li><button><a href="maps.php">Mapa <img class="icone" src="images/map (1)l.png"></a></button></li>
            <li><button><a href="add_posto.php">Adicionar Posto&nbsp;<img class="icone" src="images/gas-stationl.png"></a></button></li>
            
            
        </ul>
        <button><a href="index.php">Sair&nbsp;<img class="icone" src="images/logoutl.png"></a></button>
    </nav>
</div>
        <div class="col-md-4">
            <h2>Adicionar Posto</h2>
        </div>
        <div class="col-md-4"></div>
</div>
<form id="postoadd" method="post" action="cadastar_posto.php" enctype="multipart/form-data">
<div class="row">
            <div class="col-md-4">
                <label>Nome</label><br>
                <input type="text" placeholder="Digite o Nome do Posto" name="postonome" required><br><br>
            </div>    
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
</div>             

<div class="row">
                    <div class="col-md-4">
                        <label>Horário</label><br>
                        <input type="checkbox" id="horas" name="horas" onchange="horario()">Posto 24 horas<br>
                        <label>Horário de Abertura</label><br>
                        <input type="time" required id="tempo1" name="tempo1"><br>
                    </div>
                    <div class="col-md-4">
                        <br><br><label>Horário de Encerramento</label><br>
                        <input type="time" required id="tempo2" name="tempo2"><br><br>
                    </div>
                
            
<div class="col-md-4">
  <input type="file" name="fileToUpload[]" id="fileToUpload" multiple style="display: none">
  <label for="fileToUpload" style="display:inline-block; width:10px; height:10px; cursor:pointer; ">
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
          
<div class="row">
           <div class="col-md-4">
                <label>Latitude</label><br>
                <input type="text" id="lati" name="lati" placeholder="-##.####" required>
            </div>
           
                
                <div class="col-md-4">
                    <label>Longitude</label><br>
                     <input type="text" id="long" name="long" placeholder="-##.####" required>
                </div>
            
        <div class="col-md-4"></div>

<div class="row">
    <div class="col-md-4">
        <label>CEP</label><br>
        <input type="text" id="cep" name="cep">    
    </div>
    <div class="col-md-4">
        <label>Logradouro</label><br>
        <input type="text" disabled id="rua" name="rua" style="background-color: #bbb">
    </div>
    <div class="col-md-4">
        <label>Bairro</label><br>
        <input type="text" disabled id="bairro" name="bairro" style="background-color: #bbb">
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <label>Cidade</label><br>
        <input type="text" disabled id="cidade" name="cidade" style="background-color: #bbb">
    </div>
    <div class="col-md-4">
        <label>UF</label><br>
        <input type="text" disabled id="estado" name="estado" style="background-color: #bbb">
    </div>
    <div class="col-md-4">
        <label>Número</label><br>
        <input type="text" required id="numero" name="numero">
    </div>
</div>

</div>    
<div class="row">
    <div class="col-md-4">
        <label>Observações</label><br>
        <textarea cols="25" rows="5" name="obs" maxlength="250"></textarea>
    </div>
    <div class="col-md-4"><br>
        <input type="submit" value="Cadastrar" id="botao">
    </div>
    <div class="col-md-4">
        
    </div>
</form>
</div>     




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