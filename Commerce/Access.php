<?php
declare(strict_types=1);
namespace GDO\LinkUUp\Commerce;

/** Request policy. Identity and ownership must come from the server session/DB. */
final class Access
{
    public static function check(bool $activeMember, bool $owner, bool $inactiveRoom, string $action, string $method, string $origin, string $token, string $supplied): void
    {
        if (!$activeMember) throw new \RuntimeException('Anmeldung erforderlich', 403);
        $actions = ['state','save-menu','save-tables','publication','transition','topup-test','place','message'];
        if (!in_array($action, $actions, true)) throw new \RuntimeException('Aktion nicht verfügbar', 400);
        if (!in_array($method, ['GET','POST'], true)) throw new \RuntimeException('Methode nicht erlaubt', 405);
        if ($action !== 'state' && ($method !== 'POST' || $origin !== 'http://app.localhost' || strlen($token) !== 64 || !hash_equals($token, $supplied))) {
            throw new \RuntimeException('Sitzung abgelaufen. Bitte neu öffnen.', 403);
        }
        if (in_array($action, ['save-menu','save-tables','publication','transition'], true) && !$owner) {
            throw new \RuntimeException('Keine Berechtigung für diese Location', 403);
        }
        // Existing orders remain readable and settleable by their authorized parties.
        if ($inactiveRoom && in_array($action, ['save-menu','save-tables','publication','place','topup-test'], true)) {
            throw new \RuntimeException('Location ist nicht verfügbar', 409);
        }
    }
}
