jQuery(document).ready(function ($) {	        
    
    //C�digo General Jquery
    //Login
    $("#formLogin").validate({
        rules: {  
            usuario:{required: true},
            clave: {required: true}
				
        },
        messages: {                       
            usuario: "Escriba usuario",
            clave: "Escriba password"
           
                 
        }
    });
    
    //C�digo de confirmaci�n
    $('.confirmation').click(function () {
        
        mensaje = $(this).attr("title");
        
        if(mensaje == null){
            mensaje = '\u00BFEst\u00e1s Seguro?'; 
        }
        
        return  confirm(mensaje);
        
    });      
          
});