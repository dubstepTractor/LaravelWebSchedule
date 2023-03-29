@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (isset($alert))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {{ __($alert) }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header"><strong>{{ __('Редактирование преподавателей') }}</strong></div>

                <div class="card-body">
                    <p class="card-text">
                        {{ __('Перед тем как прикреплять преподавателя к занятию, необходимо добавить его в систему.') }}
                    </p>

                    <form method="POST" action="{{ route('home.teacher') }}">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="surname">{{ __('Фамилия') }}</label>
                                <input type="text" class="form-control mb-3" id="surname" name="surname">
                            </div>
                            <div class="col">
                                <label for="name">{{ __('Имя') }}</label>
                                <input type="text" class="form-control mb-3" id="name" name="name">
                            </div>
                            <div class="col">
                                <label for="patronymic">{{ __('Отчество') }}</label>
                                <input type="text" class="form-control mb-3" id="patronymic" name="patronymic">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2" name="teacher-add" value="add">
                                {{ __('Добавить') }}
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('Назад') }}</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('home.teacher') }}">
                        @csrf
                        <ul class="list-group list-group-flush">
                            @foreach($teachers as $teacher)
                                <li class="list-group-item">
                                    <div class="row justify-content-between">
                                        <div class="col-2">
                                            {{ __($loop->iteration) }}
                                        </div>
                                        <div class="col-8">
                                            {{ __($teacher->getSurname().' '.$teacher->getName().' '. $teacher->getPatronymic() ?? '') }}
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" name="teacher-remove" value="{{ $teacher->getId() }}">{{ __('X') }}</button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
