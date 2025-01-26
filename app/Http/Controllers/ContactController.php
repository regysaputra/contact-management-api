<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function create(ContactCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        $contact = new Contact($data);
        $contact->user_id = $user->id;
        $contact->save();

        $contactResource = new ContactResource($contact);

        return $contactResource->response()->setStatusCode(201);
    }

    public function get(int $id): ContactResource
    {
        $user = Auth::user();
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();

        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return new ContactResource($contact);
    }

    public function update(int $id, ContactUpdateRequest $request): ContactResource
    {
        // Get current authenticated user
        $user = Auth::user();

        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated();
        $contact->fill($data);
        $contact->save();

        return new ContactResource($contact);
    }

    public function destroy(int $id)
    {
        // Get current authenticated user
        $user = Auth::user();

        // Check if specific contact exist within specific user
        $contact = Contact::where('id', $id)->where('user_id', $user->id)->first();
        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        $contact->delete();
        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request): ContactCollection {
        // Get current authenticated user
        $user = Auth::user();

        // Get number of page
        $page = $request->input('page', 1);

        // Get size of data
        $size = $request->input('size', 10);

        // Get contacts from current authenticated user
        $contacts = Contact::query()->where('user_id', $user->id);

        // If query string is empty, return all contact
        if (request()->filled('name') || request()->filled('email') || request()->filled('phone')) {
            return new ContactCollection($contacts->paginate(perPage: $size, page: $page));
        }

        $contacts = $contacts->where(function (Builder $builder) use($request) {
            // Search by name
            $name = $request->input('name');
            if($name) {
                $builder->where(function (Builder $builder) use ($name){
                    $builder->orWhere('first_name', 'ilike' , '%'. $name .'%');
                    $builder->orWhere('last_name', 'ilike' , '%'. $name .'%');
                });
            }

            // Search by email
            $email = $request->input('email');
            if($email) {
                $builder->orWhere('email', 'like' , '%'. $email. '%');
            }

            // Search by phone
            $phone = $request->input('phone');
            if($phone) {
                $builder->orWhere('phone', 'like' , '%'. $phone. '%');
            }
        });

        $contacts = $contacts->paginate(perPage: $size, page: $page);

        return new ContactCollection($contacts);
    }
}
