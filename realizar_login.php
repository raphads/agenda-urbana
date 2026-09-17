<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit">
    <title>Pernoite Amiga</title>
    <link rel="icon" href="images/logo-agenda-teste.png">
    <link rel="stylesheet" href="style.css">
    <script src="agenda.js"></script>
    <script type="text/javascript" src="jquery-3.5.1.min.js"></script>

        </head>
        </html>
<?php
session_start();
 include 'conexao.php';
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
$user = $_POST["txt_email"];
$senha = $_POST["txt_senha"];
$sql = $conecta_db->prepare("SELECT * FROM tb_cadastro WHERE email_user = ? AND password_user = ?");
$sql->bind_param("ss", $user, $senha); // "ss" = duas strings
$sql->execute();
$result = $sql->get_result(); // Executa a consulta e obtém o resultado

if($user == "admin@pernoite.com" && $senha == "@Admin1234"){
    echo "<center>";
    echo "<br>";
    //echo "<a href=\"area_adm.php\">Area do Administrador</a>";
    header('location:area_adm.php');
}else if ($result->num_rows > 0) {
    echo "<center>";
    echo "<hr>";
    echo "Login realizado com sucesso!";
    echo "<hr>";
	echo "<br>";
    $row = $result->fetch_assoc();
    $_SESSION['usuario_id'] = $row['id_user'];
    $_SESSION['usuario'] = $row['email_user'];
    header('location:maps.php');
} else {
    echo "<center>";
    echo "<hr>";
    echo "Usuario ou Senha não conferem. Tente novamente.";
    echo "<hr>";
	echo "<br>";
    echo "<a href=\"index.php\">Voltar </a>";
    }
}

?>