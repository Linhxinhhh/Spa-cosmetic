<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Staff;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user)
    {
        $this->syncStaffRole($user);
    }

    public function updated(User $user)
    {
        $this->syncStaffRole($user);
    }

    private function syncStaffRole(User $user)
    {
        // Chỉ sync nếu user có role 'staff'
        if (!$user->hasRole('staff')) {
            $staff = Staff::where('user_id', $user->user_id)->first();
            if ($staff) {
                $staff->update(['status' => false]); // Deactivate thay vì delete
                Log::info("User {$user->user_id} no longer has staff role, deactivated staff record ID {$staff->staff_id}.");
            }
            return;
        }

        // Update hoặc create staff record
        $staff = Staff::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'full_name' => $user->name ?? $user->full_name ?? '',
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'avatar' => $user->avatar ?? null,
                'position' => $user->position ?? 'Nhân viên', // Mặc định nếu user không có position
                'hire_date' => $user->hire_date ?? now()->format('Y-m-d'),
                'status' => true,
            ]
        );

        // Log chi tiết
        $action = Staff::where('user_id', $user->id)->where('staff_id', '!=', $staff->staff_id)->exists() ? 'updated' : 'created';
        Log::info("Staff {$action} for user {$user->user_id}: Staff ID {$staff->staff_id}, Email: {$staff->email}, Status: " . ($staff->status ? 'Active' : 'Inactive'));
    }
}