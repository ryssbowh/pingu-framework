@extends('installer::layout')

@section('body')
	<div class="card">
	  <div class="card-header">
	    Step 1 : Server requirements
	  </div>
	  <div class="card-body">
	    <h6 class="font-weight-bold">Php version</h6>
	    <p>
	    	Minimum {{ config('installer.minPhpVersion') }}
	    	@if($checks['php'])
	    		<i class="float-right fa fa-check"></i>
	 		@else
	 			<i class="float-right fa fa-times"></i>
	    	@endif
	    </p>
	    <h6 class="font-weight-bold">Php extensions</h6>
	    @foreach($checks['phpExtension'] as $name => $pass)
	    	<p>
		    	{{ $name }}
		    	@if($pass)
		    		<i class="float-right fa fa-check"></i>
		 		@else
		 			<i class="float-right fa fa-times"></i>
		    	@endif
		    </p>
	    @endforeach
	    <h6 class="font-weight-bold">Apache extensions</h6>
	    @foreach($checks['apacheExtension'] as $name => $pass)
	    	<p>
		    	{{ $name }}
		    	@if($pass)
		    		<i class="float-right fa fa-check"></i>
		 		@else
		 			<i class="float-right fa fa-times"></i>
		    	@endif
		    </p>
	    @endforeach
	    <h6 class="font-weight-bold">File permissions</h6>
	    @foreach($checks['permissions'] as $name => $pass)
	    	<p>
		    	{{ $name }} ({{ config('installer.permissions.'.$name) }})
		    	@if($pass)
		    		<i class="float-right fa fa-check"></i>
		 		@else
		 			<i class="float-right fa fa-times"></i>
		    	@endif
		    </p>
	    @endforeach
	    <h6 class="font-weight-bold">Apache permissions</h6>
	    <p>{{ $checks['apachePerm']['title'] }}
	    	@if($checks['apachePerm']['pass'])
	    		<i class="float-right fa fa-check"></i>
	 		@else
	 			<i class="float-right fa fa-times"></i>
	    	@endif
	    </p>
	    <h6 class="font-weight-bold">Commands</h6>
	    @foreach($checks['commands'] as $name => $pass)
	    	<p>
		    	{{ $name }}
		    	@if($pass)
		    		<i class="float-right fa fa-check"></i>
		 		@else
		 			<i class="float-right fa fa-times"></i>
		    	@endif
		    </p>
	    @endforeach
	  </div>
	  <div class="card-footer text-muted text-right">
	    @if($failure)
	    	<p>Please fix the issues and <a href="">Refresh</a></p>
	    @else
	    	<a href="{{ route('install.env') }}" class="btn btn-primary">Next</a>
	    @endif
	  </div>
	</div>
@endsection