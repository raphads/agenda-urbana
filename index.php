<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Login - Agenda Urbana</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
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
            background: #fff;
        }
        .image-section {
            background: linear-gradient(135deg, #17395e, #102a48);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            min-height: 480px;
        }
        .form-section {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .btn-custom-primary {
            background-color: #17395e;
            border-color: #ee603c;
            color: #fff;
            border-radius: 40px;
            padding: 10px 30px;
            font-weight: 600;
            width: 100%;
            transition: background 0.2s;
        }
        .btn-custom-primary:hover {
            background-color: #102a48;
            border-color: #d94e2b;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            
            <div class="row card-custom">
                
                <!-- Imagem à Esquerda -->
                <div class="col-lg-7 image-section d-none d-lg-flex">
                    <img src="images/logoagenda.jfif" alt="Agenda Urbana">
                </div>

                <!-- Formulário à Direita -->
                <div class="col-lg-5 form-section">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark mb-1"><i class="fa fa-calendar-check-o text-primary me-2"></i> Agenda Urbana</h2>
                        <p class="text-muted small">Faça login para acessar os eventos</p>
                    </div>

                    <form id="logingform" method="post" action="realizar_login.php">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Email</label>
                            <input type="email" placeholder="Digite seu Email" name="txt_email" class="form-control" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">Senha</label>
                            <input type="password" placeholder="***" name="txt_senha" class="form-control" required>
                        </div>
                        
                        <button type="submit" id="botao" class="btn btn-custom-primary mb-3">Entrar</button>
                    </form>

                    <div class="text-center mt-2">
                        <span class="text-muted small">Não tem uma Conta?</span><br>
                        <a href="cadastro.php" class="fw-bold text-decoration-none" style="color: #ee603c;">Cadastre-se</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>