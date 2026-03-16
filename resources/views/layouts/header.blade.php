            
            @auth
            
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                  <i class="bi bi-list"></i>
                </a>
              </li>
              <li class="nav-item d-none d-md-block"><a href="{{ route('reports.fillings') }}" class="nav-link">Dolum Raporları</a></li>
              <li class="nav-item d-none d-md-block"><a href="{{ route('samples.report.page') }}" class="nav-link">Analiz Raporları</a></li>
             
                <li class="nav-item d-none d-md-block"><a href="{{ route('complaints.index') }}" class="nav-link">Müşteri Öneri Şikayet</a></li>
                  <li class="nav-item d-none d-md-block">
                  <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#socialModal">
                    Sosyal Medya
                  </a>
                </li>
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
            
                  <ul class="navbar-nav ms-auto d-flex align-items-center">
      <!-- Profil -->
      <li class="nav-item">
          <a href="{{ route('profile.edit') }}" class="nav-link">
              <i class="bi bi-person-lines-fill me-1"></i> Profil
          </a>
      </li>

      <!-- Çıkış -->
      <li class="nav-item">
          <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="nav-link btn btn-link text-danger p-0">
                  <i class="bi bi-box-arrow-right me-1"></i> Çıkış Yap
              </button>
          </form>
      </li>
  </ul>
            
            <!--end::End Navbar Links-->
          </div>
          <!--end::Container-->
        </nav>
        <!-- Sosyal Medya Modal -->
<div class="modal fade" id="socialModal" tabindex="-1" aria-labelledby="socialModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="socialModalLabel">📱 İletişim & Sosyal Medya</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <p><i class="bi bi-envelope-fill me-2"></i> <strong>Email:</strong> mrt.krmn.55@gmail.com  // info@mkwep.com.tr</p>
        <p><i class="bi bi-telephone-fill me-2"></i> <strong>Telefon:</strong> +90 545 650 22 55</p>

        <hr>
        <p><i class="bi bi-linkedin me-2 text-primary"></i> <a href="https://linkedin.com/in/murat-karaman-292657217" target="_blank">LinkedIn</a></p>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>

  @endauth