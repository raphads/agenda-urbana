<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recebendo todos os dados do formulário com segurança
    $eventonome   = $_POST["eventonome"];
    $preco_barato = $_POST["preco_barato"];
    $preco_caro   = $_POST["preco_caro"];
    $data_abre    = $_POST["data_abre"];
    $data_fecha   = $_POST["data_fecha"];
    $tempo1       = $_POST["tempo1"];
    $tempo2       = $_POST["tempo2"];
    $lati         = $_POST["lati"];
    $long         = $_POST["long"];
    $cep          = $_POST["cep"];
    $numero       = $_POST["numero"];
    $obs          = $_POST["obs"];

    // 2. Inserção do evento utilizando Prepared Statement
    $sql = $conecta_db->prepare("INSERT INTO tb_evento (nome_evento, preco_barato, preco_caro, data_abre, data_fecha, endereco, num_evento, latitude, longitude, hora_abre, hora_fecha, obs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Tipos: s = string, d = decimal/double
    $sql->bind_param("sddsssssssss", $eventonome, $preco_barato, $preco_caro, $data_abre, $data_fecha, $cep, $numero, $lati, $long, $tempo1, $tempo2, $obs);
    
    if ($sql->execute()) {
        $evento_id = $conecta_db->insert_id;

        // 3. Upload múltiplo de fotos
        if (isset($_FILES['fileToUpload']) && !empty($_FILES['fileToUpload']['name'][0])) {
            $target_dir = "uploads/";

            // Cria a pasta uploads caso ela não exista
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $totalFiles = count($_FILES['fileToUpload']['name']);

            for ($i = 0; $i < $totalFiles; $i++) {
                // Evita conflito de nomes duplicados usando uniqid()
                $file_name = uniqid() . "_" . basename($_FILES['fileToUpload']['name'][$i]);
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'][$i], $target_file)) {
                    $sqlFoto = $conecta_db->prepare("INSERT INTO fotos_eventos (id_evento, caminho) VALUES (?, ?)");
                    $sqlFoto->bind_param("is", $evento_id, $target_file);
                    $sqlFoto->execute();
                }
            }
        }
    } else {
        die("Erro ao cadastrar o evento: " . $conecta_db->error);
    }
}

// Redireciona após o cadastro
header('location: maps.php');
exit;
?>