<?php
function view($name, $data = []) {
    extract($data);
    include "layouts/header.php";
    include "pages/{$name}.php";
    include "layouts/footer.php";
}

function adminView($name, $data = []) {
    extract($data);
    include "pages/admin/layouts/header.php";
    include "pages/admin/{$name}.php";
}
