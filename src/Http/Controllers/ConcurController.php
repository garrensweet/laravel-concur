<?php

namespace VdPoel\Concur\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VdPoel\Concur\Api\Factory;

/**
 * Class ConcurController
 *
 * @package VdPoel\Concur\Http\Controllers
 */
class ConcurController extends Controller
{
    /**
     * @var Factory
     */
    protected $concur;

    /**
     * ConcurController constructor.
     */
    public function __construct()
    {
        $this->concur = app()->make('concur.api.factory');

        $this->middleware(function (Request $request, \Closure $next) {
            abort_unless(auth()->check(), 401, 'Unauthorized.');

            return $next($request);
        });
    }

    /**
     * @return JsonResponse|RedirectResponse
     * @throws GuzzleException
     */
    public function lookupTravelProfile()
    {
        if ($this->concur->travelProfile->get(['userid_value' => request()->user()->getAttribute('email')])) {
            return $this->respond(route('concur.signin'));
        }

        return $this->respond(route('concur.travel.profile.create'));
    }

    /**
     * @return JsonResponse|RedirectResponse
     * @throws GuzzleException
     */
    public function createTravelProfile()
    {
        abort_unless($this->concur->travelProfile->create($this->mapFormParams('travel.profile')), 400, 'Unable to create travel profile.');

        return $this->respond(route('concur.signin'));
    }

    /**
     * @return JsonResponse|RedirectResponse
     */
    public function signin()
    {
        return $this->respond(config('concur.api.urls.signin'));
    }

    /**
     * @param string $type
     * @return array
     */
    protected function mapFormParams(string $type): array
    {
        return with(config(sprintf('concur.form_params.%s', $type)), function (array $map) {
            return array_combine(array_keys($map), array_values(request()->user()->only(array_values($map))));
        });
    }

    /**
     * @param string $url
     * @return JsonResponse|RedirectResponse
     */
    protected function respond(string $url)
    {
        return request()->ajax() ? response()->json(['redirect' => $url], 302) : redirect()->to($url);
    }
}
