<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('pageTitle') - TMS</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link href="{{ asset('style.css') }}" rel="stylesheet">
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <!-- icons are obtained from https://icons8.com/ -->
    <style>
      .custom-sidebar{
        height: 75%;
      }

      .notification{
        margin-top: 8.5px;
        margin-right: 20px;
      }

      .notification-icon {
        position: relative;
      }

      .badge {
        position: absolute;
        background-color: red; /* Set the background color */
        color: white; /* Set the text color */
        border-radius: 50%; /* Makes it a circle */
        padding: 4px 8px; /* Adjust padding as needed */
      }

    .icon img {
        width: 24px; /* Adjust the width as needed */
        height: 24px; /* Adjust the height as needed */
    }
      
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/homepage') }}">
                    TMS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                          <li class="notification">
                            <a href="{{ route('notification') }}">
                              <i class="fa fa-bell"></i>
                              <span class="badge badge-light">{{ $notification_number }}</span>
                            </a>
                          </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
                <div class="custom-sidebar">
                  <div class="sidebar-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                      </svg>
                  </div>
                  
                  <div class="logo"></div>
              
                  <div class="menu">
                      <div class="menu-title">Menu</div>
              
                      <a href="/homepage">
                          <div class="icon">
                              <img src="https://img.icons8.com/ios/452/home-page.png" alt="Homepage Icon" class="w-6 h-6"/>
                          </div>
                          <div class="label">Homepage</div>
                      </a>
              
                      <a href="/trainee-profile">
                          <div class="icon">
                              <img src="https://img.icons8.com/ios/452/user--v1.png" alt="Profile Icon" class="w-6 h-6"/>
                          </div>
                          <div class="label">Profile</div>
                      </a>
              
                      <a href="/view-seat-plan">
                          <div class="icon">
                              <img src="https://img.icons8.com/ios/50/aircraft-seat-middle.png" alt="View Seat Plan" class="w-6 h-6"/>
                          </div>
                          <div class="label">View Seat Plan</div>
                      </a>
              
                      <a href="/trainee-upload-resume">
                          <div class="icon">
                              <img src="https://img.icons8.com/ios/452/resume-website.png" alt="Upload Resume Icon" class="w-6 h-6"/>
                          </div>
                          <div class="label">Upload Resume</div>
                      </a>
              
                      <a href="/trainee-upload-logbook">
                          <div class="icon">
                              <img src="https://img.icons8.com/ios/452/book-stack.png" alt="Upload Logbook Icon" class="w-6 h-6"/>
                          </div>
                          <div class="label">Upload Logbook</div>
                      </a>
              
                      <a href="/trainee-task-timeline">
                          <div class="icon">
                              <img src="https://img.icons8.com/ios/452/timeline.png" alt="Task Timeline Icon" class="w-6 h-6"/>
                          </div>
                          <div class="label">Task Timeline</div>
                      </a>
                  </div>
              </div>
              
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
<script src="{{ asset('app.js') }}"></script>
</html>
