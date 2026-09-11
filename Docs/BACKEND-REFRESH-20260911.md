# LinkUUp backend refresh

The Welcome page now uses the app's dark violet/cyan palette, a shared navigation
drawer, consistent public forms and a connected scroll journey. The globe becomes
a location pin; one route links the category preview, the three control symbols
and the final app action. Motion never selects categories or changes settings.

## Integration

Prepared against upstream main `8fe45fc`. Existing upstream location/polygon work
and removal of backend-return links on login/registration are retained. The login
redirect keeps upstream's working URL argument and adds a clearer DE/EN message.
The Bootstrap5Theme initialization fixes are already in its main `cb509c6`;
use that version or newer. No extra theme PR is required.

The configured app URL must be an absolute URL for the target environment.
This change does not hardcode or deploy an environment-specific destination.
New asset revisions invalidate the previous navigation controllers in browser cache.

## Validation

- PHP and JavaScript syntax checks; diff whitespace check.
- Firefox: 390px and 1440px views, no horizontal overflow.
- Navigation opens/closes, Escape restores focus, outside click closes.
- Public login, recovery, contact, privacy, terms and imprint views checked.
- Category selection remains interactive. Scroll motion does not submit forms.
- Globe/route forward and backward scrolling, symbol docking and large jumps checked.
- Reduced motion and short viewports retain static, accessible content.
- Guest access to protected pages still redirects to login with a valid link.

Browser checks were performed on the local implementation before transplanting
onto current upstream; the transplant only required resolving the login-message
conflict. Syntax and diff checks were repeated against the PR branch.

## Review limits

Authenticated pages and a physical iPhone/Safari still need review. Existing Google
Maps configuration/CORS messages remain separate. No FPS or conversion improvement
is claimed. The globe is a stylised illustration, not live location information.
No production deployment, dependency installation, or account changes are included.
