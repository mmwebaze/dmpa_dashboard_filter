<?php
namespace Drupal\dmpa_dashboard_filter\EventSubscriber;

use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DashboardEventSubscriber implements EventSubscriberInterface {


    /**
     * The request stack.
     *
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;
    /**
     * The route match object for the current page.
     *
     * @var \Drupal\Core\Routing\RouteMatchInterface
     */
    protected $routeMatch;
    protected $currentUser;

    public function __construct(RequestStack $requestStack, RouteMatchInterface $routeMatch, AccountInterface $currentUser) {
        $this->requestStack = $requestStack;
        $this->routeMatch = $routeMatch;
        $this->currentUser = $currentUser;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(){
        $events[KernelEvents::RESPONSE][] = ['dashboard'];
        return $events;
    }
    public function dashboard(FilterResponseEvent $event){
        $route_name = $this->routeMatch->getRouteName();

        if (explode('/', $route_name)[0] == 'system.404'){
            return;
        }

        $isAuthenticatedUser = $this->currentUser->isAuthenticated();

        if ($isAuthenticatedUser){
            $voc = ['BURKINA FASO','DRC','GHANA','KENYA','MADAGASCAR','MALAWI','MOZAMBIQUE', 'NIGER','NIGERIA',
                'SENEGAL','UGANDA','ZAMBIA'];
            $request = $this->requestStack->getCurrentRequest();
            $basePath = $request->getPathInfo();

            $countries = array();
            $user = \Drupal\user\Entity\User::load($this->currentUser->id());
            $userCountries = $user->field_user_country->getValue();

            $countryIds = array();
            foreach ($userCountries as $userCountry){
                array_push($countryIds, $userCountry['target_id']);
            }

            $terms = Term::loadMultiple($countryIds);
            foreach ($terms as $term){
                array_push($countries, strtoupper($term->getName()));
            }

            $selectedCountry = strtoupper(explode('/',$basePath)[1]);

            if (in_array($selectedCountry, $voc)){
                if (!in_array($selectedCountry, $countries)){
                    drupal_set_message('You cannot view this dashboard');
                    $response = $event->getResponse();
                    $event->setResponse(new RedirectResponse('http://google.com'));

                }
            }
        }
    }
}