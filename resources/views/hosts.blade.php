@extends('app')

@section('content')
<div class="container">
	@if(empty($hosts))
		<div class="alert alert-danger">No registered hosts</div>
	@else
		<div class="row">
			<div class="col-sm-1">
				STATUS
			</div>
			<div class="col-sm-1">
				HTTP
			</div>
			<div class="col-sm-4">
				NAME
			</div>
			<div class="col-sm-4">
				HOST
			</div>
			<div class="col-sm-2">
				LAST
			</div>
		</div>
		@foreach($hosts as $host)
		<div class="row">
			<div class="col-sm-1">
				@if($host->status == 'UP')
					<span class="glyphicon glyphicon-ok"></span>
				@elseif($host->status == 'DOWN')
					<span class="glyphicon glyphicon-remove"></span>
				@else
					<span class="glyphicon glyphicon-question-sign"></span>
				@endif
			</div>
			<div class="col-sm-1">
				@if( ! empty($host->status_code))
					{{ $host->status_code }}
				@else
					<span class="glyphicon glyphicon-question-sign"></span>
				@endif
			</div>
			<div class="col-sm-4">
				{{ $host->name }}
			</div>
			<div class="col-sm-4">
				<a href="{{ $host->url }}">
					{{ $host->url }}
				</a>
			</div>
			<div class="col-sm-2">
				@if( ! $host->current)
					<span class="glyphicon glyphicon-question-sign"></span>
				@else
					[{{ $host->current->status}}] {{ $host->current->created_at }}
				@endif
			</div>
		</div>
		@endforeach
	@endif
</div>
<script type="text/javascript">

setTimeout(function() {
	location.reload();
}, 60000);

</script>
@endsection