@extends('installer::layout')

@section('body')
	<div class="card modules">
	  {{ FormFacade::open() }}
	  <div class="card-header">
	    Step 4 : Select your Modules
	  </div>
	  <div class="card-body">
	  	<ul class="list-group mb-4">
	  		@foreach($mandatory as $module)
	  			<li class="list-group-item bg-light container">
	  				<div class="row">
	  					<div class="text-left col-10">{{ $module->getName() }}<br/><small>{{ $module->getDescription() }}</small></div>
	  					<div class="text-right col-2">{{ FormFacade::checkbox('modules[]', $module->getName(), true, ['disabled' => true])}}</div>
	  				</div>
	  			</li>
	  		@endforeach
	  		@foreach($optionnal as $module)
	  			<li class="list-group-item container">
	  				<div class="row">
	  					<div class="text-left col-10">{{ $module->getName() }}<br/><small>{{ $module->getDescription() }}</small></div>
	  					<div class="text-right col-2">{{ FormFacade::checkbox('modules[]', $module->getName())}}</div>
	  				</div>
	  			</li>
	  		@endforeach
	  	</ul>
	  </div>
	  <div class="card-footer text-muted text-right">
	    {{ FormFacade::submit('Next', ['class' => 'btn btn-primary']) }}
	  </div>
	  {{ FormFacade::close() }}
	</div>
@endsection