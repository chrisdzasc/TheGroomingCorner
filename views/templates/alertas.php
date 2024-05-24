<?php
    foreach($alertas as $key => $mensajes): // Este foreach va a iterar sobre el arreglo principal para acceder al key 
        foreach($mensajes as $mensaje): // Este foreach va a iterar sobre los mensajes
?>
            <div class="alerta <?php echo $key; ?>">
                <?php echo $mensaje; ?>
            </div>
<?php
        endforeach;
    endforeach;
?>