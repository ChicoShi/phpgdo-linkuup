<?php
namespace GDO\LinkUUp\thm\lup\UI\tpl;

// Keep all unrelated PHPGDO pages on their original theme.
global $me;
if (!\GDO\LinkUUp\BackendLayout::supports($me)) {
    require GDO_PATH . "GDO/Bootstrap5Theme/thm/bs5/UI/tpl/page_html.php";
    return;
}

use GDO\Core\Javascript;
use GDO\Core\Website;
use GDO\Language\Trans;
use GDO\UI\GDT_Loading;
use GDO\UI\GDT_Page;

/** @var $page GDT_Page * */
?>
<!DOCTYPE html>
<html class="nojs lup-backend-ui" lang="<?=Trans::$ISO?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?=$page->renderTitle()?></title>
	<?=Website::displayHead()?>
	<?=Website::displayMeta()?>
	<?=Website::displayLink()?>
</head>
<body class="nojs lup-backend">
<div class="d-flex" id="wrapper">
    <!-- Sidebar-->
    <aside class="border-end bg-body-tertiary" id="sidebar-wrapper" aria-label="<?=t('menu')?>">
        <ul id="leftnav" class="list-group list-group-flush">
			<?php
			foreach ($page->leftBar()->getFields() as $gdt) : ?>
                <li class="list-group-item list-group-item-action"><?=$gdt->renderHTML()?></li>
			<?php
			endforeach; ?>
        </ul>
    </aside>
    <!-- Page content wrapper-->
    <div id="page-content-wrapper">
        <!-- Top navigation-->
        <nav class="navbar lup-shell-header">
            <div class="container-fluid">
                <button class="btn btn-primary" id="sidebarToggle" type="button" aria-controls="sidebar-wrapper" aria-label="<?=t('toggle_sidebar')?>"><span class="navbar-toggler-icon" aria-hidden="true"></span></button>
                <div class="navbar-brand"><?=$page->topBar()->renderHTML()?></div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav ms-auto mt-2 mt-lg-0">
						<?php
						foreach ($page->rightBar()->getFields() as $gdt) : ?>
                            <li class="nav-item"><span class="nav-link"><?=$gdt->renderHTML()?></span></li>
						<?php
						endforeach; ?>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- topTabs -->
        <!-- Page content-->
        <main id="content-wrap" class="container-fluid" tabindex="-1">

			<?=$page->topResponse()->renderHTML()?>
			<?=$page->html?:''?>
        </main>
    </div>
</div>
<footer><?=$page->bottomBar()->addClass('gdt-footer')->renderHTML()?></footer>
<?=GDT_Loading::make()->renderHTML()?>
<script src="<?=\GDO\LinkUUp\Module_LinkUUp::instance()->wwwPath('js/lup-backend-shell.js?rev=20260920_9')?>"></script>
<?=Javascript::displayJavascripts()?>
</body>
</html>
