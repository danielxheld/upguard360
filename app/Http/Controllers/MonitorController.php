<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MonitorController extends Controller
{
    public function index(): View
    {
        $monitors = Monitor::all();

        return view('monitors/monitors', ['monitors' => $monitors]);
    }

    /**
     * Display the application's create monitor form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('monitors/new-monitor');
    }

    /**
     * Save a new monitor to the database.
     *
     * @param Request $request The HTTP request containing form data
     * @return RedirectResponse The redirect response
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'type' => 'required',
            'url_or_ip' => 'required',
            'port' => 'required',
        ], [
            'title.required' => 'Title field is required.',
            'type.required' => 'Type field is required.',
            'url_or_ip.required' => 'URL or IP field is required.',
            'port.required' => 'Port field is required.',
        ]);

        $monitor = Monitor::create($validatedData);
        return redirect()->route('monitors.show', $monitor->id)->with('success', 'Monitor created successfully.');
    }


    public function show($id)
    {
        $monitor = Monitor::find($id);
        return view('monitors/show-monitor', ['monitor' => $monitor]);
    }

    public function delete($id)
    {
        $monitor = Monitor::find($id);
        if (!$monitor) {
            return back()->with('error', 'Monitor not found.');
        }
        $monitor->delete();
        return redirect()->route('monitors.index')->with('success', 'Monitor deleted successfully.');
    }


}