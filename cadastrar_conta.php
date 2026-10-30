<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    
    // Verifica se o checkbox foi marcado (1 para verdadeiro, 0 para falso)
    $empresa = isset($_POST["empresa"]) ? 1 : 0;

    // Inserção corrigida utilizando Prepared Statement de forma segura
    $sql = $conecta_db->prepare("INSERT INTO tb_cadastro (name_user, email_user, password_user, empresa) VALUES (?, ?, ?, ?)");
    
    // "sssi" indica 3 strings e 1 inteiro (para a coluna empresa)
    $sql->bind_param("sssi", $usuario, $email, $senha, $empresa);
    $sql->execute();
}

header('location: maps.php');
exit;
?>