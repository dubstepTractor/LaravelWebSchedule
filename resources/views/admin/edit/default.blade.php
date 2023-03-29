@extends('layouts.app')

@section('content')

<div class="container">
    <form method="POST" action="{{ route('home.default') }}">
    @csrf
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

            @if (isset($groups))
                <div class="card mb-3">
                    <div class="card-header"><strong>{{ __('Создание семестрового расписания') }}</strong></div>

                    <div class="card-body">
                        <p class="card-text">
                            {{ __('Постоянное расписание может храниться в базе данных неограниченное количество времени, но отображается только последнее созданное (текущее).') }}
                        </p>
                        <p class="card-text">
                            {{ __('При создании нового расписания все существующие замены для этой группы перестанут отображаться.') }}
                        </p>

                        <div class="form-group">
                            <select class="form-control" name="group-select">
                                <option value="">{{ __('Группа...') }}</option>
                                @foreach($groups as $group_num)
                                    @foreach($group_num as $group)
                                        <option value="{{ $group->getId() }}">
                                            {{ __('Группа '.$group->getNumber().' '.$group->getLetter()) }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary mr-2" name="schedule-add" value="add">
                                {{ __('Создать') }}
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('Назад') }}</a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row justify-content-between">
        @for($i = 0; $i < 7; $i++)
            <div class="col-md-6 pb-3">
                <div class="card">
                    <div class="card-header">
                        <strong>{{ $weekdays[$i]->getName() }}</strong>
                    </div>
                    <ul class="list-group list-group-flush">
                        @for($k = 0; $k < 10; $k++)
                            <li class="list-group-item" style="height: 150px;">
                                <div class="row">
                                    <div class="col-2">
                                        {{ __($k + 1) }}
                                    </div>
                                    <div class="col-7">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm" name="{{$i}}-{{$k}}-subject" placeholder="Название предмета">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm" name="{{$i}}-{{$k}}-classroom" placeholder="Аудитория">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" name="{{$i}}-{{$k}}-teacher">
                                                <option value="">{{ __('Преподаватель...') }}</option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{ $teacher->getId() }}">
                                                        {{ __($teacher->getSurname().' '.$teacher->getName().' '. $teacher->getPatronymic() ?? '') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" name="{{$i}}-{{$k}}-second">
                                                <option value="">{{ __('Преподаватель...') }}</option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{ $teacher->getId() }}">
                                                        {{ __($teacher->getSurname().' '.$teacher->getName().' '. $teacher->getPatronymic() ?? '') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm" name="{{$i}}-{{$k}}-commentary" placeholder="Примечание">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        @endfor
    </div>
    </form>
</div>

@endsection
