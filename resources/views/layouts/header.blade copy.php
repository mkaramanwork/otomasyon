          
          @auth
          
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Tüm Raporlar</a></li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Günlük Raporlar</a></li>
            <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Tank Durumları</a></li>
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            
      
            
            <!--end::Notifications Dropdown Menu-->
            <!--begin::Fullscreen Toggle-->
            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
            <!--begin::User Menu Dropdown-->
           <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                    <img src="{{ asset('assets/admin/img/logo.png') }}"
                         class="rounded-circle me-2"
                         style="width: 32px; height: 32px;"
                         alt="User Image">
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </a>
              </li>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header text-center">
                        <strong>{{ auth()->user()->name }}</strong><br>
                        <small>{{ auth()->user()->getRoleNames()->first() }}</small>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            Profil Düzenle
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                Çıkış Yap
                            </button>
                        </form>
                    </li>
                </ul>
           
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
@endauth