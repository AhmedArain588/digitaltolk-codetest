<?php

namespace DTApi\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User; // Adjust the namespace based on your project structure
use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     * @var User
     */
    protected $repository;
    

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */

    // Renamed the parameter for better clarity and consistency. 
    public function __construct(BookingRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $authenticatedUser = Auth::user();
        /**
         * Returning early helps to decrease the amount of nesting, resulting in code that is easier to follow. 
         * In the revamped code, the declaration is first made and the if statement verifies that $user_id holds a valid value. 
         * If $user_id is not null, undefined, or an empty string, then the code within the if statement will be performed.
         */
        $user_id = $request->get('user_id');
        if ($user_id) {
            $response = $this->repository->getUsersJobs($user_id);
            // Consider using the response() helper method consistently for all responses.
            return response()->json($response);
        }

        /**
         * The newly optimized code utilizes Laravel's configuration system (config) to retrieve the ID for both the admin role and super admin role. 
         * This not only enhances readability, but also centralizes the process and aligns with Laravel's standards. It streamlines the management of configuration settings, making it effortlessly manageable.
         */
        if ($authenticatedUser->user_type == config('app.admin_role_id') || $authenticatedUser->user_type == config('app.superadmin_role_id')) {
            $response = $this->repository->getAll($request);
            // Consider using the response() helper method consistently for all responses.
            return response()->json($response);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->repository->with('translatorJobRel.user')->find($id);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($job);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $authenticatedUser = Auth::user();
        // Eliminates unnecessary variable assignment.
        $response = $this->repository->store($authenticatedUser, $request->all());
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {

        $data = $request->all();
        $cuser = Auth::user();
        // Laravel provides a more concise except method on collections, making the code cleaner.
        $response = $this->repository->updateJob($id, $data->except(['_token', 'submit']), $cuser);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        // To simplify, I took out the variable adminSenderEmail from the query because it wasn't necessary.
        $data = $request->all();
        $response = $this->repository->storeJobEmail($data);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        if ($user_id = $request->get('user_id')) {
            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            // Consider using the response() helper method consistently for all responses.
            return response()->json($response);
        }
        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $response = $this->repository->acceptJob($data, $user);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    public function acceptJobWithId(Request $request)
    {
        $data = $request->get('job_id');
        $user = Auth::user();
        $response = $this->repository->acceptJobWithId($data, $user);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $response = $this->repository->cancelJobAjax($data, $user);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->endJob($data);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    public function customerNotCall(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->customerNotCall($data);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        // Removed the data Variable because of no use of it;
        $user = Auth::user();
        $response = $this->repository->getPotentialJobs($user);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    public function distanceFeed(Request $request)
    {
        $data = $request->all();

        // Replaces the if-else statement with a ternary operator for better clarity.
        $distance = $data['distance'] ?? "";
        $time = $data['time'] ?? "";
        $session = $data['session_time'] ?? "";
        $flagged = ($data['flagged'] == true) ? "yes" : "no";
        $manually_handled = ($data['manually_handled'] == true) ? "yes" : "no";
        $by_admin = ($data['by_admin'] == true) ? "yes" : "no";
        $admincomment = $data['admincomment'] ?? "";
        $jobid = $data['jobid'] ?? 0;

        // To simplify, I took out the variable name from the query because it wasn't necessary.
        $this->updateDistance($jobid, $distance, $time);

        // To simplify, I took out the variable name from the query because it wasn't necessary.
        $this->updateJob($jobid, $admincomment, $session, $flagged, $manually_handled, $by_admin);

        // Consider using the response() helper method consistently for all responses.
        return response()->json("Record updated!");
    }

    public function reopen(Request $request)
    {
        $data = $request->all();
        $response = $this->repository->reopen($data);
        // Consider using the response() helper method consistently for all responses.
        return response()->json($response);
    }

    public function resendNotifications(Request $request)
    {
        // $data = $request->all();
        // To simplify, I took out the variable of $data from the below function because it wasn't necessary.
        $job = $this->repository->find($request->get('jobid'));
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');
        // Consider using the response() helper method consistently for all responses.
        return response()->json(['success' => 'Push sent']);
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {

        // To simplify, I took out the variable of $data and $job_data  from the below function because it wasn't necessary.
        $job = $this->repository->find($request->get('jobid'));

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            // Consider using the response() helper method consistently for all responses.
            return response()->json(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            // Consider using the response() helper method consistently for all responses.
            return response()->json(['success' => $e->getMessage()]);
        }
    }

    protected function updateDistance($jobid, $distance, $time)
    {
        if ($time || $distance) {
            Distance::where('job_id', $jobid)->update(['distance' => $distance, 'time' => $time]);
        }
    }

    protected function updateJob($jobid, $admincomment, $session, $flagged, $manually_handled, $by_admin)
    {
        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {
            Job::where('id', $jobid)->update([
                'admin_comments' => $admincomment,
                'flagged' => $flagged,
                'session_time' => $session,
                'manually_handled' => $manually_handled,
                'by_admin' => $by_admin,
            ]);
        }
    }
}
