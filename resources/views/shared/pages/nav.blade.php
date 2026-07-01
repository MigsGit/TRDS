
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="height: 100vh">

    <!-- System title and logo -->
    <a href="{{ route('blank') }}" class="brand-link">
        <img src="{{ asset('public/images/pricon_logo2.png') }}"
            alt="OITL"
            class="brand-image img-circle elevation-3"
            style="opacity: .8">

        <span class="brand-text font-weight-light font-size"><h5>TRDS v2</h5></span>
    </a> <!-- System title and logo -->

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item has-treeview">
                    <a href="{{ url('../RapidX') }}" class="nav-link">
                        <i class="nav-icon fas fa-arrow-left"></i>
                        <p>Return to RapidX</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('blank') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">Administrator Management</li>
                <li class="nav-item has-treeview">
                    <a href="{{ route('user_master') }}"  class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>[Migz] User list</p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">Modules</li>
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('hr_memo') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>HR MEMO/Approval </p>
                    </a>
                </li> --}}
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p> HR Memo/Approval </p>&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-down"> </i>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('hr_memo_exam') }}" class="nav-link">
                                <p> Examinations</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('hr_memo') }}" class="nav-link">
                                <p> Memo/Approval</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- <li class="nav-item has-treeview">
                    <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>[Darren] Training Request/Approval </p>
                    </a>
                </li> --}}

                 <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p> Training Request/Approval  </p>&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-down"> </i>
                    </a>

                     <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('training_request') }}" class="nav-link">
                                <p> Training Request</p>
                            </a>
                        </li>
                        {{-- <li class="nav-item">
                            <a href="{{ route('training_request_approval') }}" class="nav-link">
                                <p> Traning Approval</p>
                            </a>
                        </li> --}}
                    </ul>

                </li>

                <li class="nav-item has-treeview">
                    <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>[Migz] Training Attendance </p>
                    </a>
                </li>

                {{-- <li class="nav-item has-treeview">
                    <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>[Chan] Theoretical Exam </p>
                    </a>
                </li> --}}

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <p>
                            [Chan] Theoretical Exam
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('questionnaire') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Questionnaire</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('examDashboard') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Examination</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Exam Result</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>[Nessa] Personal Skill Matrix </p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p> Training Endorsement </p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="" data-toggle="modal" data-target="#modalOnGoing" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>[Chris] Qualification / Certification </p>
                    </a>
                </li>
            </ul>
        </nav>
    </div><!-- Sidebar -->
</aside>
