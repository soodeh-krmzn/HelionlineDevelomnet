<!-- Menu -->
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            {{ \App\Models\Admin\Menu::showMenus() }}
        </ul>
    </div>
</aside>
<!-- / Menu -->
