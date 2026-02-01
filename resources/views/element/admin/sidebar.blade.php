@php
	$action =  Route::getCurrentRoute()->getName();
@endphp
 <div id="sidebar" class="active">
  <div class="sidebar-wrapper active">
     <div class="sidebar-header">
      <div class="d-flex justify-content-between">
         <div class="logo"> <a href="{{ url('/admin/dashboard'); }}">AOL</a> </div>
         <div class="toggler"> <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a> </div>
       </div>
    </div>
     <div class="sidebar-menu">
      <ul class="menu">
         <li class="sidebar-item {{$action =='admin.dashboard' ?'active':''}}"> 
         	<a href="{{ url('/admin/dashboard'); }}" class='sidebar-link'> <i class="bi bi-grid-fill"></i> <span>{{Session::get('admin_type')}} Dashboard</span> </a> 
         </li>
         @php
         $managerActive =
         $profile =
         $changePassword =
         $accounts =
         false;         
         if($action =='admin.update-profile'){
         	$managerActive = $profile = true;
         }
         if($action =='admin.change-password'){
         	$managerActive = $changePassword = true;
         }
         if($action =='admin.accounts'){
         	$managerActive = $accounts = true;
         }          
         @endphp
         <li class="sidebar-item  has-sub {{$managerActive?'active':''}}"> 
         	<a href="#" class='sidebar-link'> <i class="bi bi-person-fill"></i> <span>My Profile</span> </a>
          <ul class="submenu {{$managerActive?'active':''}}">
             <li class="submenu-item {{$profile?'active':''}}"> 
             	<a href="{{ url('/admin/update-profile'); }}">Update Profile</a> 
             </li>
             <li class="submenu-item {{$changePassword?'active':''}} "> 
             	<a href="{{ url('/admin/change-password'); }}">Change Password</a> 
             </li>
             <?php /*?>@if(Session::get('admin_type') == 'Admin')
             <li class="submenu-item {{$accounts?'active':''}}"> 
             	<a href="{{ url('/admin/accounts'); }}">Accounts</a> 
             </li>
             @endif<?php */?>
           </ul>
        </li> 
        @php                        
         $managerOPDActive =
         $newOPDRegistration =
         $reScheduleOPD =
         $admins =
         false;
         if($action =='admin.new-opd-registration' ||  $action =='admin.re-schedule-opd'){
         	$managerOPDActive = true;
         }
         if($action =='admin.new-opd-registration'){
         	$managerOPDActive = $newOPDRegistration = true;
         }
         if($action =='admin.re-schedule-opd'){
         	$managerOPDActive = $reScheduleOPD = true;
         }
         @endphp
         <li class="sidebar-item  has-sub {{$managerOPDActive?'active':''}}"> 
         	<a href="#" class='sidebar-link'> <i class="bi bi-list-check"></i> <span>OPD Manager</span> </a>
          <ul class="submenu {{$managerOPDActive?'active':''}}">
          
             <li class="submenu-item {{$newOPDRegistration?'active':''}}"> 
             	<a href="{{ url('/admin/new-opd-registration'); }}">New OPD Registration </a> 
             </li>

             <li class="submenu-item {{$reScheduleOPD?'active':''}}"> 
             	<a href="{{ url('/admin/re-schedule-opd'); }}">Re-Schedule OPD </a> 
             </li>
            
             
           </ul>
        </li>

        <?php /*?>
         @if(Session::get('admin_type') == 'Admin' || Session::get('admin_type') == 'Account')
         @php                        
         $managerActive =
         $users =
         $admins =
         false;
         if($action =='admin.admins' ||  $action =='admin.add-admins' ||  $action =='admin.edit-admins'){
         	$managerActive = $admins = true;
         }
         if($action =='admin.users' ||  $action =='admin.add-user' ||  $action =='admin.edit-user'){
         	$managerActive = $users = true;
         }
         @endphp
         <li class="sidebar-item  has-sub {{$managerActive?'active':''}}"> 
         	<a href="#" class='sidebar-link'> <i class="bi bi-person-fill"></i> <span>Users</span> </a>
          <ul class="submenu {{$managerActive?'active':''}}">
          
             <li class="submenu-item {{$users?'active':''}}"> 
             	<a href="{{ url('/admin/users'); }}">Customers </a> 
             </li>
            @if(Session::get('admin_type') == 'Admin')
             <li class="submenu-item {{$admins?'active':''}}"> 
             	<a href="{{ url('/admin/admins'); }}">Admin Manager</a> 
             </li>
             @endif
             
           </ul>
        </li>
         @endif<?php */?>
         @php
         $generalManagersActive =
         $financialYears =
         $departments =
         $designations =
         $diseases =
         false;
         if($action =='admin.financial-years' || $action =='admin.add-financial-year' || $action =='admin.edit-financial-year'){
         	$generalManagersActive = $financialYears = true;
         }
         if($action =='admin.departments' || $action =='admin.add-department' || $action =='admin.edit-department'){
         	$generalManagersActive = $departments = true;
         }
         if($action =='admin.designations' || $action =='admin.add-designation' || $action =='admin.edit-designation'){
         	$generalManagersActive = $designations = true;
         }
         if($action =='admin.diseases' || $action =='admin.add-disease' || $action =='admin.edit-disease'){
         	$generalManagersActive = $diseases = true;
         }
         @endphp
         <li class="sidebar-item has-sub {{$generalManagersActive?'active':''}}">
         	<a href="#" class='sidebar-link'> <i class="bi bi-gear-fill"></i> <span>General Managers</span> </a>
          <ul class="submenu {{$generalManagersActive?'active':''}}">
             <li class="submenu-item {{$financialYears?'active':''}}">
             	<a href="{{ url('/admin/financial-years'); }}">Financial Years</a>
             </li>
             <li class="submenu-item {{$departments?'active':''}}">
             	<a href="{{ url('/admin/departments'); }}">Departments</a>
             </li>
             <li class="submenu-item {{$designations?'active':''}}">
             	<a href="{{ url('/admin/designations'); }}">Designations</a>
             </li>
             <li class="submenu-item {{$diseases?'active':''}}">
             	<a href="{{ url('/admin/diseases'); }}">Diseases</a>
             </li>
           </ul>
         </li>
         <li class="sidebar-item"> <a href="{{ url('/admin/logout'); }}" class='sidebar-link'> <i class="bi bi-box-arrow-right"></i> <span>Log Out</span> </a> </li>
       </ul>
    </div>
     <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
   </div>
</div>