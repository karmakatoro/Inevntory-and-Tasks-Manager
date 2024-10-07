 <!-- ========== Left Sidebar Start ========== -->
 <div class="left-side-menu">

     <!-- LOGO -->
     <div class="logo-box">
         <a href="{{ route('dashboard.sales') }}" class="logo logo-dark text-center">
             <span class="logo-sm">
                 <img src="{{ asset('assets/images/logo-sm-dark.png') }}" alt="" height="24">
                 <!-- <span class="logo-lg-text-light">Minton</span> -->
             </span>
             <span class="logo-lg">
                 <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="20">
                 <!-- <span class="logo-lg-text-light">M</span> -->
             </span>
         </a>

         <a href="index.html" class="logo logo-light text-center">
             <span class="logo-sm">
                 <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="24">
             </span>
             <span class="logo-lg">
                 <img src="{{ asset('assets/images/logo-light.pn') }}g" alt="" height="20">
             </span>
         </a>
     </div>

     <div class="h-100" data-simplebar>

         <!-- User box -->
         <div class="user-box text-center">
             <img src="{{ asset('storage/users/' . auth()->user()->photo) }}" alt="user-img" title="Mat Helme"
                 class="rounded-circle avatar-md">
             <div class="dropdown">
                 <a href="#" class="text-reset dropdown-toggle h5 mt-2 mb-1 d-block"
                     data-bs-toggle="dropdown">{{ auth()->user()->name }}</a>
                 <div class="dropdown-menu user-pro-dropdown">

                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                         <i class="fe-user me-1"></i>
                         <span>My Account</span>
                     </a>

                     <!-- item-->
                     <a href="javascript:void(0);" class="dropdown-item notify-item">
                         <i class="fe-settings me-1"></i>
                         <span>Settings</span>
                     </a>
                     <!-- item-->
                     <a href="{{ route('auth.get-logged-out') }}" class="dropdown-item notify-item">
                         <i class="fe-log-out me-1"></i>
                         <span>Logout</span>
                     </a>

                 </div>
             </div>
             <p class="text-reset">{{ Str::ucfirst(auth()->user()->type) }}</p>
         </div>

         <!--- Sidemenu -->
         <div id="sidebar-menu">

             <ul id="side-menu">
                 <li>
                     <a href="#sidebarDashboards" data-bs-toggle="collapse" aria-expanded="false"
                         aria-controls="sidebarDashboards" class="waves-effect">
                         <i class="ri-dashboard-line"></i>
                         <span class="badge bg-success rounded-pill float-end">3</span>
                         <span> Dashboards </span>
                     </a>
                     <div class="collapse" id="sidebarDashboards">
                         <ul class="nav-second-level">
                             <li>
                                 <a href="{{ route('dashboard.sales') }}">Sales</a>
                             </li>
                             <li>
                                 <a href="{{ route('dashboard.analytics') }}">Analytics</a>
                             </li>
                         </ul>
                     </div>
                 </li>
                 <li>
                     <a href="#sidebarEcommerce" data-bs-toggle="collapse" aria-expanded="false"
                         aria-controls="sidebarEcommerce">
                         <i class="ri-shopping-cart-2-line"></i>
                         <span class="badge bg-danger float-end">New</span>
                         <span> Products </span>
                     </a>
                     <div class="collapse" id="sidebarEcommerce">
                         <ul class="nav-second-level">
                             <li>
                                 <a href="ecommerce-products.html">List</a>
                             </li>
                             <li>
                                 <a href="ecommerce-customers.html">Catégories</a>
                             </li>
                             <li>
                                 <a href="ecommerce-orders.html">Sub-catégories</a>
                             </li>
                             <li>
                                 <a href="ecommerce-orders-detail.html">Orders</a>
                             </li>
                             <li>
                                 <a href="ecommerce-orders-detail.html">Deliveries</a>
                             </li>
                             <li>
                                 <a href="ecommerce-orders-detail.html">Stock</a>
                             </li>
                         </ul>
                     </div>
                 </li>


                 <li>
                     <a href="#sidebarEmail" data-bs-toggle="collapse" aria-expanded="false"
                         aria-controls="sidebarEmail">
                         <i class="ri-task-line"></i>
                         <span> Activities </span>
                         <span class="menu-arrow"></span>
                     </a>
                     <div class="collapse" id="sidebarEmail">
                         <ul class="nav-second-level">
                             <li>
                                 <a href="email-inbox.html">Projects</a>
                             </li>
                             <li>
                                 <a href="email-read.html">Tasks</a>
                             </li>
                             <li>
                                 <a href="email-templates.html">Reports</a>
                             </li>
                         </ul>
                     </div>
                 </li>

                 <li>
                     <a href="{{ route('users.index') }}">
                         <i class="ri-group-line"></i>
                         <span> Users </span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('settings.index') }}">
                         <i class="ri-settings-4-line"></i>
                         <span> Settings </span>
                     </a>
                 </li>
             </ul>

         </div>
         <!-- End Sidebar -->

         <div class="clearfix"></div>

     </div>
     <!-- Sidebar -left -->

 </div>
 <!-- Left Sidebar End -->
