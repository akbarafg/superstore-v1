<script setup>
import { ref, reactive, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ColorMode from '@/Components/ColorMode.vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

// Load initial language and direction settings
function initializeLanguage() {
  const savedLocale = localStorage.getItem('locale') || 'en';
  const savedDirection = localStorage.getItem('direction') || 'ltr';

  locale.value = savedLocale;
  document.documentElement.setAttribute('dir', savedDirection);
  document.documentElement.setAttribute('lang', savedLocale);

  // Load custom CSS based on saved direction
  loadCustomCss(savedDirection);
}

function changeLanguage(event) {
  const selectedLocale = event.target.value;
  const direction = selectedLocale === 'fa' || selectedLocale === 'ps' ? 'rtl' : 'ltr';

  locale.value = selectedLocale;
  document.documentElement.setAttribute('dir', direction);
  document.documentElement.setAttribute('lang', selectedLocale);

  // Save settings to localStorage
  localStorage.setItem('locale', selectedLocale);
  localStorage.setItem('direction', direction);


  // Load custom CSS based on direction
  loadCustomCss(direction);

}

// Load custom CSS for RTL/LTR direction
function loadCustomCss(direction) {
  const existingLink = document.getElementById('stylesheet');
  if (existingLink) {
    existingLink.remove();  // Remove the existing CSS link
  }

  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.id = 'stylesheet';

  if (direction === 'rtl') {
    link.href = 'assets/css/rtl/all.min.css'; 
  } else {
    link.href = 'assets/css/ltr/all.min.css';  
  }

  document.head.appendChild(link);  // Append the new CSS link
}


onMounted(() => {
  initializeLanguage();

});

// Reactive state for sidebar 
const isSidebarFixed = ref(false);  // Whether the sidebar is fixed


// Detect OS for custom scrollbars
const detectOS = () => {
  const platform = window.navigator.platform;
  const windowsPlatforms = ["Win32", "Win64", "Windows", "WinCE"];
  const customScrollbarsClass = "custom-scrollbars";

  if (windowsPlatforms.includes(platform)) {
    document.documentElement.classList.add(customScrollbarsClass);
  }
};

// Initialize sidebar functionality
const initializeSidebars = () => {
  initSidebarMainResize();
};

// Main Sidebar: Resize
const initSidebarMainResize = () => {
  const sidebarMain = document.querySelector(".sidebar-main");
  const togglers = document.querySelectorAll(".sidebar-main-resize");
  const resizeClass = "sidebar-main-resized";
  const unfoldClass = "sidebar-main-unfold";
  const unfoldDelay = 150;
  let timerStart, timerFinish;

  if (sidebarMain) {
    togglers.forEach((toggler) =>
      toggler.addEventListener("click", (e) => {
        e.preventDefault();
        sidebarMain.classList.toggle(resizeClass);
        if (!sidebarMain.classList.contains(resizeClass)) {
          sidebarMain.classList.remove(unfoldClass);
        }
      })
    );

    sidebarMain.addEventListener("mouseenter", () => {
      clearTimeout(timerFinish);
      timerStart = setTimeout(() => {
        if (sidebarMain.classList.contains(resizeClass)) {
          sidebarMain.classList.add(unfoldClass);
        }
      }, unfoldDelay);
    });

    sidebarMain.addEventListener("mouseleave", () => {
      clearTimeout(timerStart);
      timerFinish = setTimeout(() => {
        sidebarMain.classList.remove(unfoldClass);
      }, unfoldDelay);
    });
  }
};

// Lifecycle hook
onMounted(() => {
  detectOS();
  initializeSidebars();
});

// Sidebar state
const isSidebarOpen = ref(false);

// Close sidebar on outside click (optional)
onMounted(() => {
  document.addEventListener('click', (event) => {
    const sidebar = document.querySelector('.sidebar-main');
    const toggleButton = document.querySelector('.sidebar-mobile-main-toggle');

    if (sidebar && toggleButton) {
      if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
        closeSidebar();
      }
    }
  });
});


// Reactive state for sidebar visibility
const isSidebarVisible = ref(false);  // Whether the sidebar is visible

// Function to toggle sidebar visibility
const toggleSidebar = () => {
  isSidebarVisible.value = !isSidebarVisible.value;
  console.log("ok working!");
};

// Function to close the sidebar
const closeSidebar = () => {
  isSidebarVisible.value = false;
};



// Main Sidebar

// Reactive state
const currentOpenSubmenu = ref(null); // Tracks the currently open submenu

// Function to toggle submenu
const toggleSubmenu = (key) => {
  currentOpenSubmenu.value = currentOpenSubmenu.value === key ? null : key;
};

</script>

<template>
  
    <!-- Main navbar -->
    <div class="navbar navbar-dark navbar-expand-lg navbar-static border-bottom border-bottom-white border-opacity-10">
        <div class="container-fluid">
            <div class="d-flex d-lg-none me-2">
                <button
                type="button"
                class="navbar-toggler sidebar-mobile-main-toggle rounded-pill"
                @click="toggleSidebar"
                >
                <i class="ph-list"></i>
                </button>
            </div>

            <div class="navbar-brand flex-1 flex-lg-0"></div>

            <ul class="nav flex-row justify-content-end order-1 order-lg-2">
                <li class="nav-item nav-item-dropdown-lg dropdown language-switch">
                    <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill show" data-bs-toggle="dropdown" aria-expanded="true">
                        <img src="assets/img/gb.svg" height="22" alt="img">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" data-bs-popper="static">
                        <a href="#" class="dropdown-item en active">
                            <div>
                              <select v-model="locale" @change="changeLanguage"
                                  class="px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                                  <option value="en">English</option>
                                  <option value="ps">پښتو</option>
                                  <option value="fa">دری</option>
                              </select>                              
                                <div>
                                </div>
                            </div>
                        </a>
                    </div>
                </li>
            
                <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
                    <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
                        <div class="status-indicator-container">
                             <img src="assets/img/face11.jpg" class="w-32px h-32px rounded-pill" alt="img">
                            <span class="status-indicator bg-success"></span>
                        </div>
                        <span class="d-none d-lg-inline-block mx-lg-2">{{ $page.props.auth.user.name }}</span>
                    </a>

                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                    </template>
                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> {{ $t('pages.authenticate.profile') }} </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                           {{ $t('pages.authenticate.logout') }}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                    </div>
                    <div class="dropdown-menu dropdown-menu-end">

                        <a href="#" class="dropdown-item">
                            <i class="ph-user-circle me-2"></i>
                            <DropdownLink :href="route('profile.edit')">{{ $t('pages.authenticate.account_settings') }}</DropdownLink>
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="ph-sign-out me-2"></i>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                 {{ $t('pages.authenticate.logout') }}
                            </DropdownLink>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <!-- /main navbar -->
    <div 
        class="bg-gray-100"
        style="height: 100vh; overflow: auto;"
        ref="scrollContainer"
    > 
    <div 
      class="page-content" >
            
   <!-- Main sidebar -->
   <div
    :class="['sidebar sidebar-dark sidebar-main sidebar-expand-lg flex min-h-screen h-auto', { 'sidebar-visible': isSidebarVisible }]"
    >

  <!-- Sidebar content -->
  <div class="sidebar-content">

      <!-- Sidebar header -->
      <div class="sidebar-section">
          <div class="sidebar-section-body d-flex justify-content-center">
              <h5 class="sidebar-resize-hide flex-grow-1 my-auto">
                <img src="assets/img/superstore.PNG" alt="logo" class="mt-[-20px]">
              </h5>
              <div>
                  <button type="button" class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex">
                      <PhArrowsLeftRight :size="20" />
                  </button>

                  <button
                  type="button"
                  class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none"
                  @click="closeSidebar"
                  >
                  <Ph-X :size="20" />
                  </button>
              </div>
          </div>
      </div>
      <!-- /sidebar header -->


      <!-- Main navigation -->
      <div class="sidebar-section">
          <ul class="nav nav-sidebar" data-nav-type="accordion">

              <!-- Main -->
              <li class="nav-item-header pt-0">
                  <!-- <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">Main</div> -->
                  <i class="ph-dots-three sidebar-resize-show"></i>
              </li>
              <li class="nav-item">
                  <Link href="dashboard" class="nav-link active">
                      <i class="ph-house"></i>
                      <span>
                          {{ $t('pages.authenticate.dashboard') }}
                      </span>
                  </Link>
              </li>

              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('categories')">
                  <a href="#" class="nav-link">
                      <i class="ph-layout"></i>        
                      <span>{{ $t('pages.authenticate.categories') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'categories' }">
                      <li class="nav-item"><Link href="/categories" class="nav-link active">{{ $t('pages.authenticate.add') }}</Link></li>          
                  </ul>
              </li>
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('suppliers')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-person"></i>
                      <span>{{ $t('pages.authenticate.suppliers') }}</span>
                  </a>
                  <ul class="nav-group-sub " :class="{ collapse: currentOpenSubmenu !== 'suppliers' }">
                      <li class="nav-item"><a href="/suppliers" class="nav-link active">{{ $t('pages.authenticate.add') }}</a></li>
                  </ul>
              </li>
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('customers')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-person-simple"></i>
                      <span>{{ $t('pages.authenticate.customers') }}</span>
                  </a>
                  <ul class="nav-group-sub"  :class="{ collapse: currentOpenSubmenu !== 'customers' }">
                      <li class="nav-item"><Link href="/customers" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
                </li>
                <li class="nav-item nav-item-submenu" @click="toggleSubmenu('products')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-barcode"></i>
                      <span>{{ $t('pages.authenticate.products') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'products' }">
                      <li class="nav-item"><Link href="products" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
              </li>

              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('orders')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-shopping-cart"></i>
                      <span>{{ $t('pages.authenticate.orders') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'orders' }">
                      <li class="nav-item"><Link href="/orders" class="nav-link">{{ $t('pages.authenticate.add')}}</Link></li>
                  </ul>
              </li>
              
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('order_details')">
                  <a href="#" class="nav-link">
                      <i class="ph-note-pencil"></i>
                      <span>{{ $t('pages.authenticate.order_details') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'order_details' }">
                      <li class="nav-item"><Link href="order_details" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
              </li>

              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('shoppings')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-truck"></i>
                      <span>{{ $t('pages.authenticate.shoppings') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'shoppings' }">
                      <li class="nav-item"><Link href="shoppings" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
              </li>
      
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('shopping_details')">
                  <a href="#" class="nav-link">
                      <i class="ph-note-pencil"></i>
                      <span>{{ $t('pages.authenticate.shopping_details') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'shopping_details' }">
                      <li class="nav-item"><Link href="shopping_details" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
              </li>
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('employees')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-person-simple-walk"></i>
                      <span>{{ $t('pages.authenticate.employees') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'employees' }">
                      <li class="nav-item"><Link href="employees" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
              </li>
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('expenses')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-currency-dollar"></i>
                      <span>{{ $t('pages.authenticate.expenses') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'expenses' }">
                      <li class="nav-item"><Link href="expenses" class="nav-link">{{ $t('pages.authenticate.add') }}</Link></li>
                  </ul>
              </li>
              
              <li class="nav-item nav-item-submenu" @click="toggleSubmenu('backup')">
                  <a href="#" class="nav-link">
                      <i class="ph ph-currency-dollar"></i>
                      <span>{{ $t('pages.authenticate.backup') }}</span>
                  </a>
                  <ul class="nav-group-sub" :class="{ collapse: currentOpenSubmenu !== 'backup' }">
                      <li class="nav-item"><Link href="backup" class="nav-link">{{ $t('pages.authenticate.backup') }}</Link></li>
                  </ul>
              </li>
              <!-- /page kits -->

          </ul>
      </div>
      <!-- /main navigation -->

  </div>
  <!-- /sidebar content -->

  </div>
  <!-- /main sidebar -->
  <div 
      class="content-wrapper">
          <div 
              class="content-inner">
          <div 
              class="content">
              <main>
                <slot />
              </main>
          </div>
          </div>
      </div>
  </div>
</div>
      
<!-- Demo config -->
  <ColorMode />
<!-- /demo config -->
</template>

<style>

  @media (max-width: 768px) {
      .sidebar {
        overflow: visible!important;
        transition: width 0.3s!important;
      }

      .sidebar-visible {
        width: 600px!important;
      }
      
      .sidebar-content {
        transition: margin 0.3s ease-in-out;
      }

      :dir(ltr) .sidebar-content {
        margin-left: 331px !important;
        margin-right: 0 !important;
      }

      :dir(rtl) .sidebar-content {
        margin-left: 0 !important;
        margin-right: 331px !important;
      }
      .page-content {
        margin-left: 0px !important;
      }
    } 
  .content {
    padding: 0 !important;
  }

</style>


