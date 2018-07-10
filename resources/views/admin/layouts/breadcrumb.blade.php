<ol class="breadcrumb">
    <li><a href="{!! route('dashboard') !!}"><i class="fa fa-dashboard"></i> Home</a></li>
    @if(!empty(request()->segment(3)))
        <li> {!! ucfirst(request()->segment(3)) !!}</li>
    @endif
</ol>