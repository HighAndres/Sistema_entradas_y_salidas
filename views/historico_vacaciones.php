<?php
include('../lib/connect.php');

session_start();
if (!isset($_SESSION['email']) && !isset($_SESSION['rol'])) {
	header("Location:../index.php");
} else {
    if($_SESSION['rol'] != 2){
        header("Location:home.php");
    }
}

//ARRAY TODOS LOS REGISTROS    
$permisos=$conectar->query("SELECT * FROM vacaciones ORDER BY fecha_registro DESC");
$nump=$permisos->num_rows;
$arrayp=$permisos->fetch_assoc();

if(isset($_POST['editar'])){
    $id = $_GET['id'];
    $estatus=$_POST['estatus'];
    
    $conectar->query("DELETE FROM vacaciones WHERE id_vcns= '$id'");
    $_SESSION['success']='toastr.success("¡Se ha eliminado el registro!", "Éxito",{ "progressBar": true, "positionClass": "toast-top-center","hideMethod": "slideUp","extendedTimeOut": "700"})';
    header("Location:historico_vacaciones.php");
    exit();
}

date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, 'es_MX');

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Histórico de pedidos</title>

    <?php include('../lib/head_links.php'); ?>
</head>

<body>
    <?php include('../lib/header.php'); 
          include('../jquery/alerts.php');
    ?>
    
    <div class="cont">
        <div class="encabezado">
            <h3>Histórico de vacaciones</h3>
            <a href="home.php" class="btn btn-light btn-lg inicio"><i class="fas fa-home"></i></a>
        </div>
        <div class="tabla">
            <table id="tablax" class="display table-striped table-bordered dataTable no-footer">
                <thead>
                    <th>USUARIO</th>
                    <th>FECHA DE EXPEDICIÓN</th>
                    <th>DÍAS A DISFRUTAR</th>
                    <th>DEL</th>
                    <th>AL</th>
                    <th>REGRESA A SUS LABORES EL DÍA</th>
                    <th>ESTATUS</th>
                    <th>ELIMINAR</th>
                </thead>

                <tbody>
                    <?php 
            if($nump>0){
                do{?>
                    <!-- Ventana Eliminar Registros CRUD -->
                    <div class="modal fade" id="delete_<?php echo $arrayp['id_vcns']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="myModalLabel">ELIMINAR PERMISO</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <form method="POST" action="historico_vacaciones.php?id=<?php echo $arrayp['id_vcns']; ?>">
                                            <div class="row form-group">
                                                <div class="col-sm-12 d-flex justify-content-center">
                                                    <label class="control-label text-center" style="position:relative; top:7px;">¿Estás seguro de eliminar éste permiso?</label>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-sm-2">
                                                    <label class="control-label" style="position:relative; top:7px;">Usuario:</label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="user" value="<?php echo $arrayp['user']; ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col-sm-2">
                                                    <label class="control-label" style="position:relative; top:7px;">Estatus:</label>
                                                </div>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="estatus" value="<?php echo $arrayp['estatus']; ?>" disabled>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-right:auto">Cancelar</button>
                                    <button type="submit" name="editar" class="btn btn-success">Eliminar</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php echo "<tr><td>" .$arrayp['user']."</td>";
            echo "<td>".strftime("%d/%b/%Y",strtotime($arrayp['fecha_registro']))."</td>";
            echo "<td>" .$arrayp['num_dias_a_difrutar']."</td>";
            if($arrayp['dias_a_difrutar_del']>0) {
                echo "<td>".strftime("%d/%b/%Y",strtotime($arrayp['dias_a_difrutar_del']))."</td>";
                    }else{
                    echo "<td> </td>"; 
                    } 
            if($arrayp['dias_a_difrutar_al']>0) { 
                echo "<td>".strftime("%d/%b/%Y",strtotime($arrayp['dias_a_difrutar_al']))."</td>";
                    }else{
                        echo "<td> </td>";
                        }
            if($arrayp['regreso']>0) { 
                echo "<td>".strftime("%d/%b/%Y",strtotime($arrayp['regreso']))."</td>";
                    }else{
                        echo "<td> </td>";
                        }
            echo "<td>".$arrayp['estatus']."</td>";
            echo "<td><a href='#delete_".$arrayp['id_vcns']."' class='btn btn-danger btn-sm' data-toggle='modal'><i class='fas fa-trash-alt'></td></tr>";

            }while($arrayp=$permisos->fetch_assoc());
            }else{ 
             echo "<tr><td colspan=12>No hay ninguna solicitud de vacaciones registrada</td></tr>";
            }?>
                </tbody>

            </table>
        </div>
    </div>

</body>

</html>

<!-- -----------------TABLA----------------- -->
<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous">
</script>
<!-- DATATABLES -->
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js">
</script>
<!-- BOOTSTRAP -->
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js">
</script>
<!-- TABLE -->
<script>
    $(document).ready(function() {
        $('#tablax').DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "No hay ningún permiso registrado",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            scrollY: 400,
            scrollCollapse: true,
            lengthMenu: [
                [5, 10, -1],
                [5, 10, "Todo"]
            ],
            "aaSorting": [],
        });
    });

</script>
