@extends('main')
@section('content')
<div style="background-color:#94fdff;";>
    @foreach($posts as $post)
        <article>
            <h2>{!! $post->title !!}</h2>
                <p>
                    {!! $post->excerpt !!}
                    </p>
                <p>
                    published: {{ $post->published_at  }}
                    </p>
                    </article>
    @endforeach </div>
<div style="background-color:#94ff5b;height:20px";> </div>
@stop
