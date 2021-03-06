@extends('layouts.unregistrated')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li role="presentation" class="active"><a href="#register" id="register-tab" role="tab" data-toggle="tab" aria-controls="register" aria-expanded="true">{{translate('registration')}}</a></li>
                <li role="presentation"><a href="#log" id="register-tab" role="tab" data-toggle="tab" aria-controls="log" aria-expanded="true">{{translate('sing_in')}}</a></li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active in" role="tabpanel" id="register" aria-labelledby="register-tab">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}" id="regForm" onsubmit="return Send(this)" title="{{translate('registration')}}">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" id="_token">

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">{{translate('name')}}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" must="1" placeholder="{{translate('name')}}" name="name" value="{{ old('name') }}">

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{translate('email')}}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" must="1"  placeholder="{{translate('email')}}" name="email" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">{{translate('password')}}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" must="1"  placeholder="{{translate('password')}}" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">{{translate('confirm_pass')}}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" placeholder="{{translate('confirm_pass')}}" must="1"  name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-user"></i> {{translate('register')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" role="tabpanel" id="log" aria-labelledby="log-tab">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}" id="loginForm" onsubmit="return Send(this)" title="{{translate('sing_in')}}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{translate('email')}}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" placeholder="{{translate('email')}}" value="{{ old('email') }}">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">{{translate('password')}}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" placeholder="{{translate('password')}}" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember"> {{translate('remember')}}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-btn fa-sign-in"></i> {{translate('login')}}
                                </button>

                                <a class="btn btn-link" href="{{ url('/password/reset') }}">{{translate('forgot')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
             </div>
        </div>
    </div>
</div>
@endsection
