<?php

namespace App\Http\Controllers;

use Ramsey\Uuid\Uuid;
use App\Models\Comment;
use App\Models\Logbook;
use App\Models\Trainee;
use App\Models\AllTrainee;
use App\Models\Supervisor;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\TraineeAssign;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    public function index()
    {
        $supervisors = Supervisor::all();
        return view('user-management', compact('supervisors'));
    }

    public function showProfileSV()
    {
        $user = Auth::user();
        $supervisor = Supervisor::where('sains_email', $user->email)->first();
        return view('sv-profile', compact('supervisor'));
    }

    public function showAllTraineeProfileForSV() {
        $user = Auth::user();

        //Get the supervisor ID using the email.
        $supervisorID = Supervisor::where('sains_email', $user->email)->pluck('id')->first();

        //search for his or her trainees
        $traineeIDs = TraineeAssign::where('assigned_supervisor_id', $supervisorID)
            ->pluck('trainee_id')
            ->toArray();

        $traineeBasicDatas[] = null;

        //This code retrieves all trainees associated with a given supervisor.
        foreach ($traineeIDs as $traineeID) {
            //get the trainee name
            $traineeName = AllTrainee::where('id', $traineeID)->pluck('name')->first();

            //search the trainee using the name to check whether the trainee is registered or not
            $trainee = Trainee::where('name', $traineeName)->first();
            if($trainee != null){
                $traineeBasicDatas[] = $trainee;
            }
            else{
                // Trainee not found, create a new object with default values
                $defaultTrainee = new Trainee([
                    'name' => $traineeName,
                    'expertise' => 'Not Specified',
                    'phone_number' => '', 
                    'personal_email' => '', 
                    'sains_email' => '', 
                ]);
                $traineeBasicDatas[] = $defaultTrainee;
            }
        }

        return view('sv-trainee-assign', compact('traineeBasicDatas'));

    }

    public function placeholderProfileSV(){
        // Get the currently logged-in user
        $user = Auth::user();
            
        // Check if the user is a supervisor
        if ($user->role_id == 2) {
            $supervisor = Supervisor::where('sains_email', $user->email)->first();

            if (!$supervisor) {
                // Handle the case where the trainee is not found, e.g., show an error message.
                return redirect()->back()->with('error', 'Supervisor not found');
            }

            return view('sv-edit-profile', compact('supervisor'));
        } else {
            // Handle the case where the user is not a trainee (e.g., supervisor or other role)
            return redirect()->back()->with('error', 'User is not a Supervisor');
        }
    }

    public function updateProfileSV(Request $request){
        $validatedData = $request->validate([
            'fullName' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'phoneNum' => ['required', 'string', 'max:255', 'regex:/^[0-9\+]+$/'],
            'section' => 'nullable|string',
            'personalEmail' => ['required', 'email', 'regex:/^(?=.{1,64}@)[A-Za-z0-9_-]+(\\.[A-Za-z0-9_-]+)*@[^-][A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,})$/'],
            'profilePicture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        // Get the currently logged-in user
        $user = Auth::user();
    
        $user->name = $request->input('fullName');
        $user->save();

        Supervisor::where('sains_email', $user->email)
        ->update([
            'name' => $request->input('fullName'),
            'phone_number' => $request->input('phoneNum'),
            'personal_email' => $request->input('personalEmail'),
            'section' => $request->input('section'),
        ]);
    
        return redirect()->route('sv-profile');
    }

    public function goToTraineeProfile($traineeName){

        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();

        $internship_dates = AllTrainee::where('name', 'LIKE', $name)
        ->select('internship_start', 'internship_end')
        ->first();

        $trainee_id = $trainee->id;
        $logbooks = Logbook::where('trainee_id', $trainee_id)->get();

        $supervisor_id = Supervisor::where('sains_email', Auth::user()->email)
            ->pluck('id')
            ->first();

        $comment = Comment::where('supervisor_id', $supervisor_id)
            ->where('trainee_id', $trainee_id)
            ->pluck('comment')
            ->first();

        return view('sv-view-trainee-profile', compact('trainee','internship_dates', 'comment', 'logbooks'));
    }

    public function svViewTraineeLogbook($traineeName)
    {
        $name = urldecode($traineeName);
        $trainee_id = Trainee::where('name','LIKE', $name)->pluck('id')->first();
        $logbooks = Logbook::where('trainee_id', $trainee_id)->get();
        return view('view-and-upload-logbook-sv', compact('logbooks','name'));
    }

    public function svUploadLogbook(Request $request, $name)
    {
        $user = Auth::user();
        $supervisor_name = Supervisor::where('sains_email', $user->email)->pluck('name')->first();

        //get the trainee id
        $trainee_id = Trainee::where('name', 'LIKE', $name)->pluck('id')->first();
        
        // Validate the uploaded file
        $request->validate([
            'logbook' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        // Get the uploaded file
        $file = $request->file('logbook');

        // Get the original filename
        $originalFileName = $file->getClientOriginalName();

        // Store the file in the "public" disk
        $file->storeAs('public/logbooks', $originalFileName);

        // Save the file path in the database for the user
        Logbook::create([
            'trainee_id' => $trainee_id,
            'logbook_path' => 'storage/logbooks/' . $originalFileName,
            'status' => 'Signed',
        ]);
        
        //send the notification about the signed logbook to the trainee.
        $notification = new Notification();
        $notification->id = Uuid::uuid4(); // Generate a UUID for the id
        $notification->type = 'signed_logbook';
        $notification->notifiable_type = 'App\Models\Supervisor';
        $notification->notifiable_id = $trainee_id;
        $notification->data = json_encode([
            'data' => 'Your supervisor ' . $supervisor_name . ' has signed your logbook.',
            'name' => $name,
        ]);
        $notification->save(); // Save the notification to the database

        // Redirect the user to a success page
        return redirect()->route('view-and-upload-logbook-sv', $name)->with('success', 'Logbook uploaded successfully');
    }

    public function destroy(Logbook $logbook, $name)
    {
        // Delete the logbook file from storage
        $logbookPath = storage_path('app/public/logbooks/') . basename($logbook->logbook_path);
        if (file_exists($logbookPath)) {
            unlink($logbookPath);
        }

        // Delete the logbook record from the database
        $logbook->delete();

        return redirect()->route('view-and-upload-logbook-sv', $name)->with('success', 'Logbook deleted successfully');
    }

    public function svViewTraineeResume($traineeName){
        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();
        return view('sv-view-trainee-resume', compact('trainee'));
    }

    public function svCommentPage($traineeName){
        $name = urldecode($traineeName);
        $trainee = Trainee::where('name', $name)->first();

        $user = Auth::user();
        $supervisor_id = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
        $trainee_id = $trainee->id;
        $comment = Comment::where('supervisor_id', $supervisor_id)
            ->where('trainee_id', $trainee_id)
            ->pluck('comment')
            ->first();

        return view('sv-comment', compact('trainee', 'comment'));
    }

    public function svSubmitComment(Request $request){
        $validatedData = $request->validate([
            'trainee_id' => 'required',
            'comment' => 'required',
        ]);

        $user = Auth::user();
        $supervisor_id = Supervisor::where('sains_email', $user->email)->pluck('id')->first();
        $trainee_id = $request->trainee_id;
        $comment = $request->comment;

        $target = Comment::where('trainee_id', $trainee_id)
        ->where('supervisor_id', $supervisor_id)
        ->first();

        //to check whether this supervisor already give this trainee comment or not.
        if($target == null){
            Comment::create([
                'comment' => $comment,
                'trainee_id' => $trainee_id,
                'supervisor_id' => $supervisor_id,
            ]);
        }
        else{
            $target->comment = $comment;
            $target->save();
        }

        $traineeName = Trainee::where('id', $trainee_id)->first()->name;

        return redirect()->route('go-profile', $traineeName)->with('success', 'Comment submitted successfully');
    }

}
