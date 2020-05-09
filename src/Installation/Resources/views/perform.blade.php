@extends('installer::layout')

@section('body')
	<div class="card performInstall">
	  <div class="card-header">
	    Installing...
	  </div>
	  <div class="card-body">
	  	<div class="error d-none text-danger">
	  		Error encountered :
	  		<p class="message"></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.env')}}">
	  		<p>Write env file... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.enableCore')}}">
	  		<p>Enable Core module... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
        <div class="step" data-url="{{ route('install.steps.coreModules')}}">
            <p>Install core modules... <i class="float-right fa fa-check d-none"></i></p>
        </div>
	  	<div class="step" data-url="{{ route('install.steps.otherModules')}}">
	  		<p>Install extra modules... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.seed')}}">
	  		<p>Seed all modules... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
        <div class="step" data-url="{{ route('install.steps.user')}}">
            <p>Create admin user... <i class="float-right fa fa-check d-none"></i></p>
        </div>
	  	<div class="step" data-url="{{ route('install.steps.node')}}">
	  		<p>Install node modules... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.assets')}}">
	  		<p>Build assets... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.symStorage')}}">
	  		<p>Symlink storage... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.symThemes')}}">
	  		<p>Symlink themes... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<div class="step" data-url="{{ route('install.steps.cache')}}">
	  		<p>Clear cache... <i class="float-right fa fa-check d-none"></i></p>
	  	</div>
	  	<p class="success d-none text-success">
	  		Installation complete!
	  	</p>
	  </div>
	  <div class="card-footer text-muted text-right">
	    <a href="/" class="d-none btn btn-primary visit">Visit my site</a>
	  </div>
	</div>
@endsection