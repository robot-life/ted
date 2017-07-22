@extends('layouts.app')

@section ('content')
    <div class="container">
        @foreach ($tweets as $tweet)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="https://twitter.com/i/web/status/{{ $tweet->tweet_id }}" class="btn btn-default" target="_blank" rel="noopener noreferrer">View</a>
                    <a href="{{ route('tweets.show', $tweet) }}" class="btn btn-default" target="_blank">Data</a>
                </div>
                <div class="panel-body">
                    {{ $tweet->tweet }}
                </div>
                @foreach ($tweet->salutations as $salutation)
                <div class="panel-footer">
                    {{ $salutation->text }}
                    <br>
                    <input class="slider" type="text"
                        data-slider-id='{{ $salutation->id }}'
                        data-slider-min="0"
                        data-slider-max="100"
                        data-slider-step="1"
                        data-slider-value="{{ $salutation->rating ?? 50 }}"
                    >
                    <span class="rating" id="rating-{{ $salutation->id }}">{{ $salutation->rating }}</span>
                </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <style>
        .flexcontainer {
            display: flex;
            justify-content: center;
        }
    </style>
    <div class="flexcontainer">
        {{ $tweets->links() }}
    </div>
@endsection
