@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Route::has('register'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ __('Включен режим регистрации администраторов. Адрес "'.route('register').'" доступен всем пользователям. Необходимо запретить регистрацию в настройках адресов (routes/web.php) как можно скорее.') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (isset($alert))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {{ __($alert) }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-between">
        <div class="col-md-6 pb-3">
            <div class="card">
                <div class="card-header">
                    <strong>{{ __('Группы') }}</strong>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __('Редактирование данных групп') }}</p>
                    <a href="{{ route('home.group') }}" class="btn btn-primary">{{ __('Открыть') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 pb-3">
            <div class="card">
                <div class="card-header">
                    <strong>{{ __('Преподаватели') }}</strong>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __('Редактирование данных преподавателей') }}</p>
                    <a href="{{ route('home.teacher') }}" class="btn btn-primary">{{ __('Открыть') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 pb-3">
            <div class="card">
                <div class="card-header">
                    <strong>{{ __('Семестровое расписание') }}</strong>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __('Создание семестрового расписания для существующих групп') }}</p>
                    <a href="{{ route('home.default') }}" class="btn btn-primary">{{ __('Открыть') }}</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong>{{ __('Замены') }}</strong>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __('Создание замен расписаний на дни недели') }}</p>
                    <a href="{{ route('home.change') }}" class="btn btn-primary">{{ __('Открыть') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
