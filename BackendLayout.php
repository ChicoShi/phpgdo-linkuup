<?php
declare(strict_types=1);
namespace GDO\LinkUUp;

use GDO\Core\Application;
use GDO\Core\GDT;
use GDO\Core\Method;

/** Presentation scope for full LinkUUp user-facing backend pages only. */
final class BackendLayout
{
    public static function supports(?Method $method): bool
    {
//        if (!$method || Application::$MODE_DETECTED !== GDT::RENDER_WEBSITE ||
//            Application::instance()->isAPI() || !$method->isSidebarEnabled()) return false;
        $parts = explode('\\', get_class($method));
        $module = strtolower($parts[1] ?? '');
        $name = strtolower($method->getMethodName());
        $routes = [
//            'linkuup' => ['welcome', 'main', 'rooms', 'ownerrooms', 'categorylist', 'addcategory', 'editcategory', 'statistics', 'editroom', 'addroom', 'coworkers', 'addcoworker', 'locationmap'],
//            'account' => ['allsettings', 'settings'],
//            'user' => ['profile'],
//            'friends' => ['friendlist', 'request', 'requests', 'requesting'],
//            'gallery' => ['gallerylist', 'show', 'crud'],
//            'avatar' => ['gallery'],
//            'logs' => ['overview'],
//            'payment' => ['yourorders', 'orders'],
//            'paymentcredits' => ['ordercredits'],
//            'address' => ['ownaddresses', 'add', 'edit'],
//            'login' => ['form'],
//            'register' => ['form', 'tos'],
//            'recovery' => ['form', 'change'],
//            'news' => ['news'],
//            'core' => ['impressum', 'privacypolicy'],
        ];
        return !in_array($name, $routes[$module] ?? [], true);
    }
}
