<?php
namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlaceController extends Controller
{
    /**
     * Display a listing of the places.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Get all places
        $places = Place::all();
        return response()->json($places);
    }


    public function carousel()
{
   
    $places = Place::where('status', 'Approved')->get();

    return response()->json($places);
}

public function pending()
{
   
    $places = Place::where('status', 'Pending')->get();

    return response()->json($places);
}

    /**
     * Store a newly created place in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request, including status
        $request->validate([
            'name' => 'required|string|max:255',
            'place_name' => 'required|string|max:255',
            'address' => 'required|string',
            'email_address' => 'nullable|email',
            'contact_no' => 'nullable|string',
            'description' => 'nullable|string',
            'virtual_iframe' => 'nullable|string',
            'map_iframe' => 'nullable|string',
            'image_link' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            'status' => 'nullable|string',
            'entrance' => 'nullable|string',
            'pricing' => 'nullable|string',
            'activities' => 'nullable|string',
            'history' => 'nullable|string',
        ]);


        $imageLink = null;
        if ($request->hasFile('image_link') && $request->file('image_link')->isValid()) {
    
            $imageLink = $request->file('image_link')->store('places', 'public');
        }

   
        $status = $request->input('status', 'active');


        $place = Place::create(array_merge($request->all(), [
            'image_link' => $imageLink,
            'status' => $status,
        ]));

        return response()->json([
            'message' => 'Place created successfully',
            'place' => $place
        ], 201);
    }

    /**
     * Display the specified place.
     *
     * @param  \App\Models\Place  $place
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Place $place)
    {
        return response()->json($place);
    }

    /**

     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Place  $place
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $place = Place::findOrFail($id);
    
        // Handle image upload if present
        if ($request->hasFile('image_link')) {
            $image = $request->file('image_link');
            $imagePath = $image->store('public/images');
            $place->image_link = str_replace('public/', 'storage/', $imagePath);
        }
    
        // Update other fields
        $place->name = $request->input('name');
        $place->place_name = $request->input('place_name') ?? null;
        $place->address = $request->input('address') ?? null;
        $place->email_address = $request->input('email_address') ?? null;
        $place->contact_no = $request->input('contact_no') ?? null;
        $place->description = $request->input('description') ?? null;
        $place->virtual_iframe = $request->input('virtual_iframe') ?? null;
        $place->map_iframe = $request->input('map_iframe') ?? null;
        $place->status = $request->input('status') ?? 'Pending'; 
        $place->entrance = $request->input('entrance') ?? null;
        $place->history = $request->input('history') ?? null;
        $place->pricing = $request->input('pricing') ?? null;
        $place->activities = $request->input('activities') ?? null;
    
        $place->save();
    
        return response()->json([
            'message' => 'Place updated successfully',
            'data' => $place
        ]);
    }
    

    /**
     * Remove the specified place from the database.
     *
     * @param  \App\Models\Place  $place
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Place $place)
    {

        if ($place->image_link && Storage::disk('public')->exists($place->image_link)) {
            Storage::disk('public')->delete($place->image_link);
        }

  
        $place->delete();

        return response()->json([
            'message' => 'Place deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
{
   
    $request->validate([
        'status' => 'required|string|in:Approved,Pending,Rejected', 
        'remarks' => 'nullable|string',
    ]);

    
    $place = Place::find($id);

 
    if (!$place) {
        return response()->json([
            'message' => 'Place not found'
        ], 404);
    }

    $place->status = $request->input('status');

    if ($request->has('remarks')) {
        $place->remarks = $request->input('remarks');
    } elseif ($request->has('rejection_remarks')) {
        $place->remarks = $request->input('rejection_remarks');
    } elseif ($request->has('remark')) {
        $place->remarks = $request->input('remark');
    } elseif ($request->has('comment')) {
        $place->remarks = $request->input('comment');
    }

    \Log::info('Updating place status and remarks', [
        'id' => $id,
        'status' => $place->status,
        'remarks' => $place->remarks,
        'request_data' => $request->all()
    ]);

    $place->save();

    return response()->json([
        'message' => 'Status updated successfully',
        'place' => $place
    ]);
}
}
