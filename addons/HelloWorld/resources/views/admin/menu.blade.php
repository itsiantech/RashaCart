<li class="menu-item {{ Route::is('admin.addons.hello-world') ? 'active' : '' }}">
    <a wire:navigate href="{{ route('admin.addons.hello-world') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-smile"></i>
        <div>Hello World</div>
    </a>
</li>
