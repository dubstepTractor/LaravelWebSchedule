@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- TODO: Create new layout for alert rendering? --}}

            @if(isset($alert))
                <div class="alert alert-primary alert-dismissible fade show" role="alert">
                    {{ __($alert) }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($groups))
                <div class="card mb-3">
                    <div class="card-header"><strong>{{ __('Расписание на семестр') }}</strong></div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('default') }}">
                            @csrf
                            <div class="form-group">
                                <select class="form-control" name="select">
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
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Показать') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- TODO: Same as /group. Create new layout for week rendering? --}}

<div class="container">

    {{-- Render up to 7 days starting from Monday. --}}

    @if(isset($days) and !$days->isEmpty())
        <div class="row justify-content-between">

            @foreach($weekdays as $weekday)
                @continue(!isset($days[$weekday->getId()]))
                <div class="col-md-6">

                    {{-- Change card color for current day of the week. --}}

                    @php $is_current = $weekday->getId() === $current->getId(); @endphp

                    <div class="card @if($is_current) bg-primary @endif mb-3">
                        <div class="card-header @if($is_current) text-white @endif">
                            <strong>{{ __($weekday->getName()) }}</strong>
                        </div>
                        <ul class="list-group list-group-flush">

                            @if(isset($subjects[$weekday->getId()]) and !$subjects[$weekday->getId()]->isEmpty())

                                {{-- Render up to 10 subjects for each day. --}}

                                @foreach($subjects[$weekday->getId()] as $subject)

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

</div>

@endsection
