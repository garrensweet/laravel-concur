<?php

namespace VdPoel\Concur\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        return $this->combineKeysAndValues(config(sprintf('concur.form_params.%s', $type)));
    }

    /**
     * @param array $map
     * @return array
     */
    protected function combineKeysAndValues(array $map): array
    {
        return with(array_values(optional(request()->user())->only(array_values($map)) ?? []), function (array $values) use ($map) {
            return array_combine(blank($values) ? [] : array_keys($map), $values);
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
