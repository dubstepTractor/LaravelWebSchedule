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

            <div class="card mb-3">
                <img src="image/current.png" class="card-img" style="height: 150px;" alt="">
                <div class="card-img-overlay py-5">
                    <h5 class="card-title"><strong>{{ __($current->getName()) }}</strong></h5>
                    <p class="card-text">
                        {{ __($date->format('d / m / Y')) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TODO: Create new layout for day rendering? --}}

<div class="container">

    {{-- Sort groups by letter. --}}

    @foreach($groups as $letter => $groups_num)

        <div class="row justify-content-center text-muted mb-3">
            {{ __('Специальность "'.$letter.'"') }}
        </div>

        <div class="row justify-content-between">

            {{-- Render current day for each group. --}}

            @foreach($groups_num as $group)
                @continue(!isset($group) or !isset($days[$group->getId()]))
                
                <div class="col-md-6 pb-3">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ __($group->getNumber().' '.$group->getLetter()) }}</strong>
                        </div>
                        <ul class="list-group list-group-flush">

                            @if(isset($subjects[$group->getId()]) and !$subjects[$group->getId()]->isEmpty())

                                {{-- Render up to 10 subjects for each group. --}}

                                @foreach($subjects[$group->getId()] as $subject)

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

    @endforeach
</div>

@endsection
