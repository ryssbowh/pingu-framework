@extends('installer::layout')

@section('body')
	<div class="card environment">
	  {{ FormFacade::open() }}
	  <div class="card-header">
	    Step 2 : .env file
	  </div>
	  <div class="card-body">
	  	@if ($errors->any())
		    <div class="alert alert-danger">
		        <ul class="mb-0">
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
	  	<div class="form-group">
		  {{ FormFacade::label('DB_CONNECTION', 'Database driver') }}
		  {{ FormFacade::select('DB_CONNECTION', config('installer.drivers'), 'mysql', ['class' => 'form-control']) }}
		</div>
		<div class="form-group">
		  {{ FormFacade::label('DB_HOST', 'Database host') }}
		  {{ FormFacade::text('DB_HOST', '127.0.0.1', ['class' => 'form-control']) }}
		</div>
		<div class="form-group">
		  {{ FormFacade::label('DB_DATABASE', 'Database name') }}
          <p class="d-none text-warning">This database exists and will be deleted...</p>
		  {{ FormFacade::text('DB_DATABASE', null, ['class' => 'form-control']) }}
		</div>
		<div class="form-group">
		  {{ FormFacade::label('DB_USERNAME', 'Database username') }}
		  {{ FormFacade::text('DB_USERNAME', null, ['class' => 'form-control']) }}
		</div>
		<div class="form-group">
		  {{ FormFacade::label('DB_PASSWORD', 'Database password') }}
		  {{ FormFacade::text('DB_PASSWORD', null, ['class' => 'form-control']) }}
		</div>
		@foreach(config('installer.env') as $name => $data)
			<div class="form-group">
			  {{ FormFacade::label($name, $data['label']) }}
			  @if($data['type'] == 'select')
			  	{{ FormFacade::select($name, $data['values'], null, ['class' => 'form-control']) }}
			  @else
			  	{{ FormFacade::{$data['type']}($name, null, ['class' => 'form-control']) }}
			  @endif
			  
			</div>
		@endforeach
	  </div>
	  <div class="card-footer text-muted text-right">
	    {{ FormFacade::submit('Next', ['class' => 'btn btn-primary']) }}
	  </div>
	  {{ FormFacade::close() }}
	</div>
@endsection