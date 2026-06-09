  <footer id="footer" class="footer position-relative">

    <div class="container">
      <div class="row gy-5">

        <div class="col-lg-4">
          <div class="footer-content">
            <a href="{{ route('public.index') }}" class="logo d-flex align-items-center mb-4">
              <span class="sitename">{{ config('app.name') }}</span>
            </a>
            <p class="mb-4">Platform terintegrasi untuk layanan dan informasi laboratorium.</p>
          </div>
        </div>

        <div class="col-lg-2 col-6">
          <div class="footer-links">
            <h4>Navigasi</h4>
            <ul>
              <li><a href="{{ route('public.index') }}"><i class="bi bi-chevron-right"></i> Home</a></li>
              <li><a href="{{ route('public.announcements.index') }}"><i class="bi bi-chevron-right"></i> Announcements</a></li>
            </ul>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="footer-contact">
            <h4>Kontak</h4>
            <div class="contact-item">
              <div class="contact-icon">
                <i class="bi bi-geo-alt"></i>
              </div>
              <div class="contact-info">
                <p>{{ sys_tenant_address() ?? 'Alamat' }}</p>
              </div>
            </div>

            <div class="contact-item">
              <div class="contact-icon">
                <i class="bi bi-envelope"></i>
              </div>
              <div class="contact-info">
                <p>{{ sys_tenant_email() ?? 'email@example.com' }}</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="footer-bottom">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="copyright">
              <p>&copy; <span>Copyright</span> <strong class="px-1 sitename">{{ config('app.name') }}</strong> <span>All Rights Reserved</span></p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </footer>
