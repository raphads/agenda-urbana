<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Cadastro - Agenda Urbana</title>
    <link rel="icon" href="images/iconlogo.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .form-section {
            background-color: #ffffff;
            padding: 40px;
        }
        .image-section {
            background: linear-gradient(135deg, #17395e, #102a48);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .image-section img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* Garante que a imagem inteira apareça sem cortes */
        }
        .btn-custom-accent {
            background-color: #ee603c;
            border-color: #ee603c;
            color: #fff;
            border-radius: 40px;
            padding: 10px 30px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn-custom-accent:hover {
            background-color: #d94e2b;
            border-color: #d94e2b;
            color: #fff;
        }
        .back-link {
            color: #ee603c;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #d94e2b;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            
            <div class="row card-custom">
                
                <!-- Coluna do Formulário -->
                <div class="col-lg-6 form-section">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark mb-1"><i class="fa fa-user-plus text-primary me-2"></i> Criar Conta</h2>
                        <p class="text-muted small">Cadastre-se para acessar todos os eventos da Agenda Urbana</p>
                    </div>

                    <form id="logingform" method="post" action="cadastrar_conta.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Usuário</label>
                            <input type="text" placeholder="Digite seu Usuário" required name="nome" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Email</label>
                            <input type="email" placeholder="Digite seu Email" required name="email" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Senha</label>
                            <input type="password" placeholder="***" required id="senha1" class="form-control">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Confirmar Senha</label>
                            <input type="password" placeholder="***" required id="senha2" name="senha" class="form-control">
                        </div>
                        
                        <!-- Checkbox Empresa -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="empresa" name="empresa" value="1">
                            <label class="form-check-label" for="empresa">Empresa?</label>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <button type="submit" id="botao" class="btn btn-custom-accent">Cadastrar</button>
                            <a href="index.php" class="back-link small"><i class="fa fa-arrow-left me-1"></i> Voltar</a>
                        </div>
                    </form>
                </div>

                <!-- Coluna da Imagem (Sem cortes) -->
                <div class="col-lg-6 image-section d-none d-lg-flex">
                    <img src="images/logoagenda.jfif" alt="Agenda Urbana">
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>