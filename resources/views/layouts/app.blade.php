<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>{{ $title or 'saluter' }}</title>
        <meta name="description" content="Server for evaluating salutes.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Ubuntu">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/css/bootstrap-slider.min.css" integrity="sha256-qxOBz0Std9dtkon+cnUi5A7HTHO0CLbp9DnkxsJuIXc=" crossorigin="anonymous">
        <style>
            body {
                font-family: 'Ubuntu', sans-serif;
            }
            img.emoji {
                margin: 0px !important;
                display: inline !important;
                height: 32px;
            }
        </style>

        @stack('head')
    </head>
    <body>
        <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')

        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script src="https://twemoji.maxcdn.com/2/twemoji.min.js?2.3.0"></script>
        <script src="/js/Autolinker.min.js"></script>
        <script>
            window.onload = function() {
                twemoji.parse(document.body);

                var autolinker = new Autolinker( {
                    urls : {
                        schemeMatches : true,
                        wwwMatches    : true,
                        tldMatches    : true
                    },
                    email       : false,
                    phone       : false,
                    mention     : 'twitter',
                    hashtag     : 'twitter',

                    stripPrefix : false,
                    stripTrailingSlash : true,
                    newWindow   : true,

                    truncate : {
                        length   : 0,
                        location : 'end'
                    },

                    className : ''
                } );

                var myTextEl = document.body;
                myTextEl.innerHTML = autolinker.link( myTextEl.innerHTML );

                $("input.slider").slider();
                $("input.slider").on("slideStop", function(slideEvt) {
                    rateSalutation(slideEvt.target.dataset.sliderId, slideEvt.value);
                });
            }

            function rateSalutation(id, rate)
            {
                $.ajax({
                        url: '{{ route('ratings.store') }}',
                        method: 'POST',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'rate': rate,
                            'salutation_id': id,
                        },
                        success: function (data) {
                            $('#rating-' + id).text(rate);
                        },
                        error: function (data) {
                            console.log(data);
                        },
                });
            }
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.8.1/bootstrap-slider.min.js" integrity="sha256-3nkG8q6ajh1K1fHC3hi142DykXlM5TA2xX3OzP/NNJM=" crossorigin="anonymous"></script>

        @stack('scripts')

        @unless (empty(config('google-analytics')))
            <script>
                window.ga=function(){ga.q.push(arguments)};ga.q=[];ga.l=+new Date;
                ga('create','{{ config('google-analytics') }}','auto');ga('send','pageview')
            </script>
            <script src="https://www.google-analytics.com/analytics.js" async defer></script>
        @endunless
    </body>
</html>
