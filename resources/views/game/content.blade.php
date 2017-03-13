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
<div id="game_{{$game['id']}}" style="text-align: center;display: block; min-height: 10em;">
    <div class="bank">
        <span id="bank">@if($game['bank']>0){{$game['bank']}}@else 0 @endif <i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i></span>
    </div>
    <?php

        if ($game['isActive']==1){
            $time = ($game['time']+$timePerGame)-time();
            $minutes = (int)($time/60);
            $seconds = (int)($time - ($minutes*60));
            echo'<div style="text-align: center;margin-top:1.5em;">Time left:</div>';
            echo'<div style="font-size:1.5em;display:block;width:100%;text-align:center"><font>'.$minutes.':'.$seconds.' <script>setLeftGameTimer('.$game['id'].','.$time.');</script></font> <i class="fa fa-clock-o" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></div>';
            echo'<div id="players" style="font-size:1em;display:block;width:100%;text-align:center">Players: '.(int)$game['players'].' <i class="fa fa-users" aria-hidden="true" style="color:#cccccc;margin-left: 0.5em"></i></div>';
            if ($game['money']>0) {
                echo'<div style="text-align: center;margin-top:1.5em;" id="my_amount">My amount: '.$game['money'].' <i class="fa fa-btc" aria-hidden="true" style="color:#ff9800;"></i></div>';
                echo'<div style="text-align: center;margin-top:1.5em;" id="my_color">Selected color: '.$game['color'].'</div>';
            }else{
                echo'<div style="text-align: center;margin-top:1.5em;display:none;" id="my_amount"></div>';
                echo'<div style="text-align: center;margin-top:1.5em;display:none;" id="my_color"></div>';
            }
            echo'<div style="font-size:1em;display:block;width:100%;text-align:center"><a href="'.url('/result/'.$game['zipfile'].'.zip').'" data-toggle="logout">Download archive with winner sector</a></div>';


    ?>
            <div style="margin-top:1.5em;">
                <form action="{{url('/game/bet')}}" onsubmit="return Send(this)" id="amountForm" title="Amount form">
                    <label class="col-xs-12 col-sm-12 col-md-4 col-lg-4" style="padding-top:1em;">
                        @if($game['money']>0) Add more: @else Amount: @endif
                    </label>
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
                        <input type="text" class="form-control" name="amount" placeholder="Amount" must="1" value="0.00001">
                    </div>
                    <input type="hidden" value="@if($game['money']>0){{$game['color']}}@endif" name="color" must="1" placeholder="Color">
                    <button type="submit" class="btn btn-success" style="margin-top:1em">Send</button>
                </form>
            </div>
            <?
        }
        else{
            echo '<div style="text-align: center">Game finished: '.mb_substr($game['finished_at'],0,16).'</div>';
            echo '<div style="text-align: center">Win color: '.$game['win_sector'].'</div>';
        }
    ?>
</div>