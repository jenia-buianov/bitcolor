@extends('main')
@section('owner_reg')

    <div id="" class="owner-reg" style="margin-left: 30%;">
        <p> {!! link_to_route('tenant', 'Зарегистрироваться через социальные сети!') !!} </p>
        Тем самым вы нам поможете, а вам не потребуется процедура идентификации!


    </div>
    <div id="" class="owner-reg">
        <p>  {!! link_to_route('owner', 'Сдать квартиру дороже и в 2 раза быстрее') !!} </p>
        Пройти обычную регистрацию, потребуется пройти проверку, чтобы подтвердить, что вы действительно собстенник квартиры

    </div>

@stop