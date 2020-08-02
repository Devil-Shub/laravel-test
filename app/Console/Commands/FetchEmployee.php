<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domains\Auth\Models\User;
use App\Employee;

class FetchEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch-employee';

    protected $apiUrl = 'http://dummy.restapiexample.com/api/v1/employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will use to fetch employee details';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $response = Http::get($this->apiUrl);

            $employeeDetails = json_decode($response->body())->data;

            //start transactions
            DB::beginTransaction();

            foreach ($employeeDetails as $key => $value) {
                $addUser = new User;

                $addUser->type = User::TYPE_USER;
                $addUser->name = $value->employee_name;
                $addUser->age = $value->employee_age;
                $addUser->password = 'secret';
                $addUser->email_verified_at = now();
                $addUser->active = true;

                if ($addUser->save()) {
                    Employee::create([
                        'user_id' => $addUser->id,
                        'salary' => $value->employee_salary,
                    ]);
                }
                //commit transactions
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            //make log of errors
            Log::error(json_encode($e->getMessage()));
        }
    }
}
