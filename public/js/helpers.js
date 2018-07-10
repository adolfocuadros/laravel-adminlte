var loading = function() {
  $('.sgloading').show();
};

var endLoading = function() {
  $('.sgloading').hide();
};

var showCallout = function(message, tipo, title) {
  tipo = (tipo === undefined) ? 'success' : tipo;
  title = (title === undefined) ? '¡ Error !' : title;

  callout = $('#sg-callout');
  callout.find('p').html(message);

  if(tipo == 'error') {
    callout.attr('class', 'callout callout-danger');
    callout.find('h4').text(title);
    callout.fadeIn().fadeOut().fadeIn().fadeOut().fadeIn();
  } else {
    callout.find('h4').text('Felicitaciones!');
    callout.attr('class', 'callout callout-success');
    callout.fadeIn().delay(5000).fadeOut(800);
  }
}

var hideCallout = function() {
  $('#sg-callout').fadeOut();
};

var dataToLi = function(data) {
  var gen = '<ul>';

  $.each(data, function(key, value) {
    gen += '<li>'+value[0]+'</li>';
  });
  gen += '</ul>';
  return gen;
};

var restSave = function(resource, data, success, error) {
  success = (success==undefined) ? function() {} : success;
  error = (error==undefined) ? function() {} : error;

  resource.save(data).then(function (response) {
    showCallout('Se ha creado correctamente');
    success(response);
  }, function (response) {
    if(response.status == 422) {
      var json = response.json();
      console.log(json);
      showCallout(dataToLi(json),'error');
    } else {
      showCallout('Ha ocurrido un error al procesar su solicitud, por favor vuelva a intentarlo.<br>Si el error persiste, por favor contacte a Soporte Especializado','error');
    }
    error(response);

  });
};

$('.sg-close').click(function() {
  console.log('ha hecho click');
  $('#sg-callout').hide();
});

var sgConfirm = function(titulo, msg, button, success, type) {
  type = (typeof type== 'undefined') ? 'info' : type;
  swal({
    title: titulo,
    html: msg,
    type: type,
    showCancelButton: true,
    confirmButtonText: button == null ? 'Aceptar' : button,
    closeOnConfirm: false
  }).then(function(result){
    if (result.value) {
      success();
    }
  });
};

var groupBy = function(data, by) {
  var arr = {};
  $.each(data, function(index, value) {
    var group = value[by].split(' ')[0];
    if(typeof arr[group] == 'undefined') {
      arr[group] = [];
    }
    arr[group].push(value);
  });
  return arr;
};

var sgError = function(response) {
  if(response.status == 422) {
    var json = response.body;
    if(typeof json.errors != 'undefined') {
      var data = '<ul>';
      $.each(json.errors, function(index, value) {
        data += '<li class="text-left">'+value+'</li>';
      });
      data += '</ul>';
      swal({
        title: 'Error',
        html: data
      });
    } else {
      swal('Error:',json.message);
    }
  } else {
    if(response.status == 401) {
      swal('Error 401','No tiene permisos suficientes para ejecutar esta acción.', 'error');
    } else {
      swal('Error','Ha ocurrido un error: ' + response.status, 'error');
    }
  }
};

var sgSuccess = function(msg, url) {
  swal({
    title: "Congratulations !",
    text: msg,
    type: "success",
    showCancelButton: false,
    confirmButtonText: "Continue",
    closeOnConfirm: false
  }).then(function(){
    if(url) {
      window.location.href = url;
    } else {
      window.location.reload();
    }
  });
};

function number_format(amount, decimals) {

  amount += ''; // por si pasan un numero en vez de un string
  amount = parseFloat(amount.replace(/[^0-9\.]/g, '')); // elimino cualquier cosa que no sea numero o punto

  decimals = decimals || 0; // por si la variable no fue fue pasada

  // si no es un numero o es igual a cero retorno el mismo cero
  if (isNaN(amount) || amount === 0)
    return parseFloat(0).toFixed(decimals);

  // si es mayor o menor que cero retorno el valor formateado como numero
  amount = '' + amount.toFixed(decimals);

  var amount_parts = amount.split('.'),
    regexp = /(\d+)(\d{3})/;

  while (regexp.test(amount_parts[0]))
    amount_parts[0] = amount_parts[0].replace(regexp, '$1' + ',' + '$2'); // se quito la coma

  return amount_parts.join('.');
}

function valruc(valor) {
  valor = trim(valor);
  if (valor.length == 8) {
    suma = 0;
    for (i = 0; i < valor.length - 1; i++) {
      digito = valor.charAt(i) - '0';
      if (i == 0) suma += (digito * 2);
      else suma += (digito * (valor.length - i))
    }
    resto = suma % 11;
    if (resto == 1) resto = 11;
    if (resto + (valor.charAt(valor.length - 1) - '0') == 11) {
      return true;
    }
  } else if (valor.length == 11) {
    suma = 0;
    x = 6;
    for (i = 0; i < valor.length - 1; i++) {
      if (i == 4) x = 8;
      digito = valor.charAt(i) - '0';
      x--;
      if (i == 0) suma += (digito * x);
      else suma += (digito * x)
    }
    resto = suma % 11;
    resto = 11 - resto;

    if (resto >= 10) resto = resto - 10;
    if (resto == valor.charAt(valor.length - 1) - '0') {
      return true;
    }
  }
  return false;
}

function trim(cadena) {
  cadena2 = "";
  len = cadena.length;
  for (var i = 0; i <= len; i++)
    if (cadena.charAt(i) != " ") {
      cadena2 += cadena.charAt(i);
    }
  return cadena2;
}

function validateFileSize(element, e) {
  files = $(element)[0].files;

  //Comprobando si existe al menos un archivo
  if(files.length > 0) {
    file_size = files[0].size;

    if(file_size > max_file_size) {
      if(e != null) {
        e.preventDefault();
      }
      size_msg = parseFloat((max_file_size / 1024) / 1024).toFixed(2);
      swal('Error:', 'The maximum accepted file size is ' + size_msg + ' Mb, please change and try again.', 'error');
      return false;
    }
  }
  return true;
}