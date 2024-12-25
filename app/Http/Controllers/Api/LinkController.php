<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateLinkRequest;
use App\Http\Requests\LinkRequest;
use App\Http\Resources\LinkListCollection;
use App\Http\Resources\LinkResource;
use App\Models\Analytics;
use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = Link::withCount('analytics')->with('user')->where('user_id', Auth::id())->get();
        return response()->json(new LinkListCollection($links));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function store(LinkRequest $request)
    {
        $data = $request->only('original_url');
        $data['short_link'] = $this->generateRandomString();
        $data['expire_at'] = now()->addHour();
        $data['user_id'] = Auth::id();
        $link = Link::create($data);
        return response()->json(new LinkResource($link), 200);
    }

    public function generateRandomString()
    {
        return strtolower(bin2hex(random_bytes(5)));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $link = Link::where('short_link', $id)->first();
            if (Carbon::now()->gt($link->expire_at)) {
                $message = "This link has been expired" . $link->short_link;
                Mail::raw($message, function ($mail) {
                    $mail->to(Auth::user()->email)->subject('Link Expired');
                });
                return response()->json(['error' => "Link Expired"], 403);
            }
            if (!$link->is_active) {
                return response()->json(['error' => "Link is not active"], 403);
            }
            Analytics::create([
                'user_id' => Auth::id(),
                'link_id' => $link->id
            ]);
            return response()->json(new LinkResource($link));
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 403);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLinkRequest $request, Link $link)
    {
        if ($link->user_id != Auth::id()) {
            return response()->json(['message' => "You are unauthorized for this action."], 401);
        }
        $data = $request->only('original_url');
        if ($request->filled('is_active')) {
            $data['is_active'] = $request->is_active == 'true' ? true : false;
        }
        $link = tap($link)->update($data);
        return response()->json(new LinkResource($link));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Link $link)
    {
        try {
            if ($link->user_id != Auth::id()) {
                return response()->json(['message' => "You are unauthorized for this action."], 401);
            }
            DB::transaction(function () use ($link) {
                $link->analytics()->delete();
                $link->delete();
            });
            return response()->json(['success' => 'Link deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 403);
        }
    }
}
