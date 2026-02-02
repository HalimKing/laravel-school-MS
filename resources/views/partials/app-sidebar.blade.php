<!-- Sidebar Navigation -->

<!-- Sidebar Navigation -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            School<span>MS</span>
        </a>
        <div class="sidebar-toggler">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="sidebar-body">
        <ul class="nav" id="sidebarNav">
            @php
                // Helper function to check active state with exact and wildcard matching
                function isActive($patterns, $currentRoute = null) {
                    $currentRoute = $currentRoute ?? request()->route()->getName();
                    
                    if (is_array($patterns)) {
                        foreach ($patterns as $pattern) {
                            // Check for exact match first (without wildcards)
                            if ($currentRoute === $pattern) {
                                return true;
                            }
                            
                            // Check for wildcard match
                            if (str_ends_with($pattern, '*')) {
                                $basePattern = rtrim($pattern, '*');
                                if (str_starts_with($currentRoute, $basePattern)) {
                                    // Additional check to prevent overlapping patterns
                                    // Only match if the next character after base is a dot or end of string
                                    $nextChar = substr($currentRoute, strlen($basePattern), 1);
                                    if ($nextChar === '.' || $nextChar === '') {
                                        return true;
                                    }
                                }
                            }
                        }
                        return false;
                    }
                    
                    return $currentRoute === $patterns;
                }

                // Helper function to check if any child is active
                function isChildActive($items, $currentRoute = null) {
                    $currentRoute = $currentRoute ?? request()->route()->getName();
                    foreach ($items as $item) {
                        if (isset($item['active']) && isActive($item['active'], $currentRoute)) {
                            return true;
                        }
                    }
                    return false;
                }
                
                $currentRoute = request()->route()->getName();
                
                $sidebarMenu = [
                    // Main Section
                    [
                        'type' => 'category',
                        'label' => 'Main',
                    ],
                    [
                        'type' => 'link',
                        'label' => 'Dashboard',
                        'url' => route('dashboard'),
                        'icon' => 'home',
                        'active' => ['dashboard'],
                    ],

                    // School Management Section
                    [
                        'type' => 'category',
                        'label' => 'School Management',
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'students',
                        'label' => 'Students',
                        'icon' => 'users',
                        'items' => [
                            [
                                'label' => 'All Students',
                                'url' => route('admin.students.index'),
                                'active' => ['admin.students.index']
                            ],
                            [
                                'label' => 'Add Student',
                                'url' => route('admin.students.create'),
                                'active' => ['admin.students.create']
                            ],
                            [
                                'label' => 'Student Reports',
                                'url' => route('students.reports'),
                                'active' => ['students.reports']
                            ],
                        ],
                        'active_patterns' => ['admin.students.index', 'admin.students.create', 'admin.students.show', 'admin.students.edit', 'students.reports']
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'teachers',
                        'label' => 'Teachers',
                        'icon' => 'award',
                        'items' => [
                            [
                                'label' => 'All Teachers',
                                'url' => route('admin.teachers.index'),
                                'active' => ['admin.teachers.index']
                            ],
                            [
                                'label' => 'Add Teacher',
                                'url' => route('admin.teachers.create'),
                                'active' => ['admin.teachers.create']
                            ],
                            [
                                'label' => 'Password Management',
                                'url' => route('admin.teacher.password.index'),
                                'active' => ['admin.teacher.password.index']
                            ],
                        ],
                        'active_patterns' => ['admin.teachers.index', 'admin.teachers.create', 'admin.teacher.password.index']
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'classes',
                        'label' => 'Classes',
                        'icon' => 'book-open',
                        'items' => [
                            [
                                'label' => 'All Classes',
                                'url' => route('admin.classes.index'),
                                'active' => ['admin.classes.index']
                            ],
                            [
                                'label' => 'Add Class',
                                'url' => route('admin.classes.create'),
                                'active' => ['admin.classes.create']
                            ],
                        ],
                        'active_patterns' => ['admin.classes.index', 'admin.classes.create', 'admin.classes.edit']
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'academics',
                        'label' => 'Academics',
                        'icon' => 'book',
                        'items' => [
                            [
                                'label' => 'Subjects',
                                'url' => route('admin.academics.subjects.index'),
                                'active' => ['admin.academics.subjects.index', 'subjects.edit', 'admin.academics.subjects.edit']
                            ],
                            [
                                'label' => 'Assign Subjects',
                                'url' => route('admin.academics.assign-subjects.index'),
                                'active' => ['admin.academics.assign-subjects.index','admin.academics.assign-subjects.edit']
                            ],
                            [
                                'label' => 'All Sessions',
                                'url' => route('admin.sessions.index'),
                                'active' => ['admin.sessions.index']
                            ],
                            [
                                'label' => 'Add Session',
                                'url' => route('admin.sessions.create'),
                                'active' => ['admin.sessions.create']
                            ],
                            [
                                'label' => 'Academic Periods',
                                'url' => route('admin.academics.academic-periods.index'),
                                'active' => ['admin.academics.academic-periods.index']
                            ],
                            [
                                'label' => 'Add Academic Period',
                                'url' => route('admin.academics.academic-periods.create'),
                                'active' => ['admin.academics.academic-periods.create']
                            ],
                        ],
                        'active_patterns' => [
                            'subjects.index',
                            'subjects.create',
                            'subjects.edit',
                            'subjects.show',
                            'admin.sessions.index',
                            'admin.sessions.create',
                            'admin.academics.academic-periods.index',
                            'admin.academics.academic-periods.create',
                            'admin.academics.subjects.index',
                            'admin.academics.subjects.edit',
                            'admin.academics.assign-subjects.index',
                            'admin.academics.assign-subjects.create',
                            'admin.academics.assign-subjects.edit'
                        ]
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'enrollments',
                        'label' => 'Enrollments',
                        'icon' => 'book',
                        'items' => [
                            [
                                'label' => 'Enrollment List',
                                'url' => route('admin.enrollments.enrollment-list.index'),
                                'active' => ['admin.enrollments.enrollment-list.index']
                            ],
                            [
                                'label' => 'Enroll Students',
                                'url' => route('admin.enrollments.enroll-students.index'),
                                'active' => ['admin.enrollments.enroll-students.index']
                            ],
                        ],
                        'active_patterns' => [
                            'admin.enrollments.enrollment-list.index',
                            'admin.enrollments.enroll-students.index'
                            
                        ]
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'results',
                        'label' => 'Results Management',
                        'icon' => 'file-text',
                        'items' => [
                            [
                                'label' => 'Single Upload',
                                'url' => route('results.single-upload'),
                                'active' => ['results.single-upload']
                            ],
                            [
                                'label' => 'Bulk Upload',
                                'url' => route('results.bulk-upload'),
                                'active' => ['results.bulk-upload']
                            ],
                        ],
                        'active_patterns' => ['results.single-upload', 'results.bulk-upload']
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'fees',
                        'label' => 'Fees Management',
                        'icon' => 'dollar-sign',
                        'items' => [
                            [
                                'label' => 'Fee Category',
                                'url' => route('admin.fee-management.fee-categories.index'),
                                'active' => ['admin.fee-management.fee-categories.index']
                            ],
                            [
                                'label' => 'Fees',
                                'url' => route('admin.fee-management.fees.index'),
                                'active' => ['admin.fee-management.fees.index']
                            ],
                            [
                                'label' => 'Fee Collection',
                                'url' => route('admin.fee-management.collect-fees.index'),
                                'active' => ['admin.fee-management.collect-fees.index']
                            ],
                        ],
                        'active_patterns' => [
                            'admin.fee-management.fee-categories.index',
                            'admin.fee-management.fee-categories.create',
                            'admin.fee-management.fee-categories.edit',
                            'admin.fee-management.fees.index',
                            'admin.fee-management.fees.create',
                            'admin.fee-management.fees.edit',
                            'admin.fee-management.collect-fees.index'
                            ]
                    ],

                    [
                        'type' => 'category',
                        'label' => 'Administration',
                    ],

                    [
                        'type' => 'menu',
                        'id' => 'communications',
                        'label' => 'Communications',
                        'icon' => 'mail',
                        'items' => [
                            [
                                'label' => 'Announcements',
                                'url' => route('announcements.index'),
                                'active' => ['announcements.index', 'announcements.create', 'announcements.edit']
                            ],
                        ],
                        'active_patterns' => ['announcements.index', 'announcements.create', 'announcements.edit']
                    ],

                    [
                        'type' => 'menu',
                        'id' => 'events',
                        'label' => 'Events',
                        'icon' => 'calendar',
                        'items' => [
                            [
                                'label' => 'All Events',
                                'url' => route('events.index'),
                                'active' => ['events.index']
                            ],
                            [
                                'label' => 'Add Event',
                                'url' => route('events.create'),
                                'active' => ['events.create']
                            ],
                        ],
                        'active_patterns' => ['events.index', 'events.create']
                    ],

                    // Reports Section
                    [
                        'type' => 'category',
                        'label' => 'Reports',
                    ],
                    [
                        'type' => 'menu',
                        'id' => 'reports',
                        'label' => 'Reports',
                        'icon' => 'pie-chart',
                        'items' => [
                            [
                                'label' => 'Student Report',
                                'url' => route('reports.students'),
                                'active' => ['reports.students']
                            ],
                            [
                                'label' => 'Attendance Report',
                                'url' => route('reports.attendance'),
                                'active' => ['reports.attendance']
                            ],
                            [
                                'label' => 'Financial Report',
                                'url' => route('reports.finance'),
                                'active' => ['reports.finance']
                            ],
                        ],
                        'active_patterns' => ['reports.students', 'reports.attendance', 'reports.finance']
                    ],

                    // Settings Section
                    [
                        'type' => 'category',
                        'label' => 'Settings',
                    ],

                    [
                        'type' => 'menu',
                        'id' => 'settings',
                        'label' => 'Settings',
                        'icon' => 'settings',
                        'items' => [
                            [
                                'label' => 'Users',
                                'url' => route('users.index'),
                                'active' => ['users.index', 'users.create', 'users.edit']
                            ],
                            [
                                'label' => 'Roles & Permissions',
                                'url' => route('roles.index'),
                                'active' => ['roles.index', 'roles.create', 'roles.edit']
                            ],
                            [
                                'label' => 'School Settings',
                                'url' => route('settings.school'),
                                'active' => ['settings.school']
                            ],
                            [
                                'label' => 'System Settings',
                                'url' => route('settings.system'),
                                'active' => ['settings.system']
                            ],
                        ],
                        'active_patterns' => [
                            'users.index',
                            'users.create',
                            'users.edit',
                            'roles.index',
                            'roles.create',
                            'roles.edit',
                            'settings.school',
                            'settings.system'
                        ]
                    ],
                ];
            @endphp

            @foreach($sidebarMenu as $section)
                @if($section['type'] === 'category')
                    <li class="nav-item nav-category">{{ $section['label'] }}</li>
                @elseif($section['type'] === 'link')
                    @php
                        $isActive = isset($section['active']) && isActive($section['active']);
                    @endphp
                    <li class="nav-item">
                        <a href="{{ $section['url'] }}" 
                           class="nav-link {{ $isActive ? 'active' : '' }}">
                            <i class="link-icon" data-lucide="{{ $section['icon'] }}"></i>
                            <span class="link-title">{{ $section['label'] }}</span>
                        </a>
                    </li>
                @elseif($section['type'] === 'menu')
                    @php
                        $isMenuActive = false;
                        
                        // Check if this specific menu should be active
                        if (isset($section['active_patterns'])) {
                            $isMenuActive = isActive($section['active_patterns']);
                        }
                    @endphp
                    
                    <li class="nav-item {{ $isMenuActive ? 'active' : '' }}">
                        <a class="nav-link " 
                           data-bs-toggle="collapse" 
                           href="#{{ $section['id'] }}" 
                           role="button" 
                           aria-expanded="{{ $isMenuActive ? 'true' : 'false' }}" 
                           aria-controls="{{ $section['id'] }}">
                            <i class="link-icon" data-lucide="{{ $section['icon'] }}"></i>
                            <span class="link-title">{{ $section['label'] }}</span>
                            <i class="link-arrow" data-lucide="chevron-down"></i>
                        </a>
                        <div class="collapse {{ $isMenuActive ? 'show' : '' }}" 
                             data-bs-parent="#sidebarNav" 
                             id="{{ $section['id'] }}">
                            <ul class="nav sub-menu">
                                @foreach($section['items'] as $item)
                                    @php
                                        $isItemActive = isset($item['active']) && isActive($item['active']);
                                    @endphp
                                    <li class="nav-item">
                                        <a href="{{ $item['url'] }}" 
                                           class="nav-link {{ $isItemActive ? 'active' : '' }}">
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</nav>









































































	<!-- <nav class="sidebar">
      <div class="sidebar-header">
        <a href="#" class="sidebar-brand">
          Noble<span>UI</span>
        </a>
        <div class="sidebar-toggler">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      <div class="sidebar-body">
        <ul class="nav" id="sidebarNav">
          <li class="nav-item nav-category">Main</li>
          <li class="nav-item active">
            <a href="dashboard.html" class="nav-link">
              <i class="link-icon" data-lucide="box"></i>
              <span class="link-title active">Dashboard</span>
            </a>
          </li>
          <li class="nav-item nav-category">School Management</li>
          <li class="nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <a class="nav-link " data-bs-toggle="collapse" href="#students" role="button" aria-expanded="false" aria-controls="students">
              <i class="link-icon" data-lucide="mail"></i>
              <span class="link-title">Students</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="students">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('admin.students.index') }}" class="nav-link {{ request()->routeIs('admin.students.index') ? 'active' : '' }}">All Students</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.students.create') }}" class="nav-link {{ request()->routeIs('admin.students.create') ? 'active' : '' }}">Add Student</a>
                </li>
                
              </ul>
            </div>
          </li>
          <li class="nav-item {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }}">
            <a class="nav-link " data-bs-toggle="collapse" href="#teachers" role="button" aria-expanded="false" aria-controls="teachers">
              <i class="link-icon" data-lucide="mail"></i>
              <span class="link-title">Teacher</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="teachers">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="{{ route('admin.teachers.index') }}" class="nav-link {{ request()->routeIs('admin.teachers.index') ? 'active' : '' }}">All Teachers</a>
                </li>
                <li class="nav-item">
                  <a href="{{ route('admin.teachers.create') }}" class="nav-link {{ request()->routeIs('admin.teachers.create') ? 'active' : '' }}">Add Teacher</a>
                </li>
                
              </ul>
            </div>
          </li>
         
          <li class="nav-item nav-category">Components</li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#uiComponents" role="button" aria-expanded="false" aria-controls="uiComponents">
              <i class="link-icon" data-lucide="feather"></i>
              <span class="link-title">UI Kit</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="uiComponents">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/ui-components/accordion.html" class="nav-link">Accordion</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/alerts.html" class="nav-link">Alerts</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/badges.html" class="nav-link">Badges</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/breadcrumbs.html" class="nav-link">Breadcrumbs</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/buttons.html" class="nav-link">Buttons</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/button-group.html" class="nav-link">Button group</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/cards.html" class="nav-link">Cards</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/carousel.html" class="nav-link">Carousel</a>
                </li>
                <li class="nav-item">
                    <a href="pages/ui-components/collapse.html" class="nav-link">Collapse</a>
                  </li>
                <li class="nav-item">
                  <a href="pages/ui-components/dropdowns.html" class="nav-link">Dropdowns</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/list-group.html" class="nav-link">List group</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/media-object.html" class="nav-link">Media object</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/modal.html" class="nav-link">Modal</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/navs.html" class="nav-link">Navs</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/offcanvas.html" class="nav-link">Offcanvas</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/pagination.html" class="nav-link">Pagination</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/placeholders.html" class="nav-link">Placeholders</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/popover.html" class="nav-link">Popovers</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/progress.html" class="nav-link">Progress</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/scrollbar.html" class="nav-link">Scrollbar</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/scrollspy.html" class="nav-link">Scrollspy</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/spinners.html" class="nav-link">Spinners</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/tabs.html" class="nav-link">Tabs</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/toasts.html" class="nav-link">Toasts</a>
                </li>
                <li class="nav-item">
                  <a href="pages/ui-components/tooltips.html" class="nav-link">Tooltips</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#advancedUI" role="button" aria-expanded="false" aria-controls="advancedUI">
              <i class="link-icon" data-lucide="anchor"></i>
              <span class="link-title">Advanced UI</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="advancedUI">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/advanced-ui/cropper.html" class="nav-link">Cropper</a>
                </li>
                <li class="nav-item">
                  <a href="pages/advanced-ui/owl-carousel.html" class="nav-link">Owl carousel</a>
                </li>
                <li class="nav-item">
                  <a href="pages/advanced-ui/sortablejs.html" class="nav-link">SortableJs</a>
                </li>
                <li class="nav-item">
                  <a href="pages/advanced-ui/sweet-alert.html" class="nav-link">Sweet Alert</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#forms" role="button" aria-expanded="false" aria-controls="forms">
              <i class="link-icon" data-lucide="inbox"></i>
              <span class="link-title">Forms</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="forms">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/forms/basic-elements.html" class="nav-link">Basic Elements</a>
                </li>
                <li class="nav-item">
                  <a href="pages/forms/advanced-elements.html" class="nav-link">Advanced Elements</a>
                </li>
                <li class="nav-item">
                  <a href="pages/forms/editors.html" class="nav-link">Editors</a>
                </li>
                <li class="nav-item">
                  <a href="pages/forms/wizard.html" class="nav-link">Wizard</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link"  data-bs-toggle="collapse" href="#charts" role="button" aria-expanded="false" aria-controls="charts">
              <i class="link-icon" data-lucide="pie-chart"></i>
              <span class="link-title">Charts</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="charts">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/charts/apex.html" class="nav-link">Apex</a>
                </li>
                <li class="nav-item">
                  <a href="pages/charts/chartjs.html" class="nav-link">ChartJs</a>
                </li>
                <li class="nav-item">
                  <a href="pages/charts/flot.html" class="nav-link">Flot</a>
                </li>
                <li class="nav-item">
                  <a href="pages/charts/peity.html" class="nav-link">Peity</a>
                </li>
                <li class="nav-item">
                  <a href="pages/charts/sparkline.html" class="nav-link">Sparkline</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#tables" role="button" aria-expanded="false" aria-controls="tables">
              <i class="link-icon" data-lucide="layout"></i>
              <span class="link-title">Table</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="tables">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/tables/basic-table.html" class="nav-link">Basic Tables</a>
                </li>
                <li class="nav-item">
                  <a href="pages/tables/data-table.html" class="nav-link">Data Table</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#icons" role="button" aria-expanded="false" aria-controls="icons">
              <i class="link-icon" data-lucide="smile"></i>
              <span class="link-title">Icons</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="icons">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/icons/lucide-icons.html" class="nav-link">Lucide Icons</a>
                </li>
                <li class="nav-item">
                  <a href="pages/icons/flag-icons.html" class="nav-link">Flag Icons</a>
                </li>
                <li class="nav-item">
                  <a href="pages/icons/mdi-icons.html" class="nav-link">Mdi Icons</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item nav-category">Pages</li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#general-pages" role="button" aria-expanded="false" aria-controls="general-pages">
              <i class="link-icon" data-lucide="book"></i>
              <span class="link-title">Special pages</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="general-pages">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/general/blank-page.html" class="nav-link">Blank page</a>
                </li>
                <li class="nav-item">
                  <a href="pages/general/faq.html" class="nav-link">Faq</a>
                </li>
                <li class="nav-item">
                  <a href="pages/general/invoice.html" class="nav-link">Invoice</a>
                </li>
                <li class="nav-item">
                  <a href="pages/general/profile.html" class="nav-link">Profile</a>
                </li>
                <li class="nav-item">
                  <a href="pages/general/pricing.html" class="nav-link">Pricing</a>
                </li>
                <li class="nav-item">
                  <a href="pages/general/timeline.html" class="nav-link">Timeline</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#authPages" role="button" aria-expanded="false" aria-controls="authPages">
              <i class="link-icon" data-lucide="unlock"></i>
              <span class="link-title">Authentication</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="authPages">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/auth/login.html" class="nav-link">Login</a>
                </li>
                <li class="nav-item">
                  <a href="pages/auth/register.html" class="nav-link">Register</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#errorPages" role="button" aria-expanded="false" aria-controls="errorPages">
              <i class="link-icon" data-lucide="cloud-off"></i>
              <span class="link-title">Error</span>
              <i class="link-arrow" data-lucide="chevron-down"></i>
            </a>
            <div class="collapse" data-bs-parent="#sidebarNav" id="errorPages">
              <ul class="nav sub-menu">
                <li class="nav-item">
                  <a href="pages/error/404.html" class="nav-link">404</a>
                </li>
                <li class="nav-item">
                  <a href="pages/error/500.html" class="nav-link">500</a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item nav-category">Docs</li>
          <li class="nav-item">
            <a href="https://nobleui.com/html/documentation/docs.html" target="_blank" class="nav-link">
              <i class="link-icon" data-lucide="hash"></i>
              <span class="link-title">Documentation</span>
            </a>
          </li>
        </ul>
      </div>
    </nav> -->