@extends ('layout')

@section ('content')
    <div class="container">
        @foreach ($tweets as $tweet)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>{{ $tweet->salutation }}</strong>
                </div>
                <div class="panel-body">
                    {{ $tweet->tweet }}
                </div>
                <div class="panel-footer">
                    <a href="https://twitter.com/i/web/status/{{ $tweet->tweet_id }}" class="btn btn-default" target="_blank" rel="noopener noreferrer">View</a>
                    <a href="{{ route('tweets.show', $tweet) }}" class="btn btn-default" target="_blank">Data</a>

                    <input class="slider" type="text"
                        data-slider-id='{{ $tweet->id }}'
                        data-slider-min="0"
                        data-slider-max="100"
                        data-slider-step="1"
                        data-slider-value="{{ $tweet->rating ?? 50 }}"
                    >
                    <span class="rating" id="rating-{{ $tweet->id }}">{{ $tweet->rating }}</span>
                </div>
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
