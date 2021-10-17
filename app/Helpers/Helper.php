<?php
namespace App\Helpers;

use App\Models\AreaModel;
use App\Models\BranchModel;
use App\Models\config_district_model;
use App\Models\config_divison_model;
use App\Models\config_thana_model;
use App\Models\config_union_wards_model;
use App\Models\config_village_blocks_model;
use App\Models\config_working_area_model;
use App\Models\divisional_office_model;
use App\Models\holiday_configuration_model;
use App\Models\loan_product_category_model;
use App\Models\lb_info_model;
use App\Models\member_information_model;
use App\Models\rescheDule_recordmode;
use App\Models\samity_entry;
use App\Models\saving_product_model;
use App\Models\zone_model;
use App\Models\trench_model;
use App\Models\death_member_register_model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\DB;

trait Helper
{
    public $permission = [];

    public $perPage = 20;

    public function __construct()
    {
        if (request()->input('per_page')){
            $this->perPage = request()->input('per_page');
        }
    }

    public function returnData($status_code = null, $result = null, $message = null)
    {
        $data = [];
        if ($status_code) {
            $data['status'] = $status_code;
        }
        if ($result) {
            $data['result'] = $result;
        }
        if ($message) {
            $data['message'] = $message;
        }
        $data['date'] = Session::get('0');

        return $data;
    }

}
