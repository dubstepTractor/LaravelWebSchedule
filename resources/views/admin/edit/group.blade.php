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
                <div class="card-header"><strong>{{ __('Редактирование групп') }}</strong></div>

                <div class="card-body">
                    <p class="card-text">
                        {{ __('Перед тем как создавать расписание для группы, необходимо добавить ее в систему.') }}
                    </p>
                    <p class="card-text">
                        {{ __('При удалении группы все связанные с ней расписания перестанут отображаться.') }}
                    </p>

                    <form method="POST" action="{{ route('home.group') }}">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="number">{{ __('Номер') }}</label>
                                <input type="text" class="form-control mb-3" id="number" name="number">
                            </div>
                            <div class="col">
                                <label for="letter">{{ __('Буква') }}</label>
                                <input type="text" class="form-control mb-3" id="letter" name="letter">
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2" name="group-add" value="add">
                                {{ __('Добавить') }}
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('Назад') }}</a>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('home.group') }}">
                        @csrf
                        <ul class="list-group list-group-flush">
                            @foreach($groups as $group)
                                <li class="list-group-item">
                                    <div class="row justify-content-between">
                                        <div class="col-2">
                                            {{ __($loop->iteration) }}
                                        </div>
                                        <div class="col-8">
                                            {{ __($group->getNumber().' '.$group->getLetter()) }}
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" name="group-remove" value="{{ $group->getId() }}">{{ __('X') }}</button>
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
