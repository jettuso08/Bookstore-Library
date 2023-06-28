@extends('master')

@section('styles')
    {{-- Datatable --}}
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@stop

@section('content')
    <div class="card">
        <h5 class="card-header">Add Book</h5>
        <div class="card-body">
            <a class="btn btn-primary mb-3" href="{{ route('books.index') }}" role="button">Back</a>

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ Form::open(array('route' => 'books.store')) }}
                @csrf

                <div class="mb-3">
                    {{ Form::label('name', 'Book Name', array('class' => 'form-label')) }}
                    {{ Form::text('name', '', array('class' => 'form-control', 'required' => 'required')) }}
                </div>
                
                <div class="mb-3">
                    {{ Form::label('author', 'Book Author', array('class' => 'form-label')) }}
                    {{ Form::text('author', '', array('class' => 'form-control', 'required' => 'required')) }}
                </div>
                
                <div class="mb-3">
                    {{ Form::label('cover', 'Book Cover', array('class' => 'form-label')) }}
                    {{ Form::text('cover', '', array('class' => 'form-control', 'required' => 'required')) }}
                </div>

                <div class="mb-3 text-center">
                    <button class="btn btn-success w-100" type="submit">Save</button>
                </div>

            {{ Form::close() }}
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(function() {
        });
    </script>
@stop
