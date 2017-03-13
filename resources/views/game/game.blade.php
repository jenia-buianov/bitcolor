
<?php if (count($games)>0){ ?>
<script>
    if(typeof Timeouts !== 'undefined') {
        Timeouts.forEach(function (element, index) {
           clearInterval(Timeouts[index]);
        });
        delete Timeouts;
    }
    Timeouts = [];
</script>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Game</th>
                    <th style="text-align: center">{{translate('players')}}</th>
                    <th style="text-align: center">{{translate('t_left')}}</th>
                    <th style="text-align: right">{{translate('bank')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($games as $k=>$v)
                    <?php
                        $time = ($v->time+$timePerGame)-time();
                        $minutes = (int)($time/60);
                        $seconds = (int)($time - ($minutes*60));
                    ?>
                    <tr id="game_{{$v->id}}">
                        <td><a href="{{url('/game/view/'.$v->id)}}" style="font-size:1.2em">Game #{{$v->id}}</a></td>
                        <td style="text-align: center">{{$v->players}} <i class="fa fa-users" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></td>
                        <td style="text-align: center"><font>{{$minutes}}:{{$seconds}} <script>setLeftGameTimer({{$v->id}},{{$time}});</script></font> <i class="fa fa-clock-o" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></td>
                        <td style="text-align: right">@if($v->money>0) {{$v->money}} @else 0 @endif <i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;margin-left: 0.5em"></i></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
<?php } else {?>
    <div class="center">{{translate('no_games')}}</div>
<?php }?>