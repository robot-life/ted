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
                    <a href="https://twitter.com/i/web/status/{{ $tweet->tweet_id }}" class="btn btn-default" target="_blank" rel="noopener noreferrer">Tweet</a>
                    <a href="{{ route('tweets.show', $tweet) }}" class="btn btn-default" target="_blank">Data</a>
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
