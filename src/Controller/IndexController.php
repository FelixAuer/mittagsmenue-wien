<?php

namespace App\Controller;

use App\Legacy\ContainerHelper;
use App\Service\IpService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

require_once __DIR__ . '/../../includes/guihelper.php';

class IndexController extends JsonAlertController
{
    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param IpService $ipService
     * @return Response
     */
    public function index(Request $request, IpService $ipService): Response
    {
        // Make service container available for legacy code
        ContainerHelper::getInstance()->setContainer($this->container);

        // TODO

        ob_start();
        // print header
        echo get_header_html();

        // main page with header and tabbed content
        $vote_style = isset($_GET['minimal']) ? 'display: table;' : '';
        $menu_settings = $ipService->isInternIp($request->getClientIp())
            ? "<li><a href='#settings' data-ajax='false' data-icon='gear'>Einstellungen</a></li>" : '';
        echo "
<div id='page_main' data-role='page'>
	<div data-role='header'>
		<h1>
			" . getenv('SITE_TITLE') . "
			<input type='date' id='date' title='' value='" . date_offsetted('Y-m-d') . "'
					data-inline='false' data-ajax='false' data-role='date' style='padding: 0;' />
		</h1>
	</div>
	<div data-role='main' class='ui-content'>
		<div data-role='tabs' id='tabs'>
			<div data-role='navbar'>
				<ul>
					<li><a href='#venues' data-ajax='false' data-icon='home'>Lokale</a></li>
					<li><a href='#alternatives' data-ajax='false' data-icon='cloud'>Alternativen</a></li>
					{$menu_settings}
				</ul>
			</div>
			" . get_button_vote_summary_toggle_html() . "
			<div id='dialog_vote_summary' style='${vote_style}'>" . get_vote_div_html() . "</div>
			<div id='venues' style='padding: 1em 0;'>
				<div id='noVenueFoundNotifier' style='display: none'>
					Es wurde leider nichts gefunden :(<br />Bitte ändern Sie den Ausgangsort und/oder den Umkreis.
				</div>
				<div id='venueContainer'>" . get_venues_html() . "</div>
			</div>
			<div id='alternatives' class='hidden' style='padding: 1em 0;'>
				<div id='setAlternativeVenuesDialog'>" . get_alt_venue_html() . "</div>
			</div>
			<div id='settings' class='hidden' style='padding: 1em 0;'>
				<div id='setVoteSettingsDialog'>" . get_vote_setting_html() . "</div>
			</div>
		</div>
	</div>
	<div data-role='footer'>" . get_footer_html() . "</div>
</div>
</body></html>";

        // other pages used as dialogs
        if (!isset($_GET['minimal'])) {
            // note dialog
            echo get_page_note();
        }

        $response = new Response($contents = ob_get_contents());
        ob_end_clean();
        return $response;
    }

}