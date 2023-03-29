@extends('layouts.app')

@section('content')

<div class="container">
    <form method="POST" action="{{ route('home.change') }}">
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
                        <div class="card-header"><strong>{{ __('Создание замены расписания') }}</strong></div>

                        <div class="card-body">
                            <p class="card-text">
                                {{ __('Замены расписаний могут храниться в базе данных неограниченное количество времени, но отображаются только на текущую неделю.') }}
                            </p>
                            <p class="card-text">
                                {{ __('Для просмотра и редактирования замен на текущую неделю выберите группу и нажмите "Отобразить".') }}
                            </p>
                            <p class="card-text">
                                {{ __('Для создания замены также необходимо выбрать группу из списка.') }}
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
                                <button type="submit" class="btn btn-primary mr-2" name="change-show" value="show">
                                    {{ __('Отобразить') }}
                                </button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('Назад') }}</a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header">
                        <div class="row">
                            <div class="col">
                                <button type="submit" class="btn btn-sm btn-secondary mr-2" name="change-add" value="add">
                                    {{ __('Создать') }}
                                </button>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm" name="day" placeholder="{{ __($date->format('d')) }}">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm" name="month" placeholder="{{ __($date->format('m')) }}">
                            </div>
                            <div class="col">
                                <input type="text" class="form-control form-control-sm" name="year" placeholder="{{ __($date->format('Y')) }}">
                            </div>
                        </div>
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
                                            <input type="text" class="form-control form-control-sm" name="{{$k}}-subject" placeholder="{{ __('Название предмета') }}">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-sm" name="{{$k}}-classroom" placeholder="{{ __('Аудитория') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" name="{{$k}}-teacher">
                                                <option value="">{{ __('Преподаватель...') }}</option>
                                                @foreach($teacher_list as $teacher)
                                                    <option value="{{ $teacher->getId() }}">
                                                        {{ __($teacher->getSurname().' '.$teacher->getName().' '. $teacher->getPatronymic() ?? '') }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <select class="form-control form-control-sm" name="{{$k}}-second">
                                                <option value="">{{ __('Преподаватель...') }}</option>
                                                @foreach($teacher_list as $teacher)
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
                                            <input type="text" class="form-control form-control-sm" name="{{$k}}-commentary" placeholder="{{ __('Примечание') }}">
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>

        {{-- Render all changes starting from last Monday. --}}

        @if(isset($days) and !$days->isEmpty())
            <div class="row justify-content-between">

                @foreach($days as $index => $day)
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="row justify-content-between">
                                    <div class="col-10">
                                        <strong>{{ __($day->getDate()->format('d / m / Y')) }}</strong>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" name="change-remove" value="{{ $day->getId() }}">
                                            {{ __('X') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush">

                                @if(isset($subjects[$day->getId()]) and !$subjects[$day->getId()]->isEmpty())

                                    {{-- Render up to 10 subjects for each day. --}}

                                    @foreach($subjects[$day->getId()] as $subject)

                                        @if(!isset($subject))
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col-2 text-muted">
                                                        {{ __($loop->iteration) }}
                                                    </div>
                                                    <div class="col-7"></div>
                                                </div>
                                            </li>
                                            @continue;
                                        @endif

                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col-2">
                                                    {{ __($loop->iteration) }}
                                                </div>
                                                <div class="col-7">
                                                    {{ __($subject->getName()) }}
                                                </div>
                                                <div class="col">

                                                    {{-- Render dropleft button if isset at least 1 of this 3 elements. --}}

                                                    @if(!$teachers[$subject->getId()]->isEmpty() or $subject->getCommentary() !== null or $subject->getClassroom() !== null)
                                                        <div class="dropleft d-inline-flex">

                                                            @if($subject->getCommentary() !== null)
                                                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    {{ __($subject->getClassroom() ?? '') }}
                                                                </button>
                                                            @elseif($teachers[$subject->getId()]->isEmpty())
                                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" disabled>
                                                                    {{ __($subject->getClassroom() ?? '') }}
                                                                </button>
                                                            @else
                                                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    {{ __($subject->getClassroom() ?? '') }}
                                                                </button>
                                                            @endif

                                                            <div class="dropdown-menu p-3 text-muted">
                                                                @if(!$teachers[$subject->getId()]->isEmpty())
                                                                    <p>
                                                                        {{ __('Преподаватель:') }}

                                                                        @foreach($teachers[$subject->getId()] as $teacher)
                                                                            {{ __(isset($teacher) ? $teacher->getSurname().' '.$teacher->getName().' '.$teacher->getPatronymic() : '') }}
                                                                            <br>
                                                                        @endforeach
                                                                    </p>
                                                                @endif

                                                                @if($subject->getCommentary() !== null)
                                                                    <p class="mb-0">
                                                                        {{ __('Примечание: '.$subject->getCommentary()) }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </li>
                                    @endforeach

                                @else
                                    <li class="list-group-item">
                                        <div class="row justify-content-center py-5 text-muted">
                                            {{ __('Выходной') }}
                                        </div>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </div>
                @endforeach

            </div>
        @endif

    </form>
</div>

@endsection
