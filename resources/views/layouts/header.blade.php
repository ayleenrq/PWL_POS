<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="../../index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Profile Dropdown Menu -->
      <li class="nav-item dropdown">
          <a class="nav-link" href="#" data-toggle="dropdown">
              <img src="{{ asset('storage/profile/' . (Auth::user()->profile_picture ?? 'user.png')) }}" 
                  class="img-circle" 
                  alt="User Image" 
                  style="width: 22px; height: 22px; display: block; margin: auto;">
          </a>
          <div class="dropdown-menu dropdown-menu-lg">
              <span class="name-user" style="text-align: center; margin: 5px 0; display: block; font-size: 17px; color: #666;">
                  {{ Auth::user()->nama }}
              </span>
              <div class="dropdown-divider"></div>
              <a href="{{ url('/change-photo') }}" class="dropdown-item">
                  <i class="fas fa-user mr-2"></i>Profile
              </a>
              <div class="dropdown-divider"></div>
              <a href="{{ url('/logout') }}" class="dropdown-item" style="color: red;">
                  <i class="fas fa-sign-out-alt mr-2"></i>Logout
              </a>
          </div>
      </li>
    </ul>
  </nav>