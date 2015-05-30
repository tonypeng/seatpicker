<?php

abstract class SiteController extends Controller {
    public abstract function getPageTitle();

    public final function render() {
        $body = $this->renderPage();

        ob_start();
        ?>
        <!DOCTYPE HTML>
        <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <title><?= $this->getPageTitle(); ?></title>
            <link href='http://fonts.googleapis.com/css?family=Roboto:400italic,700,400' rel='stylesheet' type='text/css'>
            <link rel="stylesheet" type="text/css" href="<?= linkto('/style/reset.css'); ?>" />
            <link rel="stylesheet" type="text/css" href="<?= linkto('/style/border.css'); ?>" />
            <link rel="stylesheet" type="text/css" href="<?= linkto('/style/spacing.css'); ?>" />
            <link rel="stylesheet" type="text/css" href="<?= linkto('/style/style.css'); ?>" />
            <link rel="stylesheet" type="text/css" href="<?= linkto('/style/components.css'); ?>" />
            <link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.css" />
            <script src="<?= linkto('/script/jquery-2.1.1.min.js') ?>" type="text/javascript"></script>
            <script src="<?= linkto('/script/seatpicker.component.js') ?>" type="text/javascript"></script>
            <script src="http://cdn.jsdelivr.net/qtip2/2.2.1/jquery.qtip.min.js" type="text/javascript"></script>
            <link rel="icon" href="<?= linkto('/favicon.ico'); ?>" />
        </head>
        <body>
        <?php

        $header =  ob_get_clean();

        /* END HEADER */
        /* START FOOTER */

        ob_start();
        ?>
        </body>
        </html>
        <?php
        $footer = ob_get_clean();

        return $header.$body.$footer;
    }

    public function isSingleColumn() {
        return true;
    }

    public abstract function renderPage();
}