<header class="header">
  <div class="header-left">
    <button class="toggle-btn" id="toggleBtn">
      <i class="fas fa-bars"></i>
    </button>
    <h4 class="mb-0 ml-3">Dashboard</h4>
    <div class="search-bar">
      <input type="text" placeholder="Search...">
      <i class="fas fa-search"></i>
    </div>
  </div>
  <div class="header-right">
    <div class="header-icon">
      <i class="fas fa-expand" onclick="toggleFullscreen()"></i>
    </div>
    <div class="header-icon">
      <i class="fas fa-bell"></i>
      <span class="badge-counter">3</span>
    </div>
    <div class="header-icon">
      <i class="fas fa-envelope"></i>
      <span class="badge-counter">7</span>
    </div>
    <div class="dropdown">
      <button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown">
        <i class="fas fa-globe"></i> EN
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#">English</a>
        <a class="dropdown-item" href="#">Spanish</a>
        <a class="dropdown-item" href="#">French</a>
      </div>
    </div>
    <div class="dropdown">
      <div class="user-info" data-toggle="dropdown">
        <span class="d-none d-md-inline">Admin User</span>
        <div class="user-avatar">A</div>
      </div>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#"><i class="fas fa-user mr-2"></i>Profile</a>
        <a class="dropdown-item" href="#"><i class="fas fa-cog mr-2"></i>Settings</a>
        <div class="dropdown-divider"></div>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="dropdown-item"
            onclick="event.preventDefault(); this.closest('form').submit();"><i
              class="fas fa-sign-out-alt mr-2"></i>Logout</button>
        </form>
      </div>
    </div>
  </div>
</header>
