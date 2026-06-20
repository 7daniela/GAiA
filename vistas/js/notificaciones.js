$(document).ready(function() {

    // Función para cargar notificaciones en la campana
    function cargarNotificacionesCampana() {
        var idUsuario = $("#idUsuarioGlobalEncabezado").val();
        if (!idUsuario) return;

        var datos = new FormData();
        datos.append("obtenerNotificaciones", "ok");
        datos.append("idUsuario", idUsuario);

        $.ajax({
            url: "ajax/notificaciones.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function(respuesta) {
                // Actualizar badge
                if (respuesta.noLeidas > 0) {
                    $("#badgeNotificaciones").text(respuesta.noLeidas).show();
                } else {
                    $("#badgeNotificaciones").hide();
                }

                $("#headerNotificaciones").text(respuesta.noLeidas + " Notificaciones no leídas");

                // Actualizar lista
                var html = "";
                if (respuesta.notificaciones.length > 0) {
                    respuesta.notificaciones.forEach(function(notif) {
                        var icono = "fa-bell";
                        if (notif.tipo == "mensaje") icono = "fa-envelope";
                        if (notif.tipo == "alerta") icono = "fa-exclamation-triangle text-warning";
                        if (notif.tipo == "sistema") icono = "fa-cog";
                        
                        var bgClass = notif.leido == 0 ? "bg-light" : "";
                        var titleText = notif.titulo.length > 25 ? notif.titulo.substring(0,25)+"..." : notif.titulo;
                        
                        html += `
                        <a href="notificaciones" class="dropdown-item ${bgClass}">
                            <i class="fas ${icono} mr-2"></i> ${titleText}
                        </a>
                        <div class="dropdown-divider"></div>
                        `;
                    });
                } else {
                    html = '<span class="dropdown-item text-center text-muted text-sm py-2">No hay notificaciones</span>';
                }

                $("#contenedorNotificacionesDropdown").html(html);
            }
        });
    }

    // Cargar al inicio si existe el usuario
    if ($("#idUsuarioGlobalEncabezado").length > 0) {
        cargarNotificacionesCampana();
        // Polling cada 60 segundos
        setInterval(cargarNotificacionesCampana, 60000);
    }

    // Al hacer clic en la campana, también recargamos por si acaso
    $("#campanaNotificaciones").on("click", function() {
        cargarNotificacionesCampana();
    });

    // MARCAR COMO LEÍDA / NO LEÍDA EN LA TABLA
    $(document).on("click", ".btnMarcarLeida", function() {
        var idNotificacion = $(this).attr("idNotificacion");
        var estado = $(this).attr("estado"); // "leer" o "noleer"

        var datos = new FormData();
        datos.append("marcarLeida", "ok");
        datos.append("idNotificacion", idNotificacion);
        datos.append("accion", estado);

        $.ajax({
            url: "ajax/notificaciones.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                if (respuesta == '"ok"') {
                    window.location = "notificaciones";
                }
            }
        });
    });

    // ELIMINAR NOTIFICACION
    $(document).on("click", ".btnEliminarNotificacion", function() {
        var idNotificacion = $(this).attr("idNotificacion");

        Swal.fire({
            title: '¿Está seguro de borrar la notificación?',
            text: "¡Si no lo está puede cancelar la acción!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Sí, borrar notificación!'
        }).then(function(result){
            if (result.value) {
                var datos = new FormData();
                datos.append("eliminarNotificacion", "ok");
                datos.append("idNotificacion", idNotificacion);

                $.ajax({
                    url: "ajax/notificaciones.ajax.php",
                    method: "POST",
                    data: datos,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(respuesta) {
                        if (respuesta == '"ok"') {
                            window.location = "notificaciones";
                        }
                    }
                });
            }
        });
    });

    // MARCAR TODAS COMO LEIDAS
    $(document).on("click", "#btnMarcarTodasLeidas", function() {
        var idUsuario = $("#idUsuarioGlobal").val();
        
        if(!idUsuario) return;

        var datos = new FormData();
        datos.append("marcarTodasLeidas", "ok");
        datos.append("idUsuario", idUsuario);

        $.ajax({
            url: "ajax/notificaciones.ajax.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function(respuesta) {
                if (respuesta == '"ok"') {
                    Swal.fire({
                        icon: "success",
                        title: "Todas las notificaciones han sido marcadas como leídas",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.value) {
                            window.location = "notificaciones";
                        }
                    });
                }
            }
        });
    });

});
