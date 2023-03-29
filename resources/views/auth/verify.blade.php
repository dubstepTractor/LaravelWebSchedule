@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Подтверждение E-Mail Адреса') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('На указанный E-mail Адрес было отправлено новое письмо') }}
                        </div>
                    @endif

                    {{ __('Пожалуйста, подтвердите свой E-Mail Адрес, перейдя по ссылке в письме.') }}
                    {{ __('Если Вы не получили письмо на указанный адрес') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('нажмите сюда для отправки нового') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
