@extends('main')
@section('select')

    <div id="tenant" class="type-select">
      <p> {!! link_to_route('tenant', 'Снять квартиру без посредников!') !!} </p>
      <ul>
          <li> Квартиры без комиссии!</li>
          <li> Самая большая база</li>
          <li> Моментальная скорость</li>
      </ul>
     </div>
    <div id="owner" class="type-select">
    <p>  {!! link_to_route('owner', 'Сдать квартиру дороже и в 2 раза быстрее') !!} </p>
        <ul>
            <li> Заработай до 25кР в год с нами, за счет повышения цены</li>
            <li> Средняя скорость сдачи квартиры с риэлтором 2 недели,
            с нами всего 6 дней!</li>
            <li>Один раз поставил объявления и забыл(далее мы совершенно бесплатно делаем всю работу,
            такую как обновление, продвижение, реклама итд)
        </ul>
    </div>

@stop