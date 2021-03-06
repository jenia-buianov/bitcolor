<script>
    if(typeof Timeouts !== 'undefined') {
        Timeouts.forEach(function (element, index) {
            clearInterval(Timeouts[index]);
        });
        delete Timeouts;
    }
    Timeouts = [];
</script>
<div id="colors_">
    @foreach($sectors as $k=>$v)
    <div class="color" id="{{$v}}" @if($game['money']==0) onclick="selectColor(this)" @endif><div>

        <?php
            if (count($bets)>0){
                $total = 0;
                $found = false;
                foreach($bets as $key=>$value){
                    if ((string)$value->sector==(string)$v and !empty($value->bank) and ($value->bank>0)) {
                        echo $value->bank;
                        $found = true;
                        $total+=$value->bank;
                    }
                    elseif ((string)$value->sector==(string)$v) {
                        $found = true;
                        echo '0';
                    }
                }
                if (!$found) echo '0';
            }
            else{
                $total = 0;
                echo '0';
            }
        ?>
            <i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i>
        </div>
    </div>
@endforeach
</div>
@if($game['isActive']==0)
    <div id="info">
        <div class="bank">
            <span id="bank">@if($game['bank']>0){{$game['bank']}}@else 0 @endif <i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i></span>
        </div>

        <div id="players" style="font-size:1em;display:block;width:100%;text-align:center">{{translate('players')}}: {{(int)$game['players']}} <i class="fa fa-users" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></div>
        <div style="text-align: center">{{translate('game_fin')}}: {{mb_substr($game['finished_at'],0,16)}}</div>
        <div style="text-align: center">{{translate('win_color')}}: {{$game['win_sector']}}</div>
        @if($game['money']>0)<div style="text-align: center">{{translate('zip_pass')}}: {{$game['zipPassword']}}</div>@endif
    </div>
@endif
<div id="game_{{$game['id']}}" style="text-align: center;display: block; min-height: 10em;margin-bottom:1.5em">
    <?php

        if ($game['isActive']==1){
            $time = ($game['time']+$timePerGame)-time();
            $minutes = (int)($time/60);
            $seconds = (int)($time - ($minutes*60));
    echo'<div class="bank">
        <span id="bank">';
        if($game['bank']>0)echo $game['bank']; else echo '0';
            echo'<i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i></span>
    </div>';
            echo'<div style="text-align: center;margin-top:1.5em;">'.translate('t_left').':</div>';
            echo'<div style="font-size:1.5em;display:block;width:100%;text-align:center"><font>'.$minutes.':'.$seconds.' <script>setLeftGameTimer('.$game['id'].','.$time.');</script></font> <i class="fa fa-clock-o" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></div>';
            echo'<div id="players" style="font-size:1em;display:block;width:100%;text-align:center">'.translate('players').': '.(int)$game['players'].' <i class="fa fa-users" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></div>';
            if ($game['money']>0) {
                echo'<div style="text-align: center;margin-top:1.5em;" id="my_amount">'.translate('my_amount').': '.$game['money'].' <i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i></div>';
                echo'<div style="text-align: center;margin-top:1.5em;" id="my_color">'.translate('sel_color').': '.$game['color'].'</div>';
            }else{
                echo'<div style="text-align: center;margin-top:1.5em;display:none;" id="my_amount"></div>';
                echo'<div style="text-align: center;margin-top:1.5em;display:none;" id="my_color"></div>';
            }
            echo'<div style="font-size:1em;display:block;width:100%;text-align:center"><a href="'.url('/results/'.$game['zipfile'].'.zip').'" data-toggle="logout">'.translate('download_zip').'</a></div>';


    ?>
            <div style="margin-top:1.5em;">
                <form action="{{url('/game/bet')}}" onsubmit="return Send(this)" id="amountForm" title="{{translate('a_form')}}">
                    <label class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-top:1em;">
                        @if($game['money']>0) {{translate('add_more')}}: @else {{translate('amount')}}: @endif
                    </label>
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                        <input type="text" class="form-control" name="amount" placeholder="{{translate('amount')}}" must="1" value="0.001">
                    </div>
                    <input type="hidden" value="@if($game['money']>0){{$game['color']}}@endif" name="color" must="1" placeholder="{{translate('color')}}">
                    <button type="submit" class="btn btn-success" style="margin-top:1em">{{translate('send')}}</button>
                </form>
            </div>
            <?
        }
    ?>
</div>