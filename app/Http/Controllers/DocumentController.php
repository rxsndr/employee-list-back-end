<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
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

    private function canAccessDocument(array $user, Document $document): bool
    {
        if ($user['role'] === 'admin') {
            return true;
        }

        return $document->owner_email === $user['email'];
    }

    public function index(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $query = Document::latest();

        if ($user['role'] !== 'admin') {
            $query->where('owner_email', $user['email']);
        }

        return response()->json([
            'message' => 'Documents retrieved successfully',
            'data' => $query->get(),
        ]);
    }

    public function addDocument(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'status' => 'required|in:completed,in progress,pending',
            'notes' => 'nullable|string',
            'deadline_date' => 'required|date',
            'given_by' => 'required|string|max:255',
            'signature' => 'nullable|string',
        ]);

        $validated['owner_email'] = $user['email'];

        $document = Document::create($validated);

        return response()->json([
            'message' => 'Document created successfully',
            'data' => $document,
        ], 201);
    }

    public function updateDocument(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $document = Document::find($request->id);

        if (!$document) {
            return response()->json([
                'message' => 'Document not found',
            ], 404);
        }

        if (!$this->canAccessDocument($user, $document)) {
            return response()->json([
                'message' => 'You are not allowed to update this document',
            ], 403);
        }

        $validated = $request->validate([
            'task_name' => 'required|string|max:255',
            'status' => 'required|in:completed,in progress,pending',
            'notes' => 'nullable|string',
            'deadline_date' => 'required|date',
            'given_by' => 'required|string|max:255',
            'signature' => 'nullable|string',
        ]);

        $document->update($validated);

        return response()->json([
            'message' => 'Document updated successfully',
            'data' => $document,
        ]);
    }

    public function deleteDocument(Request $request)
    {
        $user = $this->currentUser($request);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $document = Document::find($request->id);

        if (!$document) {
            return response()->json([
                'message' => 'Document not found',
            ], 404);
        }

        if (!$this->canAccessDocument($user, $document)) {
            return response()->json([
                'message' => 'You are not allowed to delete this document',
            ], 403);
        }

        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }
}
