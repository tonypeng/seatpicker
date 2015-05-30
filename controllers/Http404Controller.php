<?php
class Http404Controller extends SiteController {
    public function getPageTitle() {
        return 'Graduation: 404';
    }

    public function renderPage() {
        ob_start();

?>
        404
<?php

        return ob_get_clean();
    }
}