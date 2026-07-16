<nav class="nav-bottom fixed-bottom">
    <ul>
        <li class="list {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" title="Dashboard">
                <span class="icon">
                    <i class='bx bxs-home icon'></i>
                </span>
            </a>
        </li>
        <li class="list {{ request()->routeIs('categories.index') ? 'active' : '' }}">
            <a href="{{ route('categories.index') }}" title="Categories">
                <span class="icon">
                    <i class='bx bxs-purchase-tag icon'></i>
                </span>
            </a>
        </li>
        <li class="list">
            <a href="" title="Create Transactions">
                <span class="icon">
                    <i class='bx bx-qr-scan icon'></i>
                </span>
            </a>
        </li>
        <li class="list">
            <a href="" title="Transactions">
                <span class="icon">
                    <i class='bx bx-history icon'></i>
                </span>
            </a>
        </li>
        <li class="list {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" title="Profile">
                <span class="icon">
                    <i class='bx bxs-user icon'></i>
                </span>
            </a>
        </li>
    </ul>
</nav>