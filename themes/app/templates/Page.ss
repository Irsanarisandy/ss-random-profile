<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <%-- $MetaTags have to set to false to set custom title --%> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    $MetaTags(false)
    <title>$SiteConfig.Title</title>
    <% if $IsDev %>
        <% require themedCSS("dist/app") %>
    <% else %>
        <% require themedCSS("dist/app.min") %>
    <% end_if %>
</head>
<body id="$ClassName">
    <div id="app">
        <% include Header %>
        <div class="container">
            <main class="my-4">
                $Layout
            </main>
            <% include Footer %>
        </div>
        <% if $IsDev %>
            <% require themedJavascript("dist/app") %>
        <% else %>
            <% require themedJavascript("dist/app.min") %>
        <% end_if %>
    </div>
</body>
</html>
