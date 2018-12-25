<table class="random-users mx-auto my-3">
    <thead>
        <tr>
            <td class="random-users__data pl-0">Full Name</td>
            <td class="random-users__data">Contact Details</td>
            <td class="random-users__data pr-0">Profile Photo</td>
        </tr>
    </thead>
    <tbody>
        <% loop $RandomUsers %>
            <tr>
                <td class="random-users__data pl-0">$FirstName $LastName</td>
                <td class="random-users__data">$CellNo<br>$Email</td>
                <td class="random-users__data pr-0 py-2 text-center">
                    <picture>
                        <source srcset="$MediumPhoto" media="(min-width: 768px)" />
                        <img src="$SmallPhoto" alt="$FirstName $LastName"/>
                    </picture>
                </td>
            </tr>
        <% end_loop %>
    </tbody>
</table>
<a class="btn btn-warning mb-3" role="button" href="$baseURL\user/fetchrandom">Generate New User</a>
