function toggleMenu() {
   if (document.getElementById("sidebar").classList.toggle("active")){
    document.getElementsByClassName("menu-icon").toggle("visible");
        document.getElementById("sidebar").classList.toggle("deactive");
   }
   if (document.getElementById("sidebar").classList.toggle("false")){
        document.getElementById("sidebar").classList.toggle("active");
}
}

function horario(){
     var hora = document.getElementById("horas").checked;
     if(hora == true){
          document.getElementById("tempo1").readOnly = true;
          document.getElementById("tempo2").readOnly = true;
          document.getElementById("tempo1").value = "00:00";
          document.getElementById("tempo2").value = "00:00";
          //document.getElementById("tempo").disabled = true;
     } else{
         document.getElementById("tempo1").readOnly = false;
         document.getElementById("tempo2").readOnly = false;
         //document.getElementById("tempo").disabled = false;
     }
}

function Confirmar(){
     if (document.getElementById("senha2").value != document.getElementById("senha1").value){
          alert("Senhas não conferem");
     }
}

$(document).ready(function(){
    $('#cpf').inputmask('999.999.999-99'); // Máscara para CPF
    $('#email').inputmask('aaa@aaa.com'); // Máscara para email
    $('#telefone').inputmask('(99)99999-9999'); // Máscara para Telefone
    $('#cep').inputmask('99999-999'); // Máscara para CEP
    $('#lati').inputmask('-99.9999');
     $('#long').inputmask('-99.9999');



$('#cep').on('blur', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                if (!("erro" in data)) {
                    $('#rua').val(data.logradouro);
                    $('#bairro').val(data.bairro);
                    $('#cidade').val(data.localidade);
                    $('#estado').val(data.uf);
                } else {
                    alert("CEP não encontrado.");
                    document.getElementById("cep").value="";
                    document.getElementById("cep").focus();
                }
            });
        }
    });	
});
