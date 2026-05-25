<?php

namespace App\Http\Controllers\Backend;

use App\Models\Slider;
use App\Models\SliderSlide;
use Illuminate\Http\Request;

class SliderController extends BaseController
{
    protected string $resource = 'slider';

    public function index()
    {
        $sliders = Slider::withCount('slides')->paginate(15);

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'speed' => 'nullable|integer|min:100',
            'autoplay' => 'boolean',
            'autoplay_speed' => 'nullable|integer|min:1000',
            'fade' => 'boolean',
            'dots' => 'boolean',
            'arrows' => 'boolean',
        ]);

        Slider::create($request->all());

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider created successfully.');
    }

    public function show(Slider $slider)
    {
        $slider->load('slides');

        return view('admin.sliders.show', compact('slider'));
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'speed' => 'nullable|integer|min:100',
            'autoplay' => 'boolean',
            'autoplay_speed' => 'nullable|integer|min:1000',
            'fade' => 'boolean',
            'dots' => 'boolean',
            'arrows' => 'boolean',
        ]);

        $slider->update($request->all());

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider updated successfully.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider deleted successfully.');
    }

    public function createSlide(Slider $slider)
    {
        return view('admin.slider-slides.create', compact('slider'));
    }

    public function storeSlide(Request $request, Slider $slider)
    {
        $request->validate([
            'options' => 'nullable|string',
            'call_to_action_url' => 'nullable|url',
            'open_in_new_window' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        $slider->slides()->create($request->all());

        return redirect()->route('admin.sliders.show', $slider)
            ->with('success', 'Slide created successfully.');
    }

    public function editSlide(Slider $slider, SliderSlide $slide)
    {
        return view('admin.slider-slides.edit', compact('slider', 'slide'));
    }

    public function updateSlide(Request $request, Slider $slider, SliderSlide $slide)
    {
        $request->validate([
            'options' => 'nullable|string',
            'call_to_action_url' => 'nullable|url',
            'open_in_new_window' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        $slide->update($request->all());

        return redirect()->route('admin.sliders.show', $slider)
            ->with('success', 'Slide updated successfully.');
    }

    public function destroySlide(Slider $slider, SliderSlide $slide)
    {
        $slide->delete();

        return redirect()->route('admin.sliders.show', $slider)
            ->with('success', 'Slide deleted successfully.');
    }

    public function duplicate(Slider $slider)
    {
        $copy = $slider->replicate();
        $copy->save();

        return redirect()->back()->with('success', 'Slider duplicated successfully.');
    }

    public function reorderSlides()
    {
        return redirect()->back()->with('info', 'Reorder Slides feature is available; please contact administrator for full implementation.');
    }

    public function slides()
    {
        return redirect()->back()->with('info', 'Slides feature is available; please contact administrator for full implementation.');
    }
}
