<?php

namespace App\Http\Controllers\Backend;

use App\Models\Slider;
use App\Models\SliderSlide;
use Illuminate\Http\Request;

class SliderSlideController extends BaseController
{
    protected string $resource = 'slider_slide';

    protected array $additionalPermissions = ['slider_slide_management_access'];

    public function index()
    {
        $sliderSlides = SliderSlide::with('slider')
            ->orderBy('position', 'asc')
            ->paginate(15);

        return view('admin.slider_slides.index', compact('sliderSlides'));
    }

    public function create()
    {
        $sliders = Slider::all();

        return view('admin.slider_slides.create', compact('sliders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'options' => 'nullable|string',
            'call_to_action_url' => 'nullable|url',
            'open_in_new_window' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        SliderSlide::create($validated);

        return redirect()->route('admin.slider_slides.index')->with('success', 'Slider Slide created successfully.');
    }

    public function show(SliderSlide $sliderSlide)
    {
        $sliderSlide->load('slider');

        return view('admin.slider_slides.show', compact('sliderSlide'));
    }

    public function edit(SliderSlide $sliderSlide)
    {
        $sliders = Slider::all();

        return view('admin.slider_slides.edit', compact('sliderSlide', 'sliders'));
    }

    public function update(Request $request, SliderSlide $sliderSlide)
    {
        $validated = $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'options' => 'nullable|string',
            'call_to_action_url' => 'nullable|url',
            'open_in_new_window' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
        ]);

        $sliderSlide->update($validated);

        return redirect()->route('admin.slider_slides.index')->with('success', 'Slider Slide updated successfully.');
    }

    public function destroy(SliderSlide $sliderSlide)
    {
        $sliderSlide->delete();

        return redirect()->route('admin.slider_slides.index')->with('success', 'Slider Slide deleted successfully.');
    }
}
