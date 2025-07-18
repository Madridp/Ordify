/**
 * Activar tooltips en todo el sitio
 */
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"], [data-toggle="tooltip"]'))
var tooltipList        = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

/**
 * 
 * Utilizado para implementar en peticiones http asíncronas con fetch API
 */
function beforeSend() {
  return fetch.apply(this, arguments);
}

/**
 * Solo para jQuery
 */
$(document).ready(function() {

  // Toast para notificaciones
  //toastr.warning('My name is Inigo Montoya. You killed my father, prepare to die!');

  // Waitme
  //$('body').waitMe({effect : 'orbit'});

  function init_selec2() {
    $('.select2-basic').select2();
    $('.select2-multiple').select2();
  }
  init_selec2();

  /**
   * Alerta para confirmación de acciones en links
   * Al confirmar redirecciona al enlace en el atributo href
   */
  $('body').on('click', '.confirmar', function(e) {
    e.preventDefault();

    let url = $(this).attr('href'),
    ok      = confirm('¿Estás seguro?');

    // Redirección a la URL del enlace
    if (ok) {
      window.location = url;
      return true;
    }
    
    console.log('Acción cancelada.');
    return true;
  });

  /**
   * Función para agregar un loader al elemento pasado en el segundo
   * parametro, false es para generarlo y true para esconderlo
   * @param {bool} hide 
   * @param {string} element 
   */
  function wait(hide = false, element = 'body') {
    $(element).waitMe(hide ? 'hide' : '');
  }

  feather.replace()

  /**
   * Función para generar la gráfica de pedidos anuales
   * en el dashboard de Ordify
   */
  function draw_pedidos_chart() {
    let hook = 'bee_hook',
    action   = 'get',
    wrapper  = $('#myChartWrapper'),
    chart    = $('#chart_pedidos_anual'),
    year     = new Date();
    year     = year.getFullYear(),
    meses    = $('#meses', wrapper).val();

    if (chart.length === 0) return;
    
    // AJAX
    $.ajax({
      url: 'ajax/chart_pedidos',
      type: 'post',
      dataType: 'json',
      cache: false,
      data : {hook, action, year, meses},
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {

        // Dibujar gráfica
        let labels = [],
        dataset    = [],
        ctx        = $('#chart_pedidos_anual');

        // Mapeando nuestros arrays
        res.data.map((row) => {
          labels.push(row.mes + ' ' + row.año);
          dataset.push(row.total);
        });

        // Remover el canvas y reinsertar
        ctx.remove();
        wrapper.append('<canvas class="my-4 w-100" id="chart_pedidos_anual" height="480"></canvas>');

        let chart = new Chart($('#chart_pedidos_anual'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              {
                data: dataset,
                lineTension: 0,
                backgroundColor: '#007bf1',
                borderColor: '#007bff',
                borderWidth: 2,
                pointBackgroundColor: '#007bff'
              }
            ]
          },
          options: {
            scales: {
              yAxes: [{
                display: true,
                ticks: {
                  suggestedMin: 0,
                  beginAtZero: true
                }
              }],
              xAxes: [{
                ticks: {
                  autoSkip: false,
                  maxRotation: 90,
                  minRotation: 45
                }
              }]
            },
            legend: {
              display: false
            }
          }
        });
        chart.update();
      } else {
        wrapper.remove();
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      wrapper.remove();
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  draw_pedidos_chart();
  $('body').on('click', '.recargar_myChart', draw_pedidos_chart);

  /**
   * Función para dibujar la tabla o gráfica de inversión por año
   * en el dashboard de Ordify
   */
  function draw_inversion_anual_chart() {
    let hook = 'bee_hook',
    action   = 'get',
    wrapper  = $('#inversion_anual'),
    chart    = $('#chart_inversion_anual'),
    year     = new Date();
    year     = year.getFullYear(),
    year     = 12;

    if (chart.length === 0) return;
    
    // AJAX
    $.ajax({
      url: 'ajax/chart_inversion',
      type: 'post',
      dataType: 'json',
      cache: false,
      data : { hook, action, year },
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {

        // Dibujar gráfica
        let labels = [],
        dataset    = [],
        ctx        = $('#chart_inversion_anual');

        // Mapeando nuestros arrays
        res.data.map((row) => {
          labels.push(row.mes + ' ' + row.año);
          dataset.push(row.total);
        });

        // Remover el canvas y reinsertar
        ctx.remove();
        wrapper.append('<canvas class="my-4 w-100" id="chart_inversion_anual" height="480"></canvas>');

        let chart = new Chart($('#chart_inversion_anual'), {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              {
                data: dataset,
                lineTension: 0,
                backgroundColor: '#00bb00',
                borderColor: '#00aa00',
                borderWidth: 2,
                pointBackgroundColor: '#00bb00'
              }
            ]
          },
          options: {
            scales: {
              yAxes: [{
                ticks: {
                  suggestedMin: 0,
                  beginAtZero: true,
                  userCallback: function(value, index, values) {
                    // Convert the number to a string and splite the string every 3 charaters from the end
                    value = value.toString();
                    value = value.split(/(?=(?:...)*$)/);
        
                    // Convert the array to a string and format the output
                    value = value.join(',');
                    return '$' + value;
                  }
                }
              }],
              xAxes: [{
                ticks: {
                  autoSkip: false,
                  maxRotation: 90,
                  minRotation: 45
                }
              }]
            },
            legend: {
              display: false
            }
          }
        });
        chart.update();
      } else {
        wrapper.remove();
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      wrapper.remove();
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  draw_inversion_anual_chart();
  $('body').on('click', '.recargar_chart_inversion_anual', draw_inversion_anual_chart);
  
  /**
   * Función para buscar un termino en la 
   * barra superior de búsqueda
   * @param {evento} e 
   * @returns 
   */
  function search_term(e) {
    e.preventDefault();

    let term      = $(this).val(),
    main_wrapper  = $('.main_wrapper_content'),
    wrapper       = $('.wrapper_dynamic_content'),
    hook          = 'bee_hook',
    action        = 'get',
    formData      = new FormData();

    formData.append('hook', hook);
    formData.append('action', action);
    formData.append('term', term);
    
    if (term.length < 1) {
      wrapper.html('');
      return;
    }

    /**
     * Petición para obtener los resultados de la busqueda
     */
    beforeSend('ajax/serch_terms',
    {
      method: 'post',
      body: formData
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        wrapper.html(res.data);
      } else {
        toastr.error(res.msg);
      }
  
      main_wrapper.waitMe('hide');
    })
    .catch(err => {
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('.search-box').on('keyup', search_term);
  
  /**
   * Función para actualizar la cantidad de un producto
   * solicitado en un pedido, solo disponible si 
   * el estado del pedido es borrador
   */
  function do_update_cantidad(e) {
    e.preventDefault();

    let input = $(this),
    original  = input.closest('td').find('[name="cantidad_original"]').val(),
    cantidad  = input.val(),
    id        = input.closest('tr').data('id'),
    data      = new FormData();

    data.append('hook', 'bee_hook');
    data.append('action', 'put'),
    data.append('id', id);
    data.append('csrf', Bee.csrf);
    data.append('cantidad', cantidad);

    if (cantidad == original) return; // no hubo cambios

    // Guardar cambios en la base de datos
    beforeSend('ajax/do_update_cantidad_producto_pedido', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        do_get_data_pedido();
        do_get_data_productos();
      } else {
        input.val(original);
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      wait(true);
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('body').on('blur', '.do_update_cantidad', do_update_cantidad);

  /**
   * Función para actualizar la cantidad de un producto
   * recibido, solo disponible para un pedido
   * en estado de procesando
   */
  function do_update_recibidos(e) {
    e.preventDefault();

    let btn  = $(this), // ahora es un button
    input      = btn.closest('.wrapper_update_recibidos').find('[name="recibidos"]'),
    total      = input.closest('.wrapper_update_recibidos').find('[name="cantidad_original_total"]').val(),
    original   = input.closest('.wrapper_update_recibidos').find('[name="cantidad_original_recibidos"]').val(),
    danados    = input.closest('.wrapper_update_recibidos').find('[name="cantidad_original_danados"]').val(),
    cancelados = input.closest('.wrapper_update_recibidos').find('[name="cantidad_original_cancelados"]').val(),
    tipo       = input.closest('.wrapper_update_recibidos').find('[name="tipo_movimiento"]').val(),
    recibidos  = parseInt(original) + parseInt(danados) + parseInt(cancelados),
    cantidad   = input.val(),
    id         = input.closest('.card').data('id'),
    data       = new FormData();

    // Filtrar el valor
    cantidad   = cantidad.length == '' ? 0 : parseInt(cantidad);

    data.append('hook', 'bee_hook');
    data.append('action', 'put'),
    data.append('id', id);
    data.append('csrf', Bee.csrf);
    data.append('cantidad', cantidad);
    data.append('tipo', tipo);

    // Si no hay cambios no realizar el ajuste
    if (cantidad == 0) {
      input.val(0);
      return;
    }

    if ((parseInt(cantidad) + parseInt(recibidos)) > parseInt(total)) {
      if (!confirm('¿Estás seguro que se recibieron más piezas que las solicitadas?')) {
        input.val(0);
        return;
      }
    }; // no hubo cambios

    // Guardar cambios en la base de datos
    beforeSend('ajax/do_update_recibidos', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        do_get_data_pedido();
        do_get_data_productos();
      } else {
        input.val(0);
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      wait(true);
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('body').on('click', '.do_update_recibidos', do_update_recibidos);

  /**
   * Función para cargar un pedido y su información
   * incluyendo el html para insertar en algunas secciones
   * de la plataforla Ordify
   */
  function do_get_pedido() {
    let wrapper_pedido = $('.wrapper_pedido');

    if (wrapper_pedido.length === 0) return;

    wrapper_pedido.waitMe();
    setTimeout(() => {
      do_get_data_pedido();
      do_get_data_proveedor();
      do_get_data_productos();
      wrapper_pedido.waitMe('hide');
    }, 500);
  }
  do_get_pedido();

  function do_get_data_pedido() {
    var wrapper_pedido = $('.wrapper_pedido'),
    wrapper            = $('.wrapper_data_pedido'),
    id_pedido          = wrapper_pedido.data('id'),
    csrf               = wrapper_pedido.data('csrf'),
    hook               = 'bee_hook',
    action             = 'get',
    data               = new FormData();

    if (wrapper.length === 0) return;

    data.append('id'    , id_pedido);
    data.append('hook'  , hook);
    data.append('action', action);
    data.append('csrf'  , csrf);

    // Hacer petición para obtener el pedido
    beforeSend('ajax/do_get_data_pedido',{
      method: 'post',
      body  : data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        wrapper.html(res.data.html)
      } else {
        wrapper.html(res.msg);
      }
    })
    .catch(err => {
      wrapper.html('Hubo un error al cargar el módulo, intenta más tarde.');
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }

  function do_get_data_proveedor() {
    var wrapper_pedido = $('.wrapper_pedido'),
    wrapper            = $('.wrapper_data_proveedor'),
    id_pedido          = wrapper_pedido.data('id'),
    csrf               = wrapper_pedido.data('csrf'),
    hook               = 'bee_hook',
    action             = 'get',
    data               = new FormData();

    if (wrapper.length === 0) return;

    data.append('id'    , id_pedido);
    data.append('hook'  , hook);
    data.append('action', action);
    data.append('csrf'  , csrf);

    // Hacer petición para obtener el pedido
    beforeSend('ajax/do_get_data_proveedor',{
      method: 'post',
      body  : data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        wrapper.html(res.data.html)
      } else {
        wrapper.html(res.msg);
      }
    })
    .catch(err => {
      wrapper.html('Hubo un error al cargar el módulo, intenta más tarde.');
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }

  function do_get_data_productos() {
    var wrapper_pedido = $('.wrapper_pedido'),
    wrapper            = $('.wrapper_data_productos'),
    id_pedido          = wrapper_pedido.data('id'),
    csrf               = wrapper_pedido.data('csrf'),
    hook               = 'bee_hook',
    action             = 'get',
    data               = new FormData();

    if (wrapper.length === 0) return;

    data.append('id'    , id_pedido);
    data.append('hook'  , hook);
    data.append('action', action);
    data.append('csrf'  , csrf);

    // Hacer petición para obtener el pedido
    beforeSend('ajax/do_get_data_productos',{
      method: 'post',
      body  : data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        wrapper.html(res.data.html)
      } else {
        wrapper.html(res.msg);
      }
    })
    .catch(err => {
      wrapper.html('Hubo un error al cargar el módulo, intenta más tarde.');
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }

  /**
   * Función para agregar un nuevo producto
   * a un pedido en existencia
   * funciona simplemente con un select input que mandará el Id del producto y su nombre
   * como valor a insertar, esto incluirá el valor definido de la variante y producto al mismo tiempo
   */
  function do_add_producto_a_pedido(e) {
    e.preventDefault();

    let id    = $('#id_producto', this).val(),
    id_pedido = $('[name="id_pedido"]', this).val(),
    option    = $('select option:selected', this),
    nombre    = option.html(),
    variante  = option.data('variante'),
    csrf      = $('[name="csrf"]', this).val(),
    data      = new FormData();

    data.append('hook'      , 'bee_hook');
    data.append('action'    , 'post');
    data.append('id'        , id);
    data.append('id_pedido' , id_pedido);
    data.append('nombre'    , nombre);
    data.append('variante'  , variante);
    data.append('csrf'      , csrf);

    // Petición http para guardar el nuevo registro en la base de datos
    wait();
    beforeSend('ajax/do_add_producto_a_pedido', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200 || res.status === 201) {
        toastr.success(res.msg);
        do_get_data_pedido();
        do_get_data_productos();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      wait(true);
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('body').on('submit', '.do_add_producto_a_pedido', do_add_producto_a_pedido);

  /**
   * Función para borrar algún producto o item de un pedido en curso
   * basado en el ID del registro en tabla pedidos_productos
   */
  function do_delete_producto_de_pedido(e) {
    e.preventDefault();

    let button = $(this),
    id         = button.data('id'),
    csrf       = Bee.csrf,
    data       = new FormData();

    data.append('hook'  , 'bee_hook');
    data.append('action', 'delete');
    data.append('id'    , id);
    data.append('csrf'  , csrf);

    if (!confirm('¿Estás seguro?')) return;

    // Crear petición para borrar el elemento de la base de datos
    wait();
    beforeSend('ajax/do_delete_producto_de_pedido', {
      method: 'post',
      body  : data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        do_get_data_pedido();
        do_get_data_productos();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      wait(true);
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('body').on('click', '.do_delete_producto_de_pedido', do_delete_producto_de_pedido);

  /**
   * Función para actualizar el proveedor de un pedido en curso
   */
  function do_update_proveedor_pedido(e) {
    e.preventDefault();

    let select = $(this),
    id         = select.val(),
    id_pedido  = select.data('id'),
    data       = new FormData();

    data.append('hook'     , 'bee_hook');
    data.append('action'   , 'put'),
    data.append('id'       , id);
    data.append('id_pedido', id_pedido);
    data.append('csrf'     , Bee.csrf);

    // Guardar cambios en la base de datos
    beforeSend('ajax/do_update_proveedor_pedido', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        do_get_data_proveedor();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      wait(true);
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('body').on('change', '.do_update_proveedor_pedido', do_update_proveedor_pedido);

  /**
   * Función para manipular el campo de edición de notas de un pedido
   */
  function do_open_update_notas_pedido(e) {
    e.preventDefault();

    let form = $('form.do_update_notas_pedido'),
    reset    = $('button[type="reset"]', form),
    guardar  = $('button[type="submit"]', form),
    textarea = $('textarea', form);

    // Abrir el formulario
    textarea.attr('disabled', false);
    textarea.closest('div').show();

    // Cambiar botones a mostrar
    reset.show();
    guardar.show();
    $(this).fadeOut();
  }
  $('body').on('click', '.do_open_update_notas_pedido', do_open_update_notas_pedido);

  /**
   * Función para cancelar la edición de las notas de un pedido
   * funciona para cerrar y regresar la vista a la normalidad escondiendo
   * inputs y botones adicionales del formulario
   */
  function do_reset_update_notas_pedido(e) {
    e.preventDefault();

    let form = $('form.do_update_notas_pedido'),
    editar   = $('.do_open_update_notas_pedido', form),
    guardar  = $('button[type="submit"]', form),
    textarea = $('textarea', form);

    // Cerrar el formulario
    form.trigger('reset');
    textarea.attr('disabled', true);
    textarea.closest('div').hide();

    // Cambiar botones a mostrar
    editar.fadeIn();

    guardar.hide();
    $(this).hide();
  }
  $('body').on('click', '.do_reset_update_notas_pedido', do_reset_update_notas_pedido);

  /**
   * Función para guardar los cambios en las notas de un pedido
   * en la base de datos
   */
  function do_save_notas_pedido(e) {
    e.preventDefault();

    let form = $('form.do_update_notas_pedido'),
    data     = new FormData(form.get(0));

    data.append('hook', 'bee_hook');
    data.append('action', 'put');

    // Guardar cambios en la base de datos
    beforeSend('ajax/do_save_notas_pedido', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        do_get_data_proveedor();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      wait(true);
      toastr.error('Hubo un error en la petición', '¡Upss!');
    });
  }
  $('body').on('submit', '.do_update_notas_pedido', do_save_notas_pedido);

  /**
   * Recalcula el total de un pedido y todos sus cambios de existir
   */
  function do_recalcular_precios(e) {
    e.preventDefault();

    let btn = $(this),
    id      = btn.data('id'),
    action  = 'put',
    hook    = 'bee_hook',
    data    = new FormData();

    data.append('action'   , action),
    data.append('hook'     , hook);
    data.append('id'       , id);
    data.append('csrf'     , Bee.csrf);

    if (!confirm('¿Estás seguro?')) return false;

    // Guardar cambios en la base de datos
    beforeSend('ajax/do_recalcular_precios', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        do_get_data_productos();
      } else {
        toastr.error(res.msg, '¡Upss!');
      }
      wait(true);
    })
    .catch(err => {
      toastr.error('Hubo un error en la petición', '¡Upss!');
      wait(true);
    });
  }
  $('body').on('click', '.recalcular_precios', do_recalcular_precios);

  $('#termino').on('keydown', (e) => {
    if (e.code == 'Enter') {
      do_buscar_productos(e);
    }
  });

  /**
   * Versión mejorada del buscador de productos ahora totalmente independiente
   */
  $('.do_buscar_productos').on('click', do_buscar_productos);
  function do_buscar_productos(e) {
    e.preventDefault();

    var input = $('#termino'),
    termino   = input.val(),
    hook      = 'bee_hook',
    action    = 'post',
    csrf      = Bee.csrf,
    form      = $('.do_add_producto_a_pedido'),
    select    = $('#id_producto', form),
    msg       = $('.wrapper_msg'),
    opciones  = '';

    if (termino.length == 0) {
      form.fadeOut(500);
      select.html('');
      return;
    }

    $.ajax({
      url: 'ajax/do_buscar_productos',
      type: 'post',
      dataType: 'json',
      cache: false,
      data: { csrf, hook, action, termino},
      beforeSend: () => {
        msg.html('Buscando...');
      }
    }).done(res => {
      if (res.status === 200) {
        //<option value="%s" data-variante="%s">%s - %s</option>', $sp->id, $sp->variante, $sp->nombre, $sp->corte)
        select.html('');
        $.each(res.data, function(i, p) {
          opciones += '<option value="'+p.id+'" data-variante="'+p.variante+'">'+p.nombre+' - '+p.corte+'</option>';
        });
        select.html(opciones);
        form.fadeIn(500);
        init_selec2();
      } else {
        toastr.error(res.msg, '¡Upss!');
        form.fadeOut(500);
        select.html('');
      }
    }).fail(err => {
      toastr.error('Hubo un error en la petición, intenta más tarde.', '¡Upss!');
      input.attr('disabled', true);
      form.fadeOut(500);
      select.html('');
    }).always(() => {
      msg.html('');
    });
  }

  /**
   * Optimiza las imágenes de productos para reducir su peso o espacio en disco
   * @param {Event} e 
   */
  function do_optimizar_imagenes(e) {
    e.preventDefault();

    let btn  = $(this),
    original = btn.html(),
    loading  = '<i class="fas fa-spin fa-sync fa-fw"></i> Procesando imágenes...',
    done     = '<i class="fas fa-check fa-fw"></i> Imágenes procesadas con éxito',
    data     = new FormData();

    data.append('hook'   , 'bee_hook');
    data.append('action' , 'post');
    data.append('csrf'   , Bee.csrf);

    // Petición http para procesar imágenes
    wait();

    // Cargador inicializado
    btn.html(loading);
    btn.attr('disabled', true);

    beforeSend('ajax/do_optimizar_imagenes', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        btn.html(done);

        // Regresar a su estado original después de 3 segundos
        setTimeout(() => {
          btn.html(original);
          btn.attr('disabled', false);
        }, 3000);

      } else {
        toastr.error(res.msg, '¡Upss!');
        btn.html(original);
        btn.attr('disabled', true);
      }
      wait(true);
    })
    .catch(err => {
      toastr.error('Hubo un error en la petición', '¡Upss!');
      btn.html(original);
      btn.attr('disabled', false);
      wait(true);
    });
  }
  $('.do_optimizar_imagenes').on('click', do_optimizar_imagenes);

  /**
   * Actualiza el precio de todos los productos que coincidan con los valores ingresados
   * @param {Event} e 
   */
   function do_update_prices(e) {
    e.preventDefault();

    console.log('ejecutando...');

    let form = $(this),
    btn      = $('button', form),
    original = btn.html(),
    loading  = '<i class="fas fa-spin fa-sync fa-fw"></i> Procesando...',
    done     = '<i class="fas fa-check fa-fw"></i> Precios actualizados con éxito',
    data     = new FormData(form.get(0));

    data.append('hook'   , 'bee_hook');
    data.append('action' , 'post');
    data.append('csrf'   , Bee.csrf);

    // Agregar cargador
    wait();

    // Actualizar contenido del botón
    btn.html(loading);
    btn.attr('disabled', true);

    beforeSend('ajax/do_update_prices', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);
        $('#precio_actual', form).val('');
        $('#precio_nuevo', form).val('');
        btn.html(done);

        // Regresar a su estado original después de 3 segundos
        setTimeout(() => {
          btn.html(original);
          btn.attr('disabled', false);
        }, 3000);

      } else {
        toastr.error(res.msg, '¡Upss!');
        btn.html(original);
        btn.attr('disabled', false);
      }
      wait(true);
    })
    .catch(err => {
      toastr.error('Hubo un error en la petición', '¡Upss!');
      btn.html(original);
      btn.attr('disabled', false);
      wait(true);
    });
  }
  $('#do_update_prices_form').on('submit', do_update_prices);

  /**
   * Registrar el pago de un pedido
   * @version 1.3.0
   */
  $('body').on('submit', '.do_pay_pedido', do_pay_pedido);
  function do_pay_pedido(e) {
    e.preventDefault();

    let form = $(this),
    btn      = $('button', form),
    original = btn.html(),
    loading  = '<i class="fas fa-spin fa-sync fa-fw"></i> Procesando...',
    data     = new FormData(form.get(0));

    data.append('hook'   , 'bee_hook');
    data.append('action' , 'post');
    data.append('csrf'   , Bee.csrf);

    // Actualizar contenido del botón
    btn.html(loading);
    btn.attr('disabled', true);

    wait();

    // Petición asíncrona
    beforeSend('ajax/do_pay_pedido', {
      method: 'post',
      body: data
    })
    .then(res => res.json())
    .then(res => {
      if (res.status === 200) {
        toastr.success(res.msg);

        // Regresar a su estado original después de 3 segundos
        setTimeout(() => {
          btn.html(original);
          btn.attr('disabled', false);
          wait(true);
        }, 2000);

      } else {
        toastr.error(res.msg, '¡Upss!');
        btn.html(original);
        btn.attr('disabled', false);
        wait(true);
      }
    })
    .catch(err => {
      toastr.error('Hubo un error en la petición', '¡Upss!');
      btn.html(original);
      btn.attr('disabled', false);
      wait(true);
    });
  }
  
  /**
   * Función para generar la gráfica de los productos más vendidos
   * en el dashboard de Ordify
   */
  function draw_chart_top_products() {
    let hook = 'bee_hook',
    action   = 'get',
    wrapper  = $('#wrapper_chart_top_products'),
    chart    = $('#chart_top_products'),
    year     = new Date();
    year     = year.getFullYear();

    if (chart.length === 0) return;
    
    // AJAX
    $.ajax({
      url: 'ajax/chart_top_products',
      type: 'post',
      dataType: 'json',
      cache: false,
      data : {hook, action, year},
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {

        // Dibujar gráfica
        let labels = [],
        dataset    = [],
        ctx        = $('#chart_top_products');

        // Mapeando nuestros arrays
        res.data.map((row) => {
          console.log(row)
          labels.push(row.pp_producto);
          dataset.push(row.total);
        });

        let chart = new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              {
                data: dataset,
                lineTension: 0,
                backgroundColor: '#5f27cd',
                borderColor: '#341f97',
                borderWidth: 1,
                pointBackgroundColor: '#341f97'
              }
            ]
          },
          options: {
            scales: {
              yAxes: [{
                display: true,
                ticks: {
                  suggestedMin: 0,
                  beginAtZero: true
                }
              }],
              xAxes: [{
                ticks: {
                  autoSkip: false,
                  maxRotation: 90,
                  minRotation: 90
                }
              }]
            },
            legend: {
              display: false
            }
          }
        });
      } else {
        wrapper.remove();
        toastr.error(res.msg, '¡Upss!');
      }
    }).fail(function(err) {
      wrapper.remove();
      toastr.error('Hubo un error en la petición', '¡Upss!');
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  draw_chart_top_products();
  $('body').on('click', '.recargar_chart_top_products', draw_chart_top_products);

  /**
   * Función para cargar la lista de productos más vendidos
   * en el dashboard de Ordify
   */
  function recargar_top_products() {
    let hook = 'bee_hook',
    action   = 'get',
    wrapper  = $('#wrapper_top_products');

    if (wrapper.length === 0) return;
    
    // AJAX
    $.ajax({
      url: 'ajax/recargar_top_products',
      type: 'post',
      dataType: 'json',
      cache: false,
      data : {hook, action},
      beforeSend: function() {
        wrapper.waitMe();
      }
    }).done(function(res) {
      if(res.status === 200) {
        wrapper.html(res.data);
      } else {
        toastr.error(res.msg, '¡Upss!');
        wrapper.html(res.msg);
      }
    }).fail(function(err) {
      toastr.error('Hubo un error en la petición', '¡Upss!');
      wrapper.remove();
    }).always(function() {
      wrapper.waitMe('hide');
    })
  }
  recargar_top_products();
  $('body').on('click', '.recargar_top_products', recargar_top_products);
});