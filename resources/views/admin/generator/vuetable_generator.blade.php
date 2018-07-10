<vuetable
        :api-url="api"
        pagination-path=""
        :fields="fields"
        table-class="table table-bordered table-hover"
        ascending-icon="glyphicon glyphicon-chevron-up"
        descending-icon="glyphicon glyphicon-chevron-down"
        :per-page="perPage"
        wrapper-class="vuetable-wrapper"
        table-wrapper=".vuetable-wrapper"
        loading-class="loading"
        pagination-component="vuetable-pagination"
        :item-actions="itemActions"
        :pagination-info-template="paginationInfoTemplate"
        :append-params="moreParams"
></vuetable>

@push('scripts')

{{--
el,
columns => [
    title, name, sort, filter
],
api,
defaultParams => [],
actions => [
    name, icon, color, title, event
],
onLoad = functionName,
moreDataVars = [],
afterLoadSuccess = 'add after';
--}}

@php
    // Ultima Modificación: 14/05/2018

    $mutating = [];
    //Table Columns
    $tableColumns = [];
    for($i = 0; $i < count($config['columns']); $i++) {
        $column = $config['columns'][$i];
        if(isset($column['title'])) {
            $tableColumns[$i]['title'] = $column['title'];
        }
        if(!isset($column['name'])) throw new \Exception('Es necesario un campo name para la columna nro: '.$i);
        $tableColumns[$i]['name'] = $column['name'];
        if(isset($column['sort']) && $column['sort'] == true) {
            $tableColumns[$i]['sortField'] = $column['name'];
        }

        if(isset($column['mutating'])) {
            if(strpos($column['mutating'], 'return') === false) {
                $column['mutating'] = 'return '.$column['mutating'].';';
            }
            $tableColumns[$i]['callback'] = 'mutating_'.$i;
            $mutating[] = [
                'func' => 'mutating_'.$i,
                'content' =>  $column['mutating']
            ];
        }
    }
    if(isset($config['actions']) && count($config['actions'])) {
      $tableColumns[] = [
          'name' => '__actions',
          'title' => 'Acciones',
          'titleClass' => 'text-center'
      ];
    }

    //Actions
    $tableActions = [];
    $jsActions = '';
    if(!empty($config['actions'])) {
        for($i = 0; $i < count($config['actions']); $i++) {
            $column = $config['actions'][$i];

            $tableActions[$i]['name'] = 'action-'.$i;
            $tableActions[$i]['label'] = isset($column['label']) ? $column['label'] : '';
            $tableActions[$i]['icon'] = $column['icon'];
            $tableActions[$i]['class'] = 'btn btn-sm btn-flat '.$column['class'];
            $tableActions[$i]['extra']['title'] = $column['title'];
            $tableActions[$i]['extra']['data-toggle'] = 'tooltip';
            $tableActions[$i]['extra']['data-placement'] = 'top';
        }

        //Generando acciones
        for($i = 0; $i < count($config['actions']); $i++) {
            $column = $config['actions'][$i];

            $jsActions .= "if (action == '".'action-'.$i."') { ".$column['event']." }\n";
        }
    }



    //Generando Pintas
    $jsHighLight = '';
    for($i = 0; $i < count($config['columns']); $i++) {
        $column = $config['columns'][$i];
        if(isset($column['filter']) && $column['filter'] == true) {
            $jsHighLight .= "data[n].".$column['name']." = this.highlight(this.searchFor, data[n].".$column['name'].");\n";
        }
    }

    //Generando funciones de mutacion
    $jsMutating = '';
    foreach ($mutating as $mut) {
        $jsMutating .= $mut['func'].": function(value) { ".$mut['content']." },";
    }
@endphp

<script>
  Vuetable = new Vue({
    el: '{!! $config['el'] !!}',
    data: {
      //generando mas variables de datos
        {!! isset($config['moreDataVars']) ? print_r($config['moreDataVars'].',', true) : '' !!}
        api: '{!! $config['api'] !!}',
      fields: {!! json_encode($tableColumns) !!},
      paginationComponent: 'vuetable-pagination',
      paginationInfoTemplate: 'Mostrando {from} a {to} de {total} elementos',
      itemActions: {!! json_encode($tableActions) !!},
      searchFor: '',
      perPage: 20,
      defaultParams: {!! isset($config['defaultParams']) ? json_encode($config['defaultParams']) : json_encode([]) !!},
      moreParams: {!! isset($config['defaultParams']) ? json_encode($config['defaultParams']) : json_encode([]) !!}
    },
    watch: {
      'perPage': function(val, oldVal) {
        this.$broadcast('vuetable:refresh')
      },
      'paginationComponent': function(val, oldVal) {
        this.$broadcast('vuetable:load-success', this.$refs.vuetable.tablePagination)
        this.paginationConfig()
      }
    },
    ready: function() {
        {!! isset($config['onLoad']) ? print_r($config['onLoad'].'(this);', true) : '' !!}
    },
    methods: {
        {!! print_r($jsMutating, true) !!}
        paginationConfig: function() {
          this.$broadcast('vuetable-pagination:set-options', {
            wrapperClass: 'pagination',
            icons: { first: '', prev: '', next: '', last: ''},
            activeClass: 'active',
            linkClass: 'btn btn-default btn-sm btn-flat',
            pageClass: 'btn btn-default btn-sm btn-flat'
          })
        },
      setFilter: function() {
        $this = this;
        $this.moreParams = [];
        $.each(this.defaultParams, function(index, val) {
          $this.moreParams.push(val);
        });
        this.moreParams.push('filter=' + this.searchFor);
        this.$nextTick(function() {
          this.$broadcast('vuetable:refresh')
        })
      },
      resetFilter: function() {
        this.searchFor = ''
        this.setFilter()
      },
      preg_quote: function( str ) {
        return (str+'').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, "\\$1");
      },
      highlight: function(needle, haystack) {
        if(typeof  haystack == 'number') {
          haystack = haystack.toString();
        }
        return haystack.replace(
          new RegExp('(' + this.preg_quote(needle) + ')', 'ig'),
          '<span class="highlight">$1</span>'
        );
      }
    },
    events: {
      'vuetable:load-success': function(response) {
        var data = response.data.data;
        if (this.searchFor !== '') {
          for (n in data) {
              {!! print_r($jsHighLight, true) !!}
          }
        }
          {!! isset($config['loadSuccess']) ? print_r($config['loadSuccess'].'(this, response);', true) : '' !!}
      },
      'vuetable:action': function(action, element) {
          {!! print_r($jsActions, true) !!}
      }
    }
  });
</script>
@endpush