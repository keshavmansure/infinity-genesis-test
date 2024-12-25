<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinkRequest;
use App\Models\Analytics;
use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class LinkController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            $links = Link::withCount('analytics')->with('user')->where('user_id', Auth::id());
            return DataTables::of($links)
                ->addIndexColumn()
                ->addColumn('action', function ($link) {
                    return view('links.actions', compact('link'));
                })
                ->editColumn('short_link', function ($link) {
                    return "<a target='_blank' href='" . route('links.show', $link->short_link) . "'>" . route('links.show', $link->short_link) . "</a>";
                })
                ->editColumn('expire_at', function ($link) {
                    return Carbon::parse($link->expire_at)->diffForHumans();
                })
                ->editColumn('created_at', function ($link) {
                    return Carbon::parse($link->created_at)->diffForHumans();
                })
                ->editColumn('is_active', function ($link) {
                    $status = $link->is_active ? 'Active' : 'Inactive';
                    $class = $link->is_active ? 'success' : 'danger';
                    return "<span class='label label-" . $class . "'>" . $status . "</span>";
                })
                ->rawColumns(['action', 'is_active', 'short_link', 'expire_at', 'created_at'])
                ->make(true);
        }
        return view('links.index');
    }


    public function create()
    {
        return view('links.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LinkRequest $request)
    {
        $data = $request->only('original_url');
        $data['short_link'] = $this->generateRandomString();
        $data['expire_at'] = now()->addHour();
        $data['user_id'] = Auth::id();
        Link::create($data);
        return redirect()->route('links.index')->with('success', 'Link created successfully');
    }

    public function generateRandomString()
    {
        return strtolower(bin2hex(random_bytes(5)));
    }


    public function show($id)
    {
        $link = Link::where('short_link', $id)->first();
        if (Carbon::now()->gt($link->expire_at)) {
            $message = "This link has been expired" . $link->short_link;
            Mail::raw($message, function ($mail) {
                $mail->to(Auth::user()->email)->subject('Link Expired');
            });
            return back()->with('error', "Link Expired");
        }
        if (!$link->is_active) {
            return back()->with('error', "Link is not active");
        }
        Analytics::create([
            'user_id' => Auth::id(),
            'link_id' => $link->id
        ]);
        return redirect($link->original_url);
    }

    public function edit(Link $link)
    {
        if ($link->user_id != Auth::id()) {
            return back()->with('message', "You are unauthorized for this action.");
        }
        return view('links.edit', compact('link'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LinkRequest $request, Link $link)
    {
        if ($link->user_id != Auth::id()) {
            return back()->with('message', "You are unauthorized for this action.");
        }
        $data = $request->only('original_url');
        if ($request->filled('is_active')) {
            $data['is_active'] = $request->is_active == 'true' ? true : false;
        }
        $link->update($data);
        return redirect()->route('links.index')->with('success', 'Link updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link)
    {
        try {
            if ($link->user_id != Auth::id()) {
                return back()->with('message', "You are unauthorized for this action.");
            }
            DB::transaction(function () use ($link) {
                $link->analytics()->delete();
                $link->delete();
            });
            return redirect()->route('links.index')->with('success', 'Link deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', "Failed to delete");
        }
    }
}
