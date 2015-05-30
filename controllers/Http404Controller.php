<?php
class Http404Controller extends SiteController {
    public function getPageTitle() {
        return 'Graduation: 404';
    }

    public function renderPage() {
        ob_start();

        ?>
        <div id="login-content" class="bg-red">
            <div id="login-vertical-wrap">
                <div id="login-vertical-center" style="text-align: center; color: #fff">
                    <p style="font-size: 96px">404 :(</p>
                    <br />
                    Oh no! The requested page couldn't be found.<br />
                    <a href="<?= linkto('index.php') ?>" style="color: #fff; text-decoration: underline">Go home</a>
                </div>
            </div>
        </div>
        <?php

        return ob_get_clean();
    }
}