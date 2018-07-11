@extends('admin.layouts.app')

@section('content')
    <!-- Default box -->

    <div class="box" id="app">
        <div class="box-header with-border">
            <h3 class="box-title">{!! $pageTitle or 'Usuarios' !!}</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive">
            <div class="col-md-12">
                <div class="form-inline form-group pull-left">
                    <label>Filtrar:</label>
                    <input v-model="searchFor" class="form-control input-sm" @keyup.enter="setFilter">
                    <button class="btn btn-primary btn-flat btn-sm" @click="setFilter">Filtrar</button>
                    <button class="btn btn-default btn-flat btn-sm" @click="resetFilter">Resetear</button>
                </div>
                <div class="pull-right">
                    <button type="button" class="btn btn-success btn-sm btn-flat" onclick="ModalForm.showForm('new')"><i class="fa fa-plus"></i> Agregar Nuevo Usuario</button>
                </div>
            </div>
            @include('admin.generator.vuetable_generator', [
                'config' => [
                  'el' => '#app',
                  'api' => request()->url().'/list',
                  'columns' => [
                    [
                      'title' => 'Nombre',
                      'name'  => 'name',
                      'sort'  => true,
                      'filter' => true
                    ],
                    [
                      'name'  => 'email',
                      'sort'  => true,
                      'filter' => true
                    ]
                  ],
                  'actions' => [
                    [
                      'icon' => 'fa fa-edit',
                      'class' => 'btn-warning',
                      'title' => 'editar',
                      'event' => "ModalForm.showForm('edit', element.id);"
                    ],
                    [
                      'icon' => 'fa fa-trash',
                      'class' => 'btn-danger',
                      'title' => 'Eliminar',
                      'event' => "ModalForm.deleteElement(element);"
                    ]
                  ]
                ]
            ])
        </div>
        <!-- /.box-body -->
    </div>
    @component('admin.generator.crud_modal', [
        'config' => [
            'id' => 'ModalForm',
            'structure' => [
                'name'      => null,
                'email'     => null
            ]
        ]
    ])
    @slot('newOrEdit')
    <div class="col-sm-12 sg-input">
        <div class="input-group">
            <span class="input-group-addon">Nombre Usuario:</span>
            <input v-model="document.name" type="text" class="form-control" placeholder="Nombre Usuario">
        </div>
    </div>
    <div class="col-sm-12 sg-input">
        <div class="input-group">
            <span class="input-group-addon">Email:</span>
            <input v-model="document.email" type="email" class="form-control" placeholder="Email">
        </div>
    </div>
    <div v-if="mode=='new'" class="col-sm-12 sg-input">
        <div class="input-group">
            <span class="input-group-addon">Password:</span>
            <input v-model="document.password" type="password" class="form-control" placeholder="Password">
        </div>
    </div>
    <div v-if="mode=='new'" class="col-sm-12 sg-input">
        <div class="input-group">
            <span class="input-group-addon">Repetir Password:</span>
            <input v-model="document.password_confirmation" type="password" class="form-control" placeholder="Repetir Password">
        </div>
    </div>
    @endslot
    @endcomponent
@endsection
