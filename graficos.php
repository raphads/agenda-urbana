<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include "conexao.php";

// 1. Contagem de Usuários Comuns vs Empresas
$sql_users = "SELECT empresa, COUNT(*) as total FROM tb_cadastro GROUP BY empresa";
$res_users = $conecta_db->query($sql_users);
$comum = 0;
$empresa = 0;
while($row = $res_users->fetch_assoc()) {
    if($row['empresa'] == 1) {
        $empresa = $row['total'];
    } else {
        $comum = $row['total'];
    }
}

// 2. Buscar CEPs dos eventos cadastrados para processar as cidades via JavaScript
$sql_eventos = "SELECT endereco FROM tb_evento WHERE endereco IS NOT NULL AND endereco != ''";
$res_eventos = $conecta_db->query($sql_eventos);
$ceps_eventos = [];
while($row_ev = $res_eventos->fetch_assoc()) {
    // Remove traços ou caracteres especiais para garantir 8 dígitos
    $cep_limpo = preg_replace('/\D/', '', $row_ev['endereco']);
    if(strlen($cep_limpo) === 8) {
        $ceps_eventos[] = $cep_limpo;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            background: #ffffff;
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
            <h1 class="h3 mb-0"><i class="fa fa-pie-chart text-warning"></i> Estatísticas e Gráficos</h1>
            <p class="text-white-50 mb-0">Panorama geral dos usuários e distribuição geográfica dos eventos</p>
        </div>
    </div>
</div>

<div class="container my-4 mb-5">
    <div class="row">
        
        <!-- Gráfico de Pizza / Rosca: Usuários vs Empresas -->
        <div class="col-lg-5 mb-4">
            <div class="card card-custom p-4 h-100">
                <h4 class="fw-bold text-dark mb-3"><i class="fa fa-users text-primary me-2"></i> Tipos de Usuários</h4>
                <p class="text-muted small">Proporção entre contas comuns e contas corporativas (empresas).</p>
                <div style="position: relative; height: 280px; width: 100%;" class="d-flex justify-content-center align-items-center">
                    <canvas id="userChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Colunas: Eventos por Cidade (Via CEP) -->
        <div class="col-lg-7 mb-4">
            <div class="card card-custom p-4 h-100">
                <h4 class="fw-bold text-dark mb-3"><i class="fa fa-bar-chart text-success me-2"></i> Eventos por Cidade</h4>
                <p class="text-muted small">Consulta automática via CEP dos eventos cadastrados.</p>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="cityChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Scripts dos Gráficos -->
<script>
    // 1. Gráfico de Rosca (Usuários vs Empresas)
    var ctxUser = document.getElementById('userChart').getContext('2d');
    var userChart = new Chart(ctxUser, {
        type: 'doughnut',
        data: {
            labels: ['Usuários Comuns', 'Empresas'],
            datasets: [{
                data: [<?php echo $comum; ?>, <?php echo $empresa; ?>],
                backgroundColor: ['#17395e', '#ee603c'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 2. Gráfico de Colunas (Eventos por Cidade resolvidos via ViaCEP)
    const cepsList = <?php echo json_encode($ceps_eventos); ?>;

    async function gerarGraficoCidades() {
        let contagemCidades = {};

        for (let cep of cepsList) {
            try {
                let response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                let data = await response.json();
                if (!data.erro) {
                    let cidadeEstado = data.localidade + " / " + data.uf;
                    contagemCidades[cidadeEstado] = (contagemCidades[cidadeEstado] || 0) + 1;
                }
            } catch (error) {
                console.error("Erro ao consultar CEP:", cep);
            }
        }

        let labelsCidades = Object.keys(contagemCidades);
        let valoresCidades = Object.values(contagemCidades);

        var ctxCity = document.getElementById('cityChart').getContext('2d');
        new Chart(ctxCity, {
            type: 'bar',
            data: {
                labels: labelsCidades.length > 0 ? labelsCidades : ['Nenhum evento com CEP'],
                datasets: [{
                    label: 'Número de Eventos',
                    data: valoresCidades.length > 0 ? valoresCidades : [0],
                    backgroundColor: '#17395e',
                    borderColor: '#102a48',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Executa a função ao carregar a página
    gerarGraficoCidades();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>