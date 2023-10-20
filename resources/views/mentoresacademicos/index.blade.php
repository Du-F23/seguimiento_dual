@extends('layouts.app')
@section('title', 'Mentores')

@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            @if( session('status') )
                <div class="alert alert-success alert-dismissible text-dark" role="alert">
                <span class="text-sm"> <a href="javascript:" class="alert-link text-dark">Excelente</a>.
                    {{ session('status') }}.</span>
                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary rounded pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Lista De Mentores Academicos</h6>
                                @if(Auth::user()->rol_id === 1)
                                    <div class="float-end">
                                        {{-- Button del modal --}}
                                        <a href="{{route('academicos.create')}}" class="btn btn-primary"
                                           title="Agregar una nueva Moto">
                                            <i class="mdi mdi-plus-circle-outline"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Titulo</th>
                                        <th>Nombre</th>
                                        <th>Correo Electronico</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($mentores as $mentor)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $mentor->titulo }}</td>
                                            <td>{{ $mentor->name }}</td>
                                            <td>{{ $mentor->email }}</td>
                                            <td>
                                                <a href="{{ route('academicos.show', Vinkla\Hashids\Facades\Hashids::encode($mentor->id)) }}"
                                                   class="btn btn-facebook">
                                                    <i class="mdi mdi-account-details btn-icon-prepend"></i>
                                                </a>
                                                @if(Auth::user()->rol_id === 1)
                                                    <a href="{{ route('academicos.edit', Vinkla\Hashids\Facades\Hashids::encode($mentor->id)) }}" class="btn btn-twitter">
                                                        <i class="mdi mdi-account-edit btn-icon-prepend"></i>
                                                    </a>
                                                    <button class="btn btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#exampleModal1"
                                                            onclick="deleteMentor({{ $mentor->id }})">
                                                        <i class="mdi mdi-delete btn-icon-prepend"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="exampleModal1" tabindex="-1"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Eliminar Mentor Academico Temporalmente</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="container">
                                        <div class="row justify-content-center">
                                            <div class="col-md-12">
                                                <form action="" id="deleteForm" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <p id="banner">¿Estas seguro de eliminar este registro?</p>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary" type="button"
                                                                data-bs-dismiss="modal">Cancelar
                                                        </button>
                                                        <button class="btn btn-danger" type="submit">Eliminar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        // hace una peticion ajax para obtener la informacion de la moto
        function deleteMentor(id) {
            let form = document.getElementById('deleteForm')
            form.action = '/academicos/' + id + '/delete'
            $.ajax({
                url: '/academicos/' + id + '/json',
                type: 'GET',
                success: function (response) {
                    $('#banner').html('¿Estas seguro de eliminar este registro? ' + response.titulo + ', ' + response.name);
                }
            })
        }

        function restoreRegistro(id) {
            let form = document.getElementById('restaurarForm')
            form.action = '/academicos/' + id + '/restaurar'
            $.ajax({
                url: '/academicos/' + id,
                type: 'GET',
                success: function (response) {
                    //console.log(response.name)
                    $('#bannerRestore').html('¿Estas seguro de restaurar este registro? ' + response.titulo + ' ' + response.name);
                }
            })
        }
    </script>
@endsection
