{{--
'config' => [
    'id' => '#NewBanner',
    'structure' => [], //Array de estructura
    'afterLoadEdit' => 'alferLoadingEdit', //Funcion despues de cargar edit
    'beforeSendForm' => 'funcion' // Antes de Send Form
]
--}}
@php
    if(!isset($config)) {
        throw new \Exception('Es necesario un array de configuracion');
    }
@endphp
<div class="modal fade" role="dialog" id="{!! $config['id'] !!}">
    <div class="modal-dialog {!! isset($config['modal-lg']) ? 'modal-lg' : '' !!}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@{{ mode == 'new' ? 'Nuevo' : 'Editar' }}</h4>
            </div>
            <form v-on:submit.prevent="sendForm">
                <div class="modal-body row">
                    {!! $newOrEdit or '' !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary btn-flat">Guardar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" role="dialog" id="{!! $config['id'] !!}Show">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Detalles</h4>
            </div>
            <div class="modal-body row">
                {!! $showDetails or '' !!}
            </div>
            <div class="modal-footer">
                {!! $showDetailsButtons or '' !!}
                <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cerrar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

@push('scripts')
<script>
    {!! $config['id'] !!} = new Vue({
      el: '#{!! $config['id'] !!}',
      mixins : [{!! isset($config['extendCreateUpdate']) ? $config['extendCreateUpdate'] : '' !!}],
      data: {
        document: {!! json_encode($config['structure']) !!},
        mode: 'new',
        rootUrl: '{!! request()->url() !!}',
        modal: '#{!! $config['id'] !!}'
      },
      methods: {
        showForm: function(type, id) {
          this.clearForm();
          this.mode = type ? type: 'new';
          $this = this;
          if($this.mode == 'edit') {
            $this.$http.get($this.rootUrl+'/'+id).then(function(response) {
              $this.document = response.body;
              {!! isset($config['afterLoadEdit']) ? $config['afterLoadEdit'].'(response, $this);' : '' !!}
              $($this.modal).modal('show');
            });
          } else {
            $(this.modal).modal('show');
          }
        },

        sendForm: function() {
          $this = this;
          let method = ($this.mode == 'new') ? 'post' : '{!! isset($config['updateWithFile']) ? 'post' : 'patch' !!}';
          let add = ($this.mode == 'new') ? '' : '/'+$this.document.id{!! isset($config['updateWithFile']) ? " + '/update'" : '' !!};
          //
          $this = this;
            {!! isset($config['beforeSendForm']) ? 'let data = '.$config['beforeSendForm'].'($this.document);' : 'let data = $this.document;' !!}

              $this.$http[method]($this.rootUrl + add, data).then(function() {
              sgSuccess('Se ha guardado correctamente');
            });
        },

        deleteElement: function(element) {
          $this = this;
          sgConfirm('Eliminar', '¿Está seguro de eliminar?', 'Continuar', function() {
            $this.$http.delete($this.rootUrl+'/'+element.id).then(function() {
              sgSuccess('Se ha eliminado correctamente');
            });
          });
        },

        clearForm: function() {
          this.document = {!! json_encode($config['structure']) !!};
        },

        showDetails: function(element) {
          $this = this;
          {!! isset($config['beforesShowDetails']) ? $config['beforesShowDetails'].'(this);' : '' !!}
          $this.$http.get($this.rootUrl+'/'+element.id+'?showDetails=true').then(function(response) {
            {!! isset($config['afterShowDetails']) ? $config['afterShowDetails'].'(response, $this);' : '' !!}
            {!! $config['id'] !!}Show.showDocument(response.body);
          });
        }
      }
    });

    {!! $config['id'] !!}Show = new Vue({
      el: '#{!! $config['id'] !!}Show',
      mixins : [{!! isset($config['extendShow']) ? $config['extendShow'] : '' !!}],
      data: {
        document: {!! json_encode($config['structure']) !!}
      },
      methods: {
        showDocument: function(document) {
          this.document = document;
          $('#{!! $config['id'] !!}Show').modal('show');
        }
      }
    });
</script>
@endpush