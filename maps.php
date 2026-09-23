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
        <title>Mapa</title>
        <link rel="icon" href="images/logo-agenda-teste.png">
        <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
 <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 500px; width: 80%; margin: auto; border: 2px solid #ccc; border-radius: 10px; }
    </style>
    </head>
    <header>
<?php include 'header.php' ?>
</header>
    <body>
        
<?php
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
        "fotos" => explode(",", $linha['fotos'])
    ];
}
?>

        <div class="container-fluid">
            <div class="row">
        
    <div class="col-md-4"></div>
    
               <h3>Mapa dos Eventos</h3>
                <div id="map"></div>

            </div>
            <div class="row">
                
                <div class="col-md-4">
                    
                     <br>
                 <label>Raio</label>&nbsp;&nbsp;<input type="Text" placeholder="Digite o raio de Busca" required>
                 </div>
                 <div class="col-md-4">
            
</div>
     <div class="col-md-4">
         <br>
            <input type="button" value="Buscar" id="botao">
</div>
            </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script do Leaflet -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Criação do mapa
    var map = L.map('map').setView([-23.66, -46.46], 10); // ajuste para sua região

    // Camada do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Dados vindos do PHP
    var eventosMapa = <?php echo json_encode($eventos, JSON_UNESCAPED_UNICODE); ?>;

    // Loop para adicionar marcadores
    eventosMapa.forEach(function(p) {
        L.marker([p.lat, p.lng])
         .addTo(map)
         .bindPopup(p.nome);
    });

    eventosMapa.forEach(function(p) {
    var popupContent = "<a href='evento.php?id=" + p.id + "'><b>" + p.nome + "</b></a><br>";
    if (p.fotos) {
        p.fotos.forEach(function(foto) {
            popupContent += "<img src='" + foto + "' width='100' style='margin:5px'>";
        });
    }
    L.marker([p.lat, p.lng]).addTo(map).bindPopup(popupContent);
});

    navigator.geolocation.getCurrentPosition(function(pos) {
    var centroLat = pos.coords.latitude;
    var centroLng = pos.coords.longitude;
    map.setView([centroLat, centroLng], 13);

    // Exibir todos inicialmente
    atualizarMapa(centroLat, centroLng, 50); // exemplo: raio inicial 50 km
});


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

var markersLayer = L.layerGroup().addTo(map);

function atualizarMapa(centroLat, centroLng, raio) {
    markersLayer.clearLayers();
    eventosMapa.forEach(function(p) {
        var distancia = calcularDistancia(centroLat, centroLng, p.lat, p.lng);
        if (distancia <= raio) {
            var popupContent = "<b>" + p.nome + "</b><br>";
            if (p.fotos) {
                p.fotos.forEach(function(foto) {
                    popupContent += "<img src='" + foto + "' width='100'>";
                });
            }
            L.marker([p.lat, p.lng]).addTo(markersLayer).bindPopup(popupContent);
        }
    });
}

document.getElementById("botao").addEventListener("click", function() {
    var raio = parseFloat(document.querySelector("input[placeholder='Digite o raio de Busca']").value);
    atualizarMapa(centroLat, centroLng, raio);
});
</script>
</body>

</html>
