@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div id="mainContent" class="col-md-8 col-lg-8">
                @include('game.game')
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="bs-component">
                    <div class="list-group">
                        <a href="{{url('/game')}}" class="list-group-item active">
                            <span class="badge" id="currGames">{{$currGames}}</span>
                            Games
                        </a>
                        <a href="{{url('/game/active')}}" class="list-group-item">
                            <span class="badge" id="myActive">{{$myActive}}</span>
                            My active games
                        </a>
                        <a href="{{url('/statistic')}}" class="list-group-item">
                            <span class="badge" id="myStatistic">{{$myStatistic}}</span>
                            Success
                        </a>
                        <a href="{{url('/statistic/history')}}" class="list-group-item">
                            <span class="badge" id="myPlayedGames">{{$myPlayedGames}}</span>
                            I played
                        </a>
                        <a href="{{url('/statistic/top')}}" class="list-group-item">
                            <span class="badge" id="placeTop">{{$top}}</span>
                            Place in top
                        </a>
                    </div>
                </div>

            </div>
    </div>
@endsection