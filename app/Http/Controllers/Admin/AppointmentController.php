<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['user','service'])->paginate(5);
        return view('dashboard.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $users = User::all();
        $services = Service::all();
        return view('dashboard.appointments.create', compact('users','services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time', // Thêm validation cơ bản: thời gian kết thúc phải sau thời gian bắt đầu
            'status' => 'required|in:pending,confirmed,cancelled', // Thêm validation cho giá trị status
            'notes' => 'nullable|string|max:1000', // Thêm validation cho notes
        ]);

        Appointment::create($request->all());

        return redirect()->route('admin.appointments.index')->with('success','Thêm lịch hẹn thành công!');
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $users = User::all();
        $services = Service::all();
        return view('dashboard.appointments.edit', compact('appointment','users','services'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Đã THÊM validation cho user_id và service_id
            'user_id' => 'required|exists:users,user_id', 
            'service_id' => 'required|exists:services,service_id', 
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time', // Thêm validation cơ bản: thời gian kết thúc phải sau thời gian bắt đầu
            'status' => 'required|in:pending,confirmed,cancelled', // Thêm validation cho giá trị status
            'notes' => 'nullable|string|max:1000', // Thêm validation cho notes
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());

        return redirect()->route('admin.appointments.index')->with('success','Cập nhật lịch hẹn thành công!');
    }

    public function destroy($id)
    {
        Appointment::findOrFail($id)->delete();
        return redirect()->route('admin.appointments.index')->with('success','Xóa lịch hẹn thành công!');
    }
}