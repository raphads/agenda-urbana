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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Adicionar Evento - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js" defer></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
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
        }
        .upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-box:hover {
            border-color: #ee603c;
            background-color: #fffaf7;
        }
        .btn-custom-accent {
            background-color: #ee603c;
            border-color: #ee603c;
            color: #fff;
            border-radius: 8px;
            padding: 10px 30px;
            font-weight: 600;
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
            <h1 class="h3 mb-0"><i class="fa fa-calendar-plus-o text-warning"></i> Adicionar Novo Evento</h1>
            <p class="text-white-50 mb-0">Preencha as informações para cadastrar um evento na plataforma</p>
        </div>
    </div>
</div>

<div class="container my-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="card card-custom p-4 p-md-5 bg-white">
                <form id="eventoadd" method="post" action="cadastar_evento.php" enctype="multipart/form-data">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Nome do Evento</label>
                            <input type="text" class="form-control" placeholder="Digite o nome do evento" name="eventonome" required>
                        </div> 
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fotos do Evento</label>
                            <div class="upload-box" onclick="document.getElementById('fileToUpload').click();">
                                <input type="file" name="fileToUpload[]" id="fileToUpload" multiple style="display: none">
                                <div id="upload-placeholder">
                                    <i class="fa fa-camera fa-2x text-muted mb-1"></i>
                                    <p class="small text-muted mb-0">Clique para enviar</p>
                                </div>
                                <!-- Carrossel de pré-visualização -->
                                <div id="carouselPreview" class="carousel slide d-none mt-2" data-bs-ride="carousel" style="width:100%; height:90px;">
                                    <div class="carousel-inner rounded" id="carousel-inner"></div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPreview" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPreview" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>    
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preços -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Preço Mínimo (R$)</label>
                            <input type="number" step="0.01" min="0" class="form-control" placeholder="0.00" name="preco_barato" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Preço Máximo (R$)</label>
                            <input type="number" step="0.01" min="0" class="form-control" placeholder="0.00" name="preco_caro" required>
                        </div>
                    </div>

                    <!-- Datas -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de Início</label>
                            <input type="date" class="form-control" required id="data_abre" name="data_abre">
                        </div>  
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Data de Encerramento</label>
                            <input type="date" class="form-control" required id="data_fecha" name="data_fecha">
                        </div>
                    </div>

                    <!-- Horários -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horário de Abertura</label>
                            <input type="time" class="form-control" required id="tempo1" name="tempo1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Horário de Encerramento</label>
                            <input type="time" class="form-control" required id="tempo2" name="tempo2">
                        </div>
                    </div>            

                    <!-- Coordenadas -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Latitude</label>
                            <input type="text" class="form-control" id="lati" name="lati" placeholder="-23.6821" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Longitude</label>
                            <input type="text" class="form-control" id="long" name="long" placeholder="-46.5427" required>
                        </div>
                    </div>

                    <!-- Endereço (CEP e Auto-preenchimento) -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000">    
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Logradouro</label>
                            <input type="text" class="form-control bg-light" disabled id="rua" name="rua">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Bairro</label>
                            <input type="text" class="form-control bg-light" disabled id="bairro" name="bairro">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Cidade</label>
                            <input type="text" class="form-control bg-light" disabled id="cidade" name="cidade">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">UF</label>
                            <input type="text" class="form-control bg-light" disabled id="estado" name="estado">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Número</label>
                            <input type="text" class="form-control" required id="numero" name="numero" placeholder="Ex: 1200">
                        </div>
                    </div>

                    <!-- Observações e Botão -->
                    <div class="row align-items-end mt-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Observações</label>
                            <textarea rows="3" name="obs" maxlength="250" class="form-control" placeholder="Informações adicionais sobre o evento..."></textarea>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <button type="submit" id="botao" class="btn btn-custom-accent w-100">
                                <i class="fa fa-check-circle me-1"></i> Cadastrar Evento
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<script>
  const inputFoto = document.getElementById('fileToUpload');
  const carouselInner = document.getElementById('carousel-inner');
  const placeholder = document.getElementById('upload-placeholder');
  const carouselPreview = document.getElementById('carouselPreview');

  inputFoto.addEventListener('change', function(event) {
    carouselInner.innerHTML = ""; 
    const arquivos = event.target.files;

    if (arquivos.length > 0) {
      placeholder.style.display = "none"; 
      carouselPreview.classList.remove('d-none');
    }

    for (let i = 0; i < arquivos.length; i++) {
      const arquivo = arquivos[i];
      if (arquivo.type.startsWith("image/")) {
        const img = document.createElement("img");
        img.src = URL.createObjectURL(arquivo);
        img.className = "d-block w-100 h-100"; 
        img.style.objectFit = "cover";        

        const item = document.createElement("div");
        item.className = "carousel-item";
        if (i === 0) item.classList.add("active"); 
        item.appendChild(img);

        carouselInner.appendChild(item);
      }
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>