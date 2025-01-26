<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private function getContact(User $user, int $idContact): Contact {
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();

        // Throw error if contact not found
        if(!$contact) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Contact not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $contact;
    }

    private function getAddress(Contact $contact, int $idAddress): Address {
        $address = Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();

        // Throw error if address not found
        if(!$address) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => [
                        'Address not found'
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    public function create(int $idContact, AddressCreateRequest $request): JsonResponse {
        // Get current authenticated user
        $user = Auth::user();

        // Get contact from current user
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated();
        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        $addressResource = new AddressResource($address);

        return $addressResource->response()->setStatusCode(201);
    }

    public function index(int $idContact): JsonResponse {
        // Get current authenticated user
        $user = Auth::user();

        // Get contact from current user
        $contact = $this->getContact($user, $idContact);

        // Get all address from contact
        $address = Address::where('contact_id', $contact->id)->get();

        $addressCollection = AddressResource::collection($address);

        return $addressCollection->response()->setStatusCode(200);
    }

    public function get(int $idContact, int $idAddress): AddressResource {
        // Get current authenticated user
        $user = Auth::user();

        // Get contact from current user
        $contact = $this->getContact($user, $idContact);

        // Get address from current user contact
        $address = $this->getAddress($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource {
        // Get current authenticated user
        $user = Auth::user();

        // Get contact from current user
        $contact = $this->getContact($user, $idContact);

        // Get address from current user contact
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }

    public function destroy(int $idContact, int $idAddress): JsonResponse {
        // Get current authenticated user
        $user = Auth::user();

        // Get contact from current user
        $contact = $this->getContact($user, $idContact);

        // Get address from current user contact
        $address = $this->getAddress($contact, $idAddress);

        $address->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }
}
