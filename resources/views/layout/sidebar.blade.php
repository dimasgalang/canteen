<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('img/chutex.svg') }}" style="width: 40px;">
        </div>
        <div class="sidebar-brand-text mx-3">Chutex <sup>Sys</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    @if($auth == true)
    @if($roleusers[0]->rolename == 'Admin')
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUser"
            aria-expanded="true" aria-controls="collapseUser">
            <i class="fas fa-fw fa-user"></i>
            <span>User</span>
        </a>
        <div id="collapseUser" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('user.index') }}">Daftar User</a>
                <a class="collapse-item" href="{{ route('role.index') }}">Daftar Role</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSyslogs"
            aria-expanded="true" aria-controls="collapseSyslogs">
            <i class="fas fa-fw fa-file"></i>
            <span>SysLogs</span>
        </a>
        <div id="collapseSyslogs" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('syslog.index') }}">System Logs</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTools"
            aria-expanded="true" aria-controls="collapseTools">
            <i class="fas fa-fw fa-cog"></i>
            <span>Tools</span>
        </a>
        <div id="collapseTools" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="chrome://flags/#unsafely-treat-insecure-origin-as-secure" target="_blank">Enable Camera</a>
            </div>
        </div>
    </li>
    @endif
    @endif
    
    @if($auth == true)
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmployee"
            aria-expanded="true" aria-controls="collapseEmployee">
            <i class="fas fa-fw fa-users"></i>
            <span>Employee</span>
        </a>
        <div id="collapseEmployee" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('karyawan.index') }}">Data Employee</a>
            </div>
        </div>
    </li>
    @endif
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseScanner"
            aria-expanded="true" aria-controls="collapseScanner">
            <i class="fas fa-fw fa-check"></i>
            <span>Scanner Check</span>
        </a>
        <div id="collapseScanner" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('scanner.checkscanning') }}">Check Scanning</a>
            </div>
        </div>
    </li>
    
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCanteen"
            aria-expanded="true" aria-controls="collapseCanteen">
            <i class="fas fa-fw fa-id-badge"></i>
            <span>Kantin</span>
        </a>
        <div id="collapseCanteen" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            @if($auth == true)
                @if($roleusers[0]->rolename == 'HR' || $roleusers[0]->rolename == 'Admin')
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('canteen.index') }}">Data Kantin</a>
                    <a class="collapse-item" href="{{ route('scanner.barcodescanning1') }}">Barcode Scanner 1</a>
                    <a class="collapse-item" href="{{ route('scanner.barcodescanning2') }}">Barcode Scanner 2</a>
                    <!-- <a class="collapse-item" href="{{ route('canteen.scancanteen1') }}">QR Canteen 1</a>
                    <a class="collapse-item" href="{{ route('canteen.scancanteen2') }}">QR Canteen 2</a> -->
                </div>
                @endif
            @else
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('scanner.barcodescanning1') }}">Barcode Scanner 1</a>
                <a class="collapse-item" href="{{ route('scanner.barcodescanning2') }}">Barcode Scanner 2</a>
            </div>
            @endif
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <!-- <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> -->

</ul>
<!-- End of Sidebar -->