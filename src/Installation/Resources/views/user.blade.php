@extends('installer::layout')

@section('body')
	<div class="card environment">
	  {{ FormFacade::open() }}
	  <div class="card-header">
	    Step 3 : Admin user
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
          {{ FormFacade::label('name', 'Name *') }}
          {{ FormFacade::text('name', '', ['required' => true, 'class' => 'form-control']) }}
        </div>
	  	<div class="form-group">
		  {{ FormFacade::label('email', 'Email (username) *') }}
		  {{ FormFacade::text('email', '', ['required' => true, 'class' => 'form-control']) }}
		</div>
		<div class="form-group">
		  {{ FormFacade::label('password', 'Password *') }}
          {{ FormFacade::password('password', ['required' => true, 'class' => 'form-control']) }}
		</div>
		<div class="form-group">
		  {{ FormFacade::label('repeat_password', 'Repeat Password *') }}
          {{ FormFacade::password('repeat_password', ['required' => true, 'class' => 'form-control']) }}
		</div>
	  </div>
	  <div class="card-footer text-muted text-right">
	    {{ FormFacade::submit('Next', ['class' => 'btn btn-primary']) }}
	  </div>
	  {{ FormFacade::close() }}
	</div>
@endsection