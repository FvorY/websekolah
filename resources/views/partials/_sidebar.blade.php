<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header text-center">
                <div class="dropdown profile-element"> <span>
                        <img alt="image" class="img-circle" src="{{ asset('assets/img/profile_small.jpg') }}" />
                    </span>
                    <span class="clear"> <span class="block m-t-xs">
                            <strong class="font-bold" style="color: white;">Admin</strong>
                            </span>
                    </span>
                </div>
                <div class="logo-element" style="background:#f3f3f4;">
                    <img src="{{ asset('assets/img/dboard/logo/sublogo.png') }}" width="30px">
                </div>
            </li>

            <li class="{{Request::is('dashboard') ? 'active' : ''}}">
              <a href="{{url('dashboard')}}"><i class="fa fa-th-large"></i>
                  <i class="" aria-hidden="true"></i><span class="nav-label">Dashboards</span>
              </a>
            </li>

        </ul>
    </div>
</nav>
