<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Page;
use App\Models\Review;
use App\Models\Service;

class SiteController extends Controller
{
    public function home()
    {
        $certificates = Certificate::query()->orderBy('sort')->get();

        // 2018 founding + the certificates marked "in tijdlijn", oldest first
        $timeline = collect([[2018, 'Vermeer Boomtechniek opgericht']])
            ->merge($certificates->where('show_in_timeline', true)->whereNotNull('year')
                ->map(fn ($c) => [$c->year, $c->name . ($c->abbreviation ? " ({$c->abbreviation})" : '')]))
            ->sortBy(0)
            ->values();

        return view('home', [
            'services' => Service::query()->active()->where('show_on_home', true)->get(),
            'timeline' => $timeline,
            'logos' => $certificates->whereNotNull('logo')->where('show_in_timeline', true),
            'review' => Review::query()->active()->first(),
        ]);
    }

    public function services()
    {
        return view('services.index', ['services' => Service::query()->active()->get()]);
    }

    public function service(Service $service)
    {
        abort_unless($service->is_active, 404);

        return view('services.show', [
            'service' => $service->load('calculatorService'),
            'others' => Service::query()->active()->whereKeyNot($service->id)->get(['title', 'slug']),
        ]);
    }

    public function page(Page $page)
    {
        return view('page', [
            'page' => $page,
            'certificates' => Certificate::query()->orderBy('sort')->get(),
        ]);
    }

    public function calculator()
    {
        return view('calculator.page');
    }
}
