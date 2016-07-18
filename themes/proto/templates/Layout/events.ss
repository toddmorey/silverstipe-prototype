<div class="container">

    <h1 class="attention">Event Listing Really Quick</h1>

    <p><a href="{$Yaml.URL}">$Yaml.Notice</a></p>

    <ul>
        <% if $Yaml.Events %>
            <% loop $Yaml.Events %>
                <li>$name</li>
            <% end_loop %>
        <% else %>
            <p>No upcoming events are available.</p>
        <% end_if %>
    </ul>

</div>