<div class="modal fade summernote-add-image-plugin-modal" tabindex="-1" role="dialog" id="{!! isset($config['id']) ? $config['id'] : 'new-file' !!}">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Selecciona Archivo</h4>
            </div>
            <div class="modal-body">
                <div>
                    <div class="input-group input-group-sm">
                        <span class="input-group-addon" id="sizing-addon3">Subir una nueva imagen: </span>
                        <input type="file" class="form-control sg-plugin-file" placeholder="Imagen" accept="image/jpeg, image/png">
                        <span class="input-group-btn">
                                <button class="btn btn-success btn-flat sg-plugin-file-btn-upload" type="button" @click="uploadFile"><i class="fa fa-upload" aria-hidden="true"></i> Subir</button>
                            </span>
                    </div>
                    <div class="sg-plugin-upload"><img src="{!! asset('img/loader.gif') !!}"> Espera mientras se sube la imagen... </div>
                </div>
                <hr>
                <div class="sg-plugin-browser">
                    <a v-for="(index, file) in files" href="#" @click="selectFile(index, file)" :class="selectedIndex == index ? 'selected' : ''" ><img :src="'{!! url('cache/small/') !!}/'+file.cache"></a>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-left">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li>
                                <a href="#" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <li><a href="#">1</a></li>
                            <li><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">4</a></li>
                            <li><a href="#">5</a></li>
                            <li>
                                <a href="#" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="text-left" style="font-size: 9px;">Photo Selecter by <a href="http://www.adolfocuadros.com" target="_blank">Adolfo Cuadros</a></div>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-flat sg-plugin-select" @click="insertFile" :disabled="selected == null">Seleccionar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{!! asset('css/summernote-add-image-plugin.css') !!}">
@endpush
@push('scripts')
<script>
    {!! isset($config['id']) ? $config['id'] : 'Selecter' !!} = new Vue({
      el: '#{!! isset($config['id']) ? $config['id'] : 'new-file' !!}',
      data: {
        files: [],
        selected: null,
        selectedIndex: null,
        context: null
      },
      ready: function() {
        this.loadImages();
      },
      methods: {
        showModal: function () {
          $('#new-file').modal('show');
        },
        loadImages: function() {
          $this = this;
          this.$http.get('/{!! request()->segment(1) !!}/panel/image-upload').then(function(response) {
            //console.log(response.body.data);
            $this.files = response.body.data;
          });
        },

        selectFile: function(index, file) {
          this.selected = file;
          this.selectedIndex = index;
        },

        insertFile: function() {
          var imgNode = $('<img style="max-width:100%;">').attr('src', this.selected.url);
          //$('#contenido').summernote('insertNode', imgNode[0]);
          //this.context.invoke('editor.insertText', 'sgimage');
            @if(!empty($config['insertFile']))
                    {!! $config['insertFile'] !!}(this.selected, this);
            @else
              this.context.invoke('editor.insertNode', imgNode[0]);
            @endif
              this.selected = null;
          this.selectedIndex = null;
          $('#new-file').modal('hide');
        },

        uploadFile: function() {
          $this = this;
          $modal = $('#new-file');
          $modal.find('.sg-plugin-upload').show();
          var file = $modal.find('.sg-plugin-file')[0];
          if(!file.files.length) {
            swal('Error', 'Seleccione un archivo para subir.', 'info');
            return false;
          }

          data = new FormData;
          data.append('image', file.files[0]);

          this.$http.post('/panel/image-upload', data).then(function(response) {
            $this.files.unshift(response.body);
            $($modal).find('.sg-plugin-upload').hide();
          }, function() {
            $($modal).find('.sg-plugin-upload').hide();
          });
        },

        setContext: function(context) {
          this.context = context;
        }
      }
    });
</script>
@endpush
