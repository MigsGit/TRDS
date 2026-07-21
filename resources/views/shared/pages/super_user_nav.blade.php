
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="height: 100vh">

    <!-- System title and logo -->
    <a href="{{ route('blank') }}" class="brand-link">
        <img src="{{ asset('public/images/pricon_logo2.png') }}"
            alt="OITL"
            class="brand-image img-circle elevation-3"
            style="opacity: .8">

        <span class="brand-text font-weight-light font-size"><h5>TRDSv2</h5></span>
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
                        <p>User list</p>
                    </a>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('insp_skill_chart_setting') }}"  class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Inspector Skill Chart Settings</p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">Modules</li>
                {{-- <li class="nav-item has-treeview">
                    <a href="{{ route('hr_memo') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>HR MEMO/Approval </p>
                    </a>
                </li> --}}
                <?php
                    // if($globalUser != null){
                    //     $user_access = explode(',', $globalUser->user_modules_id);
                    //     $hr_memo_access = in_array(1, $user_access); //HR Memorandum
                    // }
                ?>


                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p> HR Memo/Approval</p>&nbsp;&nbsp;&nbsp;<i class="fas fa-angle-down"> </i>
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
                    </ul>
                </li>
                {{-- http://rapidx/TRDSv2_attendance/ --}}
                @if ( in_array(7,explode(',', $globalUser->user_modules_id)) )
                    <li class="nav-item">
                        <a href="{{ route('training_attendance') }}"  class="nav-link">
                            <i class="fas fa-users"></i>
                            <p>Training Attendance Summary   </p>
                        </a>
                    </li>
                @endif
                 <li class="nav-item">
                     <a href="http://rapidx/TRDSv2_attendance/"  class="nav-link">
                        <i class="fas fa-user"></i>
                        <p>Training Attendance  </p>
                    </a>
                </li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <p>
                            Theoretical Exam
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
                            <a href="{{ route('examination_result') }}" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Exam Result</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-treeview">
                    <a href="{{ route('personnel_skill_matrix') }}"  class="nav-link">
                        <i class="fas fa-blind"></i>
                        <p>Personnel Skill Matrix</p>
                    </a>
                </li>
                @if ( in_array(16,explode(',', $globalUser->user_modules_id)) )
                    <li class="nav-item has-treeview">
                        <a href="{{ route('training_endorsement') }}"  class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Training Endorsement </p>
                        </a>
                    </li>
                @endif

                <li class="nav-item has-treeview">
                    {{-- <a href="{{ route('qualification_certification') }}" class="nav-link"> --}}
                    <a href="#" class="nav-link">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Qualification / Certification</p>
                    </a>
                </li>

                <li class="nav-header font-weight-bold">Export</li>
                <li class="nav-item has-treeview">
                    <a id="btnListCertPersonnel" class="nav-link">
                        <i class="fas fa-file-excel"></i>
                        <p>List of Certified Personnel</p>
                    </a>
                </li>



            </ul>
        </nav>
    </div><!-- Sidebar -->
</aside>
