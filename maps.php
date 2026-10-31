<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php"; // conexão com o banco

$sql = "SELECT p.id_evento, p.nome_evento, p.latitude, p.longitude, 
            GROUP_CONCAT(f.caminho) AS fotos
        FROM tb_evento p
        LEFT JOIN fotos_eventos f ON p.id_evento = f.id_evento
        GROUP BY p.id_evento";
$resultado = mysqli_query($conecta_db, $sql);

$eventos = [];
while ($linha = mysqli_fetch_assoc($resultado)) {
    $eventos[] = [
        "id" => $linha['id_evento'],
        "nome" => $linha['nome_evento'],
        "lat" => (float)$linha['latitude'],
        "lng" => (float)$linha['longitude'],
        "fotos" => $linha['fotos'] ? explode(",", $linha['fotos']) : []
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa dos Eventos - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    
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
        #map { 
            height: 500px; 
            width: 100%; 
            border-radius: 10px; 
            z-index: 1;
        }
        .btn-custom-accent {
            background-color: #ee603c;
            border-color: #ee603c;
            color: #fff;
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
            <h1 class="h3 mb-0"><i class="fa fa-map-marker text-warning"></i> Mapa dos Eventos</h1>
            <p class="text-white-50 mb-0">Encontre eventos próximos a você!</p>
        </div>
    </div>
</div>

<div class="container my-4 mb-5">
    
    <!-- Filtro de Raio e Busca -->
    <div class="card card-custom p-3 bg-white mb-4">
        <div class="row align-items-end">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="form-label fw-bold"><i class="fa fa-compass text-primary me-1"></i> Raio de Busca (km)</label>
                <input type="number" id="raioInput" class="form-control" placeholder="Ex: 10" min="1" required>
            </div>
            <div class="col-md-6 text-md-end">
                <button type="button" id="botao" class="btn btn-custom-accent px-4 py-2">
                    <i class="fa fa-search me-1"></i> Filtrar por Raio
                </button>
            </div>
        </div>
    </div>

    <!-- Mapa Principal -->
    <div class="card card-custom p-4 bg-white">
        <h4 class="mb-3 text-dark fw-bold"><i class="fa fa-globe text-primary me-2"></i> Localização dos Eventos</h4>
        <div id="map"></div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Variáveis globais de coordenadas do usuário
    var centroLat = -23.66;
    var centroLng = -46.46;

    // Criação do mapa
    var map = L.map('map').setView([centroLat, centroLng], 10);

    // Camada do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Dados vindos do PHP
    var eventosMapa = <?php echo json_encode($eventos, JSON_UNESCAPED_UNICODE); ?>;

    var markersLayer = L.layerGroup().addTo(map);

    function formatarPopup(p) {
        var content = "<div style='text-align: center;'>";
        content += "<a href='evento.php?id=" + p.id + "' class='fw-bold text-decoration-none'><b>" + p.nome + "</b></a><br>";
        if (p.fotos && p.fotos.length > 0) {
            content += "<div class='mt-2'>";
            p.fotos.forEach(function(foto) {
                content += "<img src='" + foto + "' width='100' class='rounded shadow-sm' style='margin:2px; object-fit: cover; height: 70px;'>";
            });
            content += "</div>";
        }
        content += "</div>";
        return content;
    }

    function atualizarMapa(lat, lng, raio) {
        markersLayer.clearLayers();
        eventosMapa.forEach(function(p) {
            var distancia = calcularDistancia(lat, lng, p.lat, p.lng);
            if (distancia <= raio) {
                L.marker([p.lat, p.lng]).addTo(markersLayer).bindPopup(formatarPopup(p));
            }
        });
    }

    function calcularDistancia(lat1, lon1, lat2, lon2) {
        var R = 6371; // km
        var dLat = (lat2 - lat1) * Math.PI / 180;
        var dLon = (lon2 - lon1) * Math.PI / 180;
        var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Geolocalização do navegador
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            centroLat = pos.coords.latitude;
            centroLng = pos.coords.longitude;
            map.setView([centroLat, centroLng], 12);
            
            // Exibir todos inicialmente com raio padrão amplo (ex: 100km)
            atualizarMapa(centroLat, centroLng, 100);
        }, function() {
            // Se o usuário negar, exibe todos os eventos sem filtro de raio
            eventosMapa.forEach(function(p) {
                L.marker([p.lat, p.lng]).addTo(markersLayer).bindPopup(formatarPopup(p));
            });
        });
    } else {
        eventosMapa.forEach(function(p) {
            L.marker([p.lat, p.lng]).addTo(markersLayer).bindPopup(formatarPopup(p));
        });
    }

    // Botão de busca por raio
    document.getElementById("botao").addEventListener("click", function() {
        var raioVal = document.getElementById("raioInput").value;
        var raio = raioVal ? parseFloat(raioVal) : 50; // Se vazio, usa 50km
        atualizarMapa(centroLat, centroLng, raio);
    });
</script>

</body>
</html>