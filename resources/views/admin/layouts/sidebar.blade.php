<!-- Sidebar user panel -->
<div class="user-panel">
    <div class="pull-left image" >
        <img  src="{!! asset('img/default-user-image.png') !!}" class="img-circle" alt="User Image">
    </div>
    <div class="pull-left info">
        <p>{!! Auth::user()->name !!}</p>
        <span>Administrador</span>
    </div>
</div>
<!-- /.search form -->
<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu" data-widget="tree">
    <li class="header">Menu Principal</li>
    <li>
        <a href="{!! route('dashboard') !!}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
        </a>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-circle"></i> <span>Niveles</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @foreach(['Ejemplo A', 'Ejemplo B'] as $item)
                <li><a href="#"><i class="fa fa-circle-o"></i> {!! $item !!}</a></li>
            @endforeach
        </ul>
    </li>
    <li>
        <a href="{!! route('panel.acerca_de') !!}">
            <i class="fa fa-info"></i>
            <span>Acerca de...</span>
        </a>
    </li>
</ul>