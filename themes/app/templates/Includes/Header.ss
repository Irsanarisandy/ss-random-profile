<main-nav>
    <a slot="baselink" class="navbar-brand" href="$baseURL">$SiteConfig.Title</a>
    <ul slot="collapsible" class="navbar-nav">
        <% loop $Menu(1) %>
            <% if $Link != $baseURL %>
                <li class="nav-item">
                    <a class="nav-link<% if $IsCurrent %> active<% end_if %>" href="$Link">
                        $MenuTitle
                    </a>
                </li>
            <% end_if %>
        <% end_loop %>
        <li class="nav-item">
            <a class="nav-link<% if $IsCurrent %> active<% end_if %>" href="$baseURL\users/fetchall">Random Users</a>
        </li>
    </ul>
    <div slot="status">
        <% if $CurrentMember.FirstName %>
            <p class="navbar-nav">Welcome, $CurrentMember.FirstName $CurrentMember.Surname</p>
        <% else %>
            <a class="navbar-nav" href="$baseURL\admin">
                <p>Log in</p>
            </a>
        <% end_if %>
    </div>
</main-nav>
