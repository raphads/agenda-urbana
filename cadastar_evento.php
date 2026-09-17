<?php
 include 'conexao.php';
 if ($_SERVER["REQUEST_METHOD"] == "POST") {

$postonome = $_POST["postonome"];
$tempo1 = $_POST["tempo1"];
$tempo2 = $_POST["tempo2"];
$fileToUpload = $_POST["fileToUpload"];
$lati = $_POST["lati"];
$long = $_POST["long"];
$cep = $_POST["cep"];
$numero = $_POST["numero"];
$obs = $_POST["obs"];

$sql = $conecta_db->prepare("SELECT * FROM tb_posto WHERE nome_posto = ?");
$sql->bind_param("s", $postonome); 
$sql->execute();
$result = $sql->get_result();


      $sql = $conecta_db->prepare ("INSERT INTO tb_posto (nome_posto, endereco, num_posto, latitude, longitude, hora_abre, hora_fecha, obs)
	                     VALUES ('$postonome','$cep','$numero','$lati','$long','$tempo1','$tempo2','$obs')") ;
    $sql->execute();

    $posto_id = $conecta_db->insert_id;

    //Upload da foto
    if (!empty($_FILES["fileToUpload"]["name"])) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["fileToUpload"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            
            $sqlFoto = $conecta_db->prepare("INSERT INTO fotos_postos (id_posto, caminho) VALUES (?, ?)");
            $sqlFoto->bind_param("is", $posto_id, $target_file);
            $sqlFoto->execute();
        }
     }
    }

header('location:maps.php');
?>