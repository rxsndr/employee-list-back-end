<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ProfilePicture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilePictureController extends Controller
{
    private function pictureResponse(ProfilePicture $picture): array
    {
        return [
            'id' => $picture->id,
            'user_email' => $picture->user_email,
            'file_name' => $picture->file_name,
            'file_path' => $picture->file_path,
            'url' => asset('storage/' . $picture->file_path),
        ];
    }

    private function currentUser(Request $request): ?array
    {
        $token = $request->bearerToken();

        if ($token === 'admin-token') {
            return [
                'role' => 'admin',
                'email' => 'admin@email.com',
            ];
        }

        if ($token && str_starts_with($token, 'employee-token-')) {
            $employeeId = (int) str_replace('employee-token-', '', $token);
            $employee = Employee::find($employeeId);

            if ($employee) {
                return [
                    'role' => 'employee',
                    'email' => $employee->email,
                ];
            }
        }

        return null;
    }

    public function index(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        if ($user['role'] !== 'admin') {
            return response()->json([
                'message' => 'You are not allowed to view profile pictures',
            ], 403);
        }

        $pictures = ProfilePicture::latest()
            ->get()
            ->unique('user_email')
            ->values()
            ->map(fn ($picture) => $this->pictureResponse($picture));

        return response()->json([
            'message' => 'Profile pictures retrieved successfully',
            'data' => $pictures,
        ]);
    }

    public function show(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $picture = ProfilePicture::where('user_email', $user['email'])
            ->latest()
            ->first();

        return response()->json([
            'message' => 'Profile picture retrieved successfully',
            'data' => $picture ? $this->pictureResponse($picture) : null,
        ]);
    }

    public function upload(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $file = $request->file('profile_picture');

        $oldPictures = ProfilePicture::where('user_email', $user['email'])->get();

        $path = $file->store('profile-pictures', 'public');

        $picture = ProfilePicture::create([
            'user_email' => $user['email'],
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        foreach ($oldPictures as $oldPicture) {
            if ($oldPicture->file_path) {
                Storage::disk('public')->delete($oldPicture->file_path);
            }

            $oldPicture->delete();
        }

        return response()->json([
            'message' => 'Profile picture uploaded successfully',
            'data' => $this->pictureResponse($picture),
        ], 201);
    }
}
