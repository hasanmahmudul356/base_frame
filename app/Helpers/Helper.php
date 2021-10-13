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

    public function __construct()
    {
//        $this->permission = new Entrust(app());
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

    public function valueFromCollection($collection, $column, $value, $return)
    {
        $loan_information = collect($collection)->where($column, $value)->first();
        if ($loan_information) {
            return $loan_information->{$return};
        } else {
            return 0;
        }
    }

    public function removeFirstBracket($string)
    {
        $string = str_replace('(', '-', $string);
        $string = str_replace(')', '-', $string);
        return $string;
    }

    public function current_member_id_genertor($samity_name)
    {
        $max = member_information_model:: where('m_branch_name', Auth::user()->branch_name)->where('m_samity_name', $samity_name)->orderByRaw('m_member_code DESC')->first();
        $max_id = '';
        if ($max) {
            $max1 = substr((string)$max->m_member_code, 9);
            $maxInt = intval($max1) + 1;
            $max_len = strlen($maxInt);
            $sub = 4 - $max_len;
            $max_id = str_repeat('0', $sub) . $maxInt;
        } else {
            $max = member_information_model::where('m_branch_name', Auth::user()->branch_name)->where('m_samity_name', $samity_name)->count() + 1;
            // $max=member_information_model::where('m_branch_name',Auth::user()->branch_name)->where('m_samity_name',$samity_name)->max('member_id')+1;
            $max_len = strlen($max);
            $sub = 4 - $max_len;
            $max_id = str_repeat('0', $sub) . $max;
        }
        return $max_id;
    }

    public function getAuthInformationForHeader($user_permisson)
    {
        $name = '';
        if (in_array('Acess_Branch', $user_permisson)) {
            $name .= ' Branch: ';
            $branch_details = BranchModel::where('branch_id', Auth::user()->branch_name)->first();
            $name .= $branch_details->branch_name;
        }


        if (in_array('Access_Head_Office', $user_permisson)) {
            $name .= ' Headoffice';
        }


        if (in_array('Access_area_Office', $user_permisson)) {
            $name .= ' Area: ';
            $area_details = AreaModel::where('area_id', Auth::user()->area_office_id)->first();
            $name .= $area_details->area_name;
        }


        if (in_array('Access_zone_Office', $user_permisson)) {
            $name .= ' Zone: ';
            $area_details = zone_model::where('id', Auth::user()->zone_office_id)->first();
            $name .= $area_details->zone_name;
        }


        if (in_array('Access_director_Office', $user_permisson)) {
            $name .= ' Director: ';
        }


        if (in_array('Access_divison_Office', $user_permisson)) {
            $name .= ' Divisonal Office :';
            $area_details = divisional_office_model::where('id', Auth::user()->divisional_office_id)->first();
            $name .= $area_details->dv_office_name;
        }

        $name .= ' |  ';

        $name .= date('l, d-m-Y', strtotime(Session::get('0')));
        $name .= ' | Level: ';

        if (in_array('Acess_Branch', $user_permisson)) {
            $name .= ' Branch';
        }
        if (in_array('Access_Head_Office', $user_permisson)) {
            $name .= ' Headoffice';
        }
        if (in_array('Access_area_Office', $user_permisson)) {
            $name .= ' Area';
        }
        if (in_array('Access_zone_Office', $user_permisson)) {
            $name .= ' Zone';
        }
        if (in_array('Access_director_Office', $user_permisson)) {
            $name .= ' Director';
        }
        if (in_array('Access_divison_Office', $user_permisson)) {
            $name .= ' Divisonal';
        }

        return $name;
    }

    public function getRequiredData($input)
    {
        try {
            $data = [];
            if (in_array('samity', $input)) {
                $data['samity'] = samity_entry::where('Branch_name', Auth::user()->branch_name)->get();
            }
            if (in_array('division', $input)) {
                $data['division'] = config_divison_model::all();
            }
            if (in_array('branch_details', $input)) {
                $data['branch_details'] = BranchModel::where('branch_id', Auth::user()->branch_name)->first();
            }
            if (in_array('admission_no', $input)) {
                $data['admission_no'] = member_information_model::where('m_branch_name', Auth::user()->branch_name)->pluck('m_admission_no')->first();
            }
            if (in_array('saving_product', $input)) {
                $data['saving_product'] = saving_product_model::where('group', 'GS')->get();
            }
            if (in_array('loan_product_category', $input)) {
                $data['loan_product_category'] = loan_product_category_model::where('is_primary', '1')->where('lpc_rule', '!=', 'Break')->get();
            }
            if (in_array('form_application_no', $input)) {
                $data['form_application_no'] = member_information_model::where('m_branch_name', Auth::user()->branch_name)->pluck('m_form_application_no')->first();
            }
            if (in_array('admission_date', $input)) {
                $data['admission_date'] = Session::get('0');
            }
            if (in_array('loan_primary_product', $input)) {
                $data['loan_primary_product'] = loan_product_category_model::where('is_primary', '1')->where('lpc_rule', '!=', 'Break')->get();
            }
            if (in_array('branch', $input)) {
                $data['branch'] = BranchModel::whereIn('branch_id', Auth::check_per())->get();
            }


            if (isset($input['district'])) {
                $data['district'] = config_district_model::where($input['district'])->get();
            }
            if (isset($input['thana'])) {
                $data['thana'] = config_thana_model::where($input['thana'])->get();
            }
            if (isset($input['union'])) {
                $data['union'] = config_union_wards_model::where($input['union'])->get();
            }
            if (isset($input['village'])) {
                $data['village'] = config_village_blocks_model::where($input['village'])->get();
            }
            if (isset($input['working_area'])) {
                $data['working_area'] = config_working_area_model::where($input['working_area'])->get();
            }
            if (isset($input['member_code'])) {
                $samity_name = isset($input['member_code']['samity']) ? $input['member_code']['samity'] : '';
                $data['member_code'] = $samity_name . '-' . $this->current_member_id_genertor($samity_name);
            }
            return $data;
        } catch (\Exception $exception) {
            return response()->json($this->returnData(5000, $exception->getMessage(), 'Something Wrong'), 200);
        }

    }

    public function weakData($date, $return)
    {
        $start_week = strtotime("last saturday midnight", strtotime($date));
        $end_week = strtotime("next friday", $start_week);
        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);
        if ($return == 'first_day') {
            return $start_week;
        }
        if ($return == 'last_day') {
            return $end_week;
        }
    }

    public function fractionMessage($amount)
    {
        return 'Fraction amount ' . $amount . ' not allow';
    }

    public function frequencyText($frequencyNumber)
    {
        if ($frequencyNumber = 'days') {
            return 'Daily';
        }
        if ($frequencyNumber = 'weeks') {
            return 'Weakly';
        }
        if ($frequencyNumber = 'months') {
            return 'Monthly';
        }
        if ($frequencyNumber = 'years') {
            return 'Yearly';
        }
    }

    //LOAN FUNCTION
    private function txSchedule($daily_interest_rate, $loan_details, $tx, &$last_date, &$total_recover_pr, &$qum_total_recover, &$pr_due, &$sr_due, $is_interest_only, &$lastThisTxSchdl, $is_lastSchdl, $is_disableRecoverable = false)
    {
        $diff_day = date_diff(date_create($last_date), date_create($tx->rlt_transcation_date));
        $usage_day = $diff_day->format("%a");

        $principal_amount = $loan_details->li_amount - $total_recover_pr;
        $after_principal = $principal_amount - $tx->pr_amount;

        $principal_amount = ($principal_amount < 0) ? 0 : $principal_amount;
        $after_principal = ($after_principal < 0) ? 0 : $after_principal;

        if (round($principal_amount) > 0) {
            if (!empty($lastThisTxSchdl)) {
                $installment_amount = $lastThisTxSchdl->prin_installment_amount;
            } else {
                $installment_amount = (!$is_disableRecoverable) ? $loan_details->li_installment_amount : 0;
            }
            if (!$is_disableRecoverable) {
                $int_installment_amount = $principal_amount * $usage_day * $daily_interest_rate;
                //$int_installment_amount = (!$is_interest_only && !$is_lastSchdl && $int_installment_amount > $installment_amount) ? $installment_amount : $int_installment_amount;
            } else {
                $int_installment_amount = 0;
            }
            if ($is_lastSchdl) {
                $prin_installment_amount = empty($lastThisTxSchdl) ? $principal_amount : $installment_amount - $int_installment_amount;
            } else {
                $prin_installment_amount = $installment_amount - $int_installment_amount;
                if ($is_interest_only) {
                    $prin_installment_amount = 0;
                }
                //if(round($after_principal)<=0) { $prin_installment_amount = $principal_amount; }
                if ((empty($lastThisTxSchdl) && $principal_amount < $prin_installment_amount)) {
                    $prin_installment_amount = $principal_amount;
                }
            }

            //Migration---
            if (isset($tx->mig_int_installment_amount)) {
                $int_installment_amount = $tx->mig_int_installment_amount;
            }
            if (isset($tx->mig_prin_installment_amount)) {
                $prin_installment_amount = $tx->mig_prin_installment_amount;
            }
            //--
        } else {
            $int_installment_amount = 0;
            $prin_installment_amount = 0;
        }

        $total_recover_pr += $tx->pr_amount;
        $qum_total_recover += $tx->rlt_amount;

        if ($is_disableRecoverable) {
            $pr_due = 0;
            $sr_due = 0;
        } else {
            if (round($after_principal) > 0) {
                STATIC $schdl_recover_pr;
                STATIC $schdl_pre_pr_due;
                $schdl_recover_pr = empty($lastThisTxSchdl) ? $tx->pr_amount : $schdl_recover_pr + $tx->pr_amount;
                $schdl_pre_pr_due = empty($lastThisTxSchdl) ? $pr_due : $schdl_pre_pr_due;
                $pr_due = $schdl_pre_pr_due + $prin_installment_amount - $schdl_recover_pr;
            } else {
                $pr_due = 0;
            }
            $sr_due += $int_installment_amount - $tx->sr_amount;
            $sr_due = ($sr_due < 0) ? 0 : $sr_due;
            $pr_due = ($pr_due < 0) ? 0 : $pr_due;
        }

        $last_date = $tx->rlt_transcation_date;
        if (!empty($lastThisTxSchdl)) {
            $lastThisTxSchdl->prin_installment_amount = 0;
        }

        $output = (object)[
            'date' => $tx->rlt_transcation_date,
            'usage_day' => $usage_day,
            'principal_amount' => $principal_amount,
            'int_installment_amount' => $int_installment_amount,
            'prin_installment_amount' => $prin_installment_amount,
            'after_principal' => $after_principal,
            "pr_outstanding" => 0,
            "sr_outstanding" => 0,
            "total_outstanding" => 0,
            "total_repay" => 0,
            'pr_due' => $pr_due,
            'sr_due' => $sr_due,
            "qum_total_recover" => $qum_total_recover,
            "total_recover" => $tx->rlt_amount,
            "pr_recover" => $tx->pr_amount,
            "sr_recover" => $tx->sr_amount
        ];

        return $output;
    }

    public function transactionSchedule($loans, $transactions, $date, $is_transaction = false, $txId_not = 0, $func1 = false, $funcArg1 = [], $func2 = false, $funcArg2 = [])
    {
        $transactions = collect($transactions)->groupBy('rlt_loan_id')->all();
        $funcDate = $date;

        $schedule_builder = app()->make(\App\Models\schedule_builder::class);
        $helper = (new self);
        //Holiday Config(Start)
        $loan_general_configuration = DB::table('loan_general_configuration')->get()->toArray();
        $hlCnf['loan_general_configuration'] = json_decode(json_encode(collect($loan_general_configuration)->toArray()), true);
        $holiday_configuration = holiday_configuration_model::all();
        $hlCnf['holiday_details'] = collect($holiday_configuration)->toArray();
        $hlCnf['holiday_configuration'] = app()->make(\App\Http\Controllers\holiday_configuration::class);
        //Holiday Config(End)

        $loan_codes = collect($loans)->pluck('li_code')->toArray();
        $reschedules = rescheDule_recordmode::whereIn('loancode', $loan_codes)->get()->toArray();


        $deathMembers = death_member_register_model::get()->keyBy('dmr_member_name')->all();

        $loansutput = [];
        foreach ($loans as $loan_details) {
            $loan_details = (object)$loan_details->toArray();
            $txScheduleArray = [];
            $is_interest_only = ($loan_details->loan_type == 3);

            $loan_tx_func = isset($transactions[$loan_details->li_code]) ? $transactions[$loan_details->li_code]->all() : [];
            if ($txId_not > 0) {
                $loan_tx = collect($loan_tx_func)->whereStrict('trench_status', null)->where('rlt_id', '!=', $txId_not)->all();
            } else {
                $loan_tx = collect($loan_tx_func)->whereStrict('trench_status', null)->all();
            }
            $cntLoanTx = count($loan_tx);

            //DeathMember Chk---
            $date = $funcDate;
            $is_death = false;
            $is_afterDeathTx = false;
            $member_death_date = '';
            if (array_key_exists($loan_details->li_member_name, $deathMembers)) {
                $deathMember = $deathMembers[$loan_details->li_member_name];
                if (strtotime($deathMember->dmr_death_date) <= strtotime($funcDate)) {
                    $date = date('Y-m-d', strtotime($deathMember->dmr_death_date . ' - 1 day'));
                    $is_death = true;
                    $member_death_date = $deathMember->dmr_death_date;

                    if ($cntLoanTx >= 1 && strtotime($loan_tx[($cntLoanTx - 1)]->rlt_transcation_date) > strtotime($date)) {
                        $is_afterDeathTx = true;
                        //$date = $loan_tx[($cntLoanTx-1)]->rlt_transcation_date;
                        $date = $funcDate;
                    }
                }
            }
            $loan_details->member_death_date = $member_death_date;
            //---

            $total_recover_pr = 0;
            $qum_total_recover = 0;
            $pr_due = 0;
            $sr_due = 0;
            $principal_balance = $loan_details->li_amount;
            $full_schedule_interest = $loan_details->li_interest_amount;
            $schedule_disbursment_date = ($loan_details->loan_type == 2 && $loan_details->number_of_trench == $loan_details->trench_status) ? $loan_details->li_final_trench_release_date : $loan_details->li_disbursment_date;
            $last_date = $schedule_disbursment_date;

            //Migration---
            $migration_schedule_date = $loan_details->migration_schedule_date;
            $is_migrate = !is_null($migration_schedule_date);
            //--
            // $hlCnf=[];
            $scheduleDates = $schedule_builder->scheduleDates($schedule_disbursment_date, $loan_details->li_payment_frequency, $loan_details->li_no_of_repayment, $loan_details->li_first_repay_period, $funcDate, $hlCnf, $loan_details->li_code, $reschedules);
             //dd(json_decode($loan_details->loan_schedules['schedules']));
            // $full_schedule = json_decode($loan_details->loan_schedules['schedules']);
            // dd($full_schedule);schedules
            // $loan_details->loan_schedule = json_decode($loan_details->loan_schedules['schedules']);

            $full_schedule = $scheduleDates[0];

            $loan_details->loan_schedule = $scheduleDates[1];
            $loan_details->installment_date_passed = $scheduleDates[2];
            $loan_details->thisMonthschedule = $scheduleDates[3];
            $loan_details->opening_installment = $scheduleDates[4];
            $loan_details->previous_month_installment = $scheduleDates[5];

            $schedule_information = collect($full_schedule);
            $regular_schedule = $schedule_information->where('date', '<=', $date)->all();
            $future_schedule = $schedule_information->where('date', '>', $date)->all();

            $full_schedule_cnt = count($full_schedule);
            $last_schedule_date = ($full_schedule_cnt > 0) ? $full_schedule[($full_schedule_cnt - 1)]->date : '';
            $loan_details->last_schedule_date = $last_schedule_date;

            $yearly_interest_rate = $schedule_builder->yearlyInterestRate($loan_details->li_interest_rate, $loan_details->li_interest_rate_period);

            if ($loan_details->li_interest_calculation_method == 'flat') { //Flat
                if ($is_interest_only) {//interest only
                    $interest_rate = $schedule_builder->convertToYear($yearly_interest_rate, $loan_details->li_repay_period_frequency) / 100;
                } else {
                    $duration = $schedule_builder->convertToYear($loan_details->li_repay_period, $loan_details->li_repay_period_frequency);
                    $interest_rate = ($yearly_interest_rate * $duration) / 100;
                    $interest_rate_index = $interest_rate + 1;
                }
                $loan_details->calculated_interest_rate = $interest_rate;

                $sr_outstanding = $loan_details->li_interest_amount;
                $ttl_pr_recoverable = 0;
                $ttl_sr_recoverable = 0;
                $ttl_pr_recover = 0;
                $ttl_sr_recover = 0;
                $is_DeathNullSet = false;
                //$date_recovered=false;

                //Installment---
                if (!$is_interest_only) {
                    $per_installment_amount = $loan_details->li_installment_amount;
                    $per_installment_principal = $per_installment_amount / $interest_rate_index;
                    $per_installment_interest = $per_installment_amount - $per_installment_principal;

                    $last_installment_principal = $loan_details->li_amount - ($per_installment_principal * ($full_schedule_cnt - 1));
                    $last_installment_interest = $last_installment_principal * (($yearly_interest_rate * $duration) / 100);
                }
                //---

                foreach ($full_schedule as $s => $scdl) {
                    $pr_recover = 0;
                    $sr_recover = 0;
                    $transaction_recovered = [];
                    $curScdlLastdate = self::curScdlLastdate($scdl->date, $loan_details->li_payment_frequency, $full_schedule, $s);

                    foreach ($loan_tx as $i => $tx) {
                        if (($s == ($full_schedule_cnt - 1)) || (strtotime($tx->rlt_transcation_date) <= strtotime($curScdlLastdate))) {
                            //Death--
                            if ($is_death && !$is_DeathNullSet && strtotime($tx->rlt_transcation_date) >= strtotime($member_death_date)) {
                                if ($ttl_pr_recoverable > ($ttl_pr_recover + $pr_recover)) {
                                    $ttl_pr_recoverable = $ttl_pr_recover + $pr_recover;
                                }
                                if ($ttl_sr_recoverable > ($ttl_sr_recover + $sr_recover)) {
                                    $ttl_sr_recoverable = $ttl_sr_recover + $sr_recover;
                                }
                                $is_DeathNullSet = true;
                            }
                            //--

                            $pr_recover += $tx->pr_amount;
                            $sr_recover += $tx->sr_amount;

                            $transaction_recovered[] = [
                                'date' => $tx->rlt_transcation_date,
                                'pr_amount' => $tx->pr_amount,
                                'sr_amount' => $tx->sr_amount,
                                'rlt_amount' => $tx->rlt_amount
                            ];
                            unset($loan_tx[$i]);
                        } else {
                            break;
                        }
                    }

                    if ($principal_balance > 0 && (!$is_death || strtotime($scdl->date) < strtotime($member_death_date))) {
                        if ($is_interest_only) {//interest only
                            if ($is_migrate) {
                                //Migration---
                                if ($pr_recover == 0 && $sr_recover == 0) {
                                    $int_installment_amount = 0;
                                } else {
                                    $int_installment_amount = $sr_recover + $loan_details->migration_service_charge_due;
                                    $is_migrate = false;
                                }
                                //--
                            } else {
                                $int_installment_amount = $principal_balance * $interest_rate;
                            }
                            $scdl_prin_installment_amount = ($s == ($full_schedule_cnt - 1)) ? $loan_details->li_amount : 0;
                            $prin_installment_amount = ($principal_balance >= $scdl_prin_installment_amount) ? $scdl_prin_installment_amount : $principal_balance;
                        } else {
                            $int_installment_amount = ($s == ($full_schedule_cnt - 1)) ? $last_installment_interest : $per_installment_interest;
                            $prin_installment_amount = ($s == ($full_schedule_cnt - 1)) ? $last_installment_principal : $per_installment_principal;
                        }
                    } else {
                        $int_installment_amount = 0;
                        $prin_installment_amount = 0;
                    }
                    $before_principal_balance = $principal_balance;

                    //Due Null for death--
                    if ($is_death && !$is_DeathNullSet && strtotime($scdl->date) >= strtotime($member_death_date)) {
                        if ($ttl_pr_recoverable > $ttl_pr_recover) {
                            $ttl_pr_recoverable = $ttl_pr_recover;
                        }
                        if ($ttl_sr_recoverable > $ttl_sr_recover) {
                            $ttl_sr_recoverable = $ttl_sr_recover;
                        }
                        $is_DeathNullSet = true;
                    }
                    //--

                    $temp_ttl_pr_recoverable = $ttl_pr_recoverable;
                    $temp_ttl_sr_recoverable = $ttl_sr_recoverable;

                    if (!$is_DeathNullSet) {
                        $ttl_pr_recoverable += $prin_installment_amount;
                        $ttl_sr_recoverable += $int_installment_amount;

                        if ($is_interest_only && $full_schedule_cnt == ($s + 1)) {
                            $ttl_pr_recoverable += $ttl_pr_recover;
                        }
                    }
                    $ttl_pr_recover += $pr_recover;
                    $ttl_sr_recover += $sr_recover;
                    $principal_balance -= $pr_recover;
                    $principal_balance = ($principal_balance < 0) ? 0 : $principal_balance;

                    $pr_due = 0;
                    $sr_due = 0;
                    $pr_advance = 0;
                    $sr_advance = 0;
                    $status = 0;
                    if (strtotime($scdl->date) <= strtotime($date) && round($before_principal_balance) > 0) {
                        $pr_due = ($ttl_pr_recoverable > $ttl_pr_recover) ? $ttl_pr_recoverable - $ttl_pr_recover : 0;
                        $sr_due = ($ttl_sr_recoverable > $ttl_sr_recover) ? $ttl_sr_recoverable - $ttl_sr_recover : 0;

                        if (!$is_interest_only) { //Not interest only
                            $pr_advance = ($ttl_pr_recover > $ttl_pr_recoverable) ? $ttl_pr_recover - $ttl_pr_recoverable : 0;
                            $sr_advance = ($ttl_sr_recover > $ttl_sr_recoverable) ? $ttl_sr_recover - $ttl_sr_recoverable : 0;
                        }
                        $status = 1;
                    }
                    //Future Schedule but current payment---
                    if ($status == 0 && ($pr_recover > 0 || $sr_recover > 0)) {
                        $pr_due = ($temp_ttl_pr_recoverable > $ttl_pr_recover) ? $temp_ttl_pr_recoverable - $ttl_pr_recover : 0;
                        $sr_due = ($temp_ttl_sr_recoverable > $ttl_sr_recover) ? $temp_ttl_sr_recoverable - $ttl_sr_recover : 0;
                        //dd($sr_due);

                        if (!$is_interest_only) { //Not interest only
                            $pr_advance = ($ttl_pr_recover > $temp_ttl_pr_recoverable) ? $ttl_pr_recover - $temp_ttl_pr_recoverable : 0;
                            $sr_advance = ($ttl_sr_recover > $temp_ttl_sr_recoverable) ? $ttl_sr_recover - $temp_ttl_sr_recoverable : 0;
                        }
                    }
                    //---

                    //SR Outstanding---
                    $sr_outstanding -= $sr_recover;
                    //---

                    $principal_balance = round($principal_balance, 8);
                    $sr_outstanding = round($sr_outstanding, 8);
                    $pr_due = round($pr_due, 8);
                    $sr_due = round($sr_due, 8);
                    $pr_advance = round($pr_advance, 8);
                    $sr_advance = round($sr_advance, 8);
                    $qum_total_recover += $pr_recover + $sr_recover;
                    if ($is_interest_only) {
                        if ($principal_balance < 1) {
                            $principal_balance = 0;
                        }
                    } else {
                        if (($principal_balance + $sr_outstanding) < 1) {
                            $principal_balance = 0;
                            $sr_outstanding = 0;
                        }
                    }

                    $txScheduleArray[] = (object)[
                        "date" => $scdl->date,
                        "usage_day" => $scdl->day,
                        "principal_amount" => $before_principal_balance,
                        "int_installment_amount" => $int_installment_amount,
                        "prin_installment_amount" => $prin_installment_amount,
                        "after_principal" => $principal_balance,
                        "pr_outstanding" => $principal_balance,
                        "sr_outstanding" => $sr_outstanding,
                        "total_outstanding" => $principal_balance + $sr_outstanding,
                        "total_repay" => $principal_balance + $sr_outstanding + $qum_total_recover,
                        "pr_due" => $pr_due,
                        "sr_due" => $sr_due,
                        "pr_advance" => $pr_advance,
                        "sr_advance" => $sr_advance,
                        "qum_total_recover" => $qum_total_recover,
                        "total_recover" => $pr_recover + $sr_recover,
                        "pr_recover" => $pr_recover,
                        "sr_recover" => $sr_recover,
                        "transaction" => $transaction_recovered,
                        "schedule_date" => $scdl->date,
                        "status" => $status,
                    ];
                }

                //Over Due Payment for interest only **(In Future, we will buid, if overdue paymet will be allowed for interest only)**
                /*if($is_interest_only && ($is_transaction||$is_death) && !empty($last_schedule_date) && strtotime($date)>strtotime($last_schedule_date)) {
                    dd($last_schedule_date);

                    $txScheduleArray[($full_schedule_cnt-1)]->date = $date;
                }*/
                //---

                //SR Outstanding---
                if ($is_interest_only) {//interest only
                    $lastSrDue = collect($txScheduleArray)->where("status", 1)->last();
                    $lastSrDue = (!empty($lastSrDue)) ? $lastSrDue->sr_due : 0;
                    $status0_int = collect($txScheduleArray)->where("status", 0)->sum("int_installment_amount");
                    $status0_int += $lastSrDue;

                    $pr_outstanding = $loan_details->li_amount;
                    $txScheduleCnt = count($txScheduleArray);
                    $tmp_sr_outstanding = 0;
                    foreach ($txScheduleArray as $key => $txSchedule) {
                        if ($txSchedule->prin_installment_amount == 0 && $txSchedule->int_installment_amount == 0 && ((!is_null($migration_schedule_date) && strtotime($txSchedule->date) < strtotime($migration_schedule_date) && $txSchedule->total_recover == 0) || $txSchedule->principal_amount <= 0)) {
                            $txSchedule->pr_outstanding = 0;
                            $txSchedule->sr_outstanding = 0;
                            $txSchedule->total_outstanding = 0;
                        } else {
                            //PR Outstanding
                            $pr_outstanding = ($txSchedule->status == 1) ? $txSchedule->after_principal : $pr_outstanding;
                            //Recover before schedule
                            if ($txSchedule->status == 0 && $txSchedule->total_recover > 0) {
                                $pr_outstanding -= $txSchedule->pr_recover;
                                $status0_int -= $txSchedule->sr_recover;
                            }

                            //SR Outstanding
                            if ($txSchedule->status == 0 && !$is_death) {
                                $sr_outstanding = $status0_int;
                            } else {
                                if (!$is_death || strtotime($txSchedule->date) < strtotime($member_death_date)) {
                                    if ($is_death) {
                                        $principal_amount = isset($txScheduleArray[$key - 1]) ? $txScheduleArray[$key - 1]->after_principal : $loan_details->li_amount;
                                        foreach ($txSchedule->transaction as $txData) {
                                            if (strtotime($txData['date']) < strtotime($member_death_date)) {
                                                $principal_amount -= $txData['pr_amount'];
                                            }
                                        }
                                        $sr_outstanding = isset($txScheduleArray[$key - 1]) ? $txScheduleArray[$key - 1]->sr_due : 0;
                                        $sr_outstanding += $txSchedule->int_installment_amount - $txSchedule->sr_recover;
                                    } else {
                                        $principal_amount = $txSchedule->after_principal;
                                        $sr_outstanding = $txSchedule->sr_due;
                                    }

                                    for ($i = $key + 1; $i < $txScheduleCnt; $i++) {
                                        $tmp_schedule = $txScheduleArray[$i];

                                        if (round($principal_amount) > 0) {
                                            $int_installment_amount = $principal_amount * $interest_rate;
                                            $prin_installment_amount = ($principal_amount >= $tmp_schedule->prin_installment_amount) ? $tmp_schedule->prin_installment_amount : $principal_amount;

                                            $principal_amount -= $prin_installment_amount;
                                            $sr_outstanding += $int_installment_amount;
                                        }
                                    }
                                    $tmp_sr_outstanding = $sr_outstanding;
                                } else {
                                    $tmp_sr_outstanding -= $txSchedule->sr_recover;
                                    $sr_outstanding = $tmp_sr_outstanding;
                                }
                            }

                            $txSchedule->pr_outstanding = $pr_outstanding;
                            $txSchedule->sr_outstanding = $sr_outstanding;
                            $txSchedule->total_outstanding = $pr_outstanding + $sr_outstanding;
                        }
                        $txSchedule->total_repay = $txSchedule->total_outstanding + $txSchedule->qum_total_recover;
                    }
                }
                //---
            } else { //Decline
                if ($is_transaction || $is_death) {
                    if ($is_death) {
                        $txDate = date('Y-m-d', strtotime($member_death_date . ' - 1 day'));
                        $loan_tx[] = (object)['rlt_transcation_date' => $txDate, 'rlt_amount' => 0, 'pr_amount' => 0, 'sr_amount' => 0, 'notTx' => true];
                        $loan_tx[] = (object)['rlt_transcation_date' => $member_death_date, 'rlt_amount' => 0, 'pr_amount' => 0, 'sr_amount' => 0, 'notTx' => true];
                    }
                    if ($is_transaction) {
                        $loan_tx[] = (object)['rlt_transcation_date' => $funcDate, 'rlt_amount' => 0, 'pr_amount' => 0, 'sr_amount' => 0, 'notTx' => true];
                    }
                    if ($is_afterDeathTx) {
                        $loan_tx = collect($loan_tx)->sortBy('rlt_transcation_date')->values()->all();
                    }

                    $cntLoanTx = count($loan_tx);

                    if (count($regular_schedule) == 0 && count($future_schedule) > 0) {
                        $regular_schedule = array_merge([$future_schedule[0]], $regular_schedule);
                        unset($future_schedule[0]);
                    }
                }

                $daily_interest_rate = $yearly_interest_rate / (100 * 365);
                $loan_details->calculated_interest_rate = $daily_interest_rate;
                //$arg_date_recovered = false;

                foreach ($regular_schedule as $s => $scdl) {
                    $curScdlLastdate = self::curScdlLastdate($scdl->date, $loan_details->li_payment_frequency, $full_schedule, $s);
                    $is_recovered = false;

                    //Transaction
                    foreach ($loan_tx as $i => $tx) {
                        if ($tx->rlt_amount == 0 && !$is_migrate && @$tx->notTx !== true) {
                            unset($loan_tx[$i]);
                            continue;
                        }

                        if (strtotime($tx->rlt_transcation_date) <= strtotime($curScdlLastdate)) {
                            //Migration---
                            if ($is_migrate) {
                                if ($tx->rlt_transcation_date == $schedule_disbursment_date && $tx->rlt_amount == 0 && @$tx->notTx !== true) {
                                    unset($loan_tx[$i]);
                                    $is_migrate = false;
                                    continue;
                                }

                                $tx->mig_int_installment_amount = $tx->sr_amount + $loan_details->migration_service_charge_due;
                                if (!$is_interest_only) {
                                    $tx->mig_prin_installment_amount = $tx->pr_amount + $loan_details->migration_principal_due;
                                }
                                $is_migrate = false;
                            }
                            //--

                            $txQ = count($txScheduleArray) - 1;
                            $lastThisTxSchdl = (isset($txScheduleArray[$txQ]) && $txScheduleArray[$txQ]->schedule_date == $scdl->date) ? $txScheduleArray[$txQ] : [];
                            $is_lastSchdl = ($last_schedule_date == $scdl->date);
                            $is_disableRecoverable = ($is_death && strtotime($tx->rlt_transcation_date) >= strtotime($member_death_date));
                            $txSchedule = (array)$helper->txSchedule($daily_interest_rate, $loan_details, $tx, $last_date, $total_recover_pr, $qum_total_recover, $pr_due, $sr_due, $is_interest_only, $lastThisTxSchdl, $is_lastSchdl, $is_disableRecoverable);
                            if ($is_transaction === true) {
                                $txStatus = (@$tx->notTx === true) ? 0 : 1;
                                if ($is_death) {
                                    $txStatus = ($i == ($cntLoanTx - 1)) ? 0 : 1;
                                }
                            } else {
                                $txStatus = 1;
                            }
                            $txSchedule = (object)array_merge($txSchedule, [
                                "transaction" => [[
                                    'date' => $tx->rlt_transcation_date,
                                    'pr_amount' => $tx->pr_amount,
                                    'sr_amount' => $tx->sr_amount,
                                    'rlt_amount' => $tx->rlt_amount
                                ]],
                                "schedule_date" => $scdl->date,
                                "status" => $txStatus,
                            ]);
                            unset($loan_tx[$i]);
                            $is_recovered = true;
                            $txScheduleArray[] = $txSchedule;
                        } else {
                            break;
                        }
                    }//End Foreach

                    // Set Due
                    if (!$is_recovered) {
                        $tx = (object)[
                            "rlt_transcation_date" => $scdl->date,
                            "rlt_amount" => 0,
                            "pr_amount" => 0,
                            "sr_amount" => 0
                        ];

                        //Migration---
                        if ($is_migrate) {
                            $tx->mig_int_installment_amount = 0;
                            if (!$is_interest_only) {
                                $tx->mig_prin_installment_amount = 0;
                            }
                        }
                        //--

                        $txQ = count($txScheduleArray) - 1;
                        $lastThisTxSchdl = (isset($txScheduleArray[$txQ]) && $txScheduleArray[$txQ]->schedule_date == $scdl->date) ? $txScheduleArray[$txQ] : [];
                        $is_lastSchdl = ($last_schedule_date == $scdl->date);
                        $is_disableRecoverable = ($is_death && strtotime($tx->rlt_transcation_date) >= strtotime($member_death_date));
                        $txSchedule = (array)$helper->txSchedule($daily_interest_rate, $loan_details, $tx, $last_date, $total_recover_pr, $qum_total_recover, $pr_due, $sr_due, $is_interest_only, $lastThisTxSchdl, $is_lastSchdl, $is_disableRecoverable);
                        $txSchedule = (object)array_merge($txSchedule, [
                            "transaction" => [[
                                'date' => $tx->rlt_transcation_date,
                                'pr_amount' => $tx->pr_amount,
                                'sr_amount' => $tx->sr_amount,
                                'rlt_amount' => $tx->rlt_amount
                            ]],
                            "schedule_date" => $scdl->date,
                            "status" => $txSchedule['principal_amount'] > 0 ? 1 : 0,
                        ]);
                        $is_recovered = true;
                        $txScheduleArray[] = $txSchedule;
                    }//endif

                    if (isset($txSchedule)) {
                        $principal_balance = $txSchedule->after_principal;
                        //Last Schedule Adjustment
                        /*if(strtotime($last_schedule_date)==strtotime($txSchedule->schedule_date)) {
                            $adj_prin_installment_amount = $txSchedule->principal_amount;
                            $txSchedule_cnt = count($txScheduleArray);
                            if(isset($txScheduleArray[($txSchedule_cnt-2)])) {
                                $adj_prin_installment_amount -= $txScheduleArray[($txSchedule_cnt-2)]->pr_due;
                            }

                            $txScheduleArray[$txSchedule_cnt-1]->prin_installment_amount = $adj_prin_installment_amount;
                        }*/
                    }
                }//endforeach

                //After Today
                $total_date = count($future_schedule);
                $i = 0;

                foreach ($future_schedule as $s => $scdl) {
                    //Rest Transaction for 1st future date---
                    $is_recovered = false;
                    //if($i==0) { //Comment for after death tx
                    $curScdlLastdate = self::curScdlLastdate($scdl->date, $loan_details->li_payment_frequency, $full_schedule, $s);
                    foreach ($loan_tx as $tx_i => $tx) {
                        if ($tx->rlt_amount == 0 && !$is_migrate && @$tx->notTx !== true) {
                            unset($loan_tx[$i]);
                            continue;
                        }

                        if (strtotime($tx->rlt_transcation_date) <= strtotime($curScdlLastdate)) {
                            //Migration---
                            if ($is_migrate) {
                                if ($tx->rlt_transcation_date == $schedule_disbursment_date && $tx->rlt_amount == 0 && @$tx->notTx !== true) {
                                    unset($loan_tx[$tx_i]);
                                    $is_migrate = false;
                                    continue;
                                }

                                $tx->mig_int_installment_amount = $tx->sr_amount + $loan_details->migration_service_charge_due;
                                if (!$is_interest_only) {
                                    $tx->mig_prin_installment_amount = $tx->pr_amount + $loan_details->migration_principal_due;
                                }
                                $is_migrate = false;
                            }
                            //--

                            $txQ = count($txScheduleArray) - 1;
                            $lastThisTxSchdl = (isset($txScheduleArray[$txQ]) && $txScheduleArray[$txQ]->schedule_date == $scdl->date) ? $txScheduleArray[$txQ] : [];
                            $is_lastSchdl = ($last_schedule_date == $scdl->date);
                            $is_disableRecoverable = ($is_death && strtotime($tx->rlt_transcation_date) >= strtotime($member_death_date));
                            $txSchedule = (array)$helper->txSchedule($daily_interest_rate, $loan_details, $tx, $last_date, $total_recover_pr, $qum_total_recover, $pr_due, $sr_due, $is_interest_only, $lastThisTxSchdl, $is_lastSchdl, $is_disableRecoverable);
                            if ($is_transaction === true) {
                                $txStatus = (@$tx->notTx === true) ? 0 : 1;
                                if ($is_death) {
                                    $txStatus = ($i == ($cntLoanTx - 1)) ? 0 : 1;
                                }
                            } else {
                                $txStatus = 1;
                            }
                            $txSchedule = (object)array_merge($txSchedule, [
                                "transaction" => [[
                                    'date' => $tx->rlt_transcation_date,
                                    'pr_amount' => $tx->pr_amount,
                                    'sr_amount' => $tx->sr_amount,
                                    'rlt_amount' => $tx->rlt_amount
                                ]],
                                "schedule_date" => $scdl->date,
                                "status" => $txStatus,
                            ]);

                            unset($loan_tx[$tx_i]);
                            $is_recovered = true;
                            $txScheduleArray[] = $txSchedule;
                        } else {
                            break;
                        }
                    }

                    if ($is_recovered) {
                        $principal_balance = $txSchedule->after_principal;
                        //Last Schedule Adjustment
                        /*if(strtotime($last_schedule_date)==strtotime($txSchedule->schedule_date)) {
                            $adj_prin_installment_amount = $txSchedule->principal_amount;
                            $txSchedule_cnt = count($txScheduleArray);
                            if(isset($txScheduleArray[($txSchedule_cnt-2)])) {
                                $adj_prin_installment_amount -= $txScheduleArray[($txSchedule_cnt-2)]->pr_due;
                            }

                            $txScheduleArray[$txSchedule_cnt-1]->prin_installment_amount = $adj_prin_installment_amount;
                        }*/
                    }
                    //}
                    //----

                    if (!$is_recovered) {
                        $diff_day = date_diff(date_create($last_date), date_create($scdl->date));
                        $day = $diff_day->format("%a");

                        $declineInstallment = $schedule_builder->declineInstallment($s + 1, $scdl->date, $loan_details->li_amount, $principal_balance, $loan_details->li_installment_amount, $daily_interest_rate, $day, ($i == ($total_date - 1)));

                        if ($principal_balance > 0) {
                            $before_principal_balance = $principal_balance;
                            $int_installment_amount = $declineInstallment['int_installment_amount'];
                            $prin_installment_amount = $declineInstallment['prin_installment_amount'];
                            if ($is_interest_only) {
                                $prin_installment_amount = ($i == ($total_date - 1)) ? $principal_balance : 0;
                            }
                            $principal_balance -= $prin_installment_amount;
                        } else {
                            $before_principal_balance = 0;
                            $principal_balance = 0;
                            $int_installment_amount = 0;
                            $prin_installment_amount = 0;
                        }

                        $is_disableRecoverable = ($is_death && strtotime($declineInstallment['installment_date']) >= strtotime($member_death_date));
                        $txSchedule = (object)[
                            "date" => $declineInstallment['installment_date'],
                            "usage_day" => $declineInstallment['usage_day'],
                            "principal_amount" => $before_principal_balance,
                            "int_installment_amount" => (!$is_disableRecoverable) ? $int_installment_amount : 0,
                            "prin_installment_amount" => (!$is_disableRecoverable) ? $prin_installment_amount : 0,
                            "after_principal" => $principal_balance,
                            "pr_outstanding" => 0,
                            "sr_outstanding" => 0,
                            "total_outstanding" => 0,
                            "total_repay" => 0,
                            "pr_due" => 0,
                            "sr_due" => 0,
                            "qum_total_recover" => 0,
                            "total_recover" => 0,
                            "pr_recover" => 0,
                            "sr_recover" => 0,
                            "transaction" => [],
                            "schedule_date" => $declineInstallment['installment_date'],
                            "status" => 0,
                        ];

                        //Last Schedule Adjustment
                        // if(strtotime($last_schedule_date)==strtotime($txSchedule->schedule_date)) {
                        //     $txSchedule->prin_installment_amount = $txSchedule->principal_amount-$pr_due;
                        // }

                        $txScheduleArray[] = $txSchedule;
                        $last_date = $scdl->date;
                    }
                    $i++;
                }

                //Over Due Payment
                if (count($loan_tx) > 0 && !empty($last_schedule_date)) {
                    foreach ($loan_tx as $i => $tx) {
                        if (strtotime($tx->rlt_transcation_date) > strtotime($last_schedule_date)) {
                            $diff_day = date_diff(date_create($last_date), date_create($tx->rlt_transcation_date));
                            $day = $diff_day->format("%a");

                            //Migration---
                            if ($is_migrate) {
                                $tx->mig_int_installment_amount = $tx->sr_amount + $loan_details->migration_service_charge_due;
                                if (!$is_interest_only) {
                                    $tx->mig_prin_installment_amount = $tx->pr_amount + $loan_details->migration_principal_due;
                                }
                                $is_migrate = false;
                            }
                            //--

                            $txQ = count($txScheduleArray) - 1;
                            $lastThisTxSchdl = (isset($txScheduleArray[$txQ]) && $txScheduleArray[$txQ]->schedule_date == $scdl->date) ? $txScheduleArray[$txQ] : [];
                            $is_lastSchdl = ($last_schedule_date == $scdl->date);
                            $is_disableRecoverable = ($is_death && strtotime($tx->rlt_transcation_date) >= strtotime($member_death_date));
                            $txSchedule = (array)$helper->txSchedule($daily_interest_rate, $loan_details, $tx, $last_date, $total_recover_pr, $qum_total_recover, $pr_due, $sr_due, true, $lastThisTxSchdl, $is_lastSchdl, $is_disableRecoverable);
                            $txSchedule = (object)array_merge($txSchedule, [
                                "transaction" => [[
                                    'date' => $tx->rlt_transcation_date,
                                    'pr_amount' => $tx->pr_amount,
                                    'sr_amount' => $tx->sr_amount,
                                    'rlt_amount' => $tx->rlt_amount
                                ]],
                                "schedule_date" => $last_schedule_date,
                                "status" => 1,
                            ]);

                            unset($loan_tx[$i]);
                            $txScheduleArray[] = $txSchedule;
                        }
                    }
                }
                //---

                //Loan Schedule Interest Amount---
                $full_schedule_interest = 0;
                $principal_amount = $loan_details->li_amount;
                foreach ($full_schedule as $scdl) {
                    if (round($principal_amount) > 0) {
                        $int_installment_amount = $principal_amount * $scdl->day * $daily_interest_rate;
                        $prin_installment_amount = $is_interest_only ? 0 : $loan_details->li_installment_amount - $int_installment_amount;

                        $principal_amount -= $prin_installment_amount;
                        $full_schedule_interest += $int_installment_amount;
                    }
                }
                //---

                //SR Outstanding---
                $lastSrDue = collect($txScheduleArray)->where("status", 1)->last();
                $lastSrDue = (!empty($lastSrDue)) ? $lastSrDue->sr_due : 0;
                $status0_int = collect($txScheduleArray)->where("status", 0)->sum("int_installment_amount");
                $status0_int += $lastSrDue;

                $pr_outstanding = $loan_details->li_amount;
                $txScheduleCnt = count($txScheduleArray);
                $tmp_sr_outstanding = 0;
                foreach ($txScheduleArray as $key => $txSchedule) {
                    if ($txSchedule->prin_installment_amount == 0 && $txSchedule->int_installment_amount == 0 && ((!is_null($migration_schedule_date) && strtotime($txSchedule->date) < strtotime($migration_schedule_date) && $txSchedule->total_recover == 0) || $txSchedule->principal_amount <= 0)) {
                        $txSchedule->pr_outstanding = 0;
                        $txSchedule->sr_outstanding = 0;
                        $txSchedule->total_outstanding = 0;
                    } else {
                        //PR Outstanding
                        $pr_outstanding = ($txSchedule->status == 1) ? $txSchedule->after_principal : $pr_outstanding;
                        $txSchedule->pr_outstanding = $pr_outstanding;
                        //SR Outstanding
                        if ($txSchedule->status == 0 && !$is_death) {
                            $sr_outstanding = $status0_int;
                        } else {
                            if (!$is_death || strtotime($txSchedule->date) < strtotime($member_death_date)) {
                                $principal_amount = $txSchedule->after_principal;
                                $sr_outstanding = $txSchedule->sr_due;
                                $date_schedule = $txSchedule->date;
                                $tmp_schedule_date = $txSchedule->schedule_date;
                                for ($i = $key + 1; $i < $txScheduleCnt; $i++) {
                                    $tmp_schedule = $txScheduleArray[$i];
                                    if ($tmp_schedule_date == $tmp_schedule->schedule_date) {
                                        continue;
                                    }

                                    $diff_day = date_diff(date_create($date_schedule), date_create($tmp_schedule->schedule_date));
                                    $day = $diff_day->format("%a");
                                    if (round($principal_amount) > 0) {
                                        $int_installment_amount = $principal_amount * $day * $daily_interest_rate;
                                        $prin_installment_amount = $is_interest_only ? 0 : $loan_details->li_installment_amount - $int_installment_amount;

                                        $principal_amount -= $prin_installment_amount;
                                        $sr_outstanding += $int_installment_amount;
                                    }

                                    $date_schedule = $tmp_schedule->schedule_date;
                                    $tmp_schedule_date = $tmp_schedule->schedule_date;
                                }
                                $tmp_sr_outstanding = $sr_outstanding;
                            } else {
                                $tmp_sr_outstanding -= $txSchedule->sr_recover;
                                $sr_outstanding = $tmp_sr_outstanding;
                            }
                        }
                        $txSchedule->sr_outstanding = $sr_outstanding;
                        $txSchedule->total_outstanding = $pr_outstanding + $sr_outstanding;
                    }
                    $txSchedule->total_repay = $txSchedule->total_outstanding + $txSchedule->qum_total_recover;
                }
                //---
            }
            $loan_details->txSchedules = $txScheduleArray;
            $loan_details->full_schedule_interest = $full_schedule_interest;

            //Call Function---
            if ($func1 !== false) {
                $loan_details = $func1($loan_details, $loan_tx_func, $funcArg1);
            }
            if ($func2 !== false) {
                $loan_details = $func2($loan_details, $loan_tx_func, $funcArg2);
            }
            //---

            $loansutput[] = $loan_details;
        }
        return $loansutput;
    }

    public function loanData($loans, $transactions, $date, $func = false, $funcArg = [], $opening_date = false)
    {
        $opening_date = ($opening_date !== false && strtotime($opening_date) <= strtotime($date)) ? $opening_date : false;

        //For Trench
        $trenchLoans = collect($loans)->where('loan_type', 2)->pluck('li_id')->all();
        $opening_trench = (!empty($trenchLoans) && $opening_date !== false) ? trench_model::select('loan_id', DB::raw('sum(trench_principal_amount) as ttlTrenchAmount'))->whereIn('loan_id', $trenchLoans)->where('trench_disburse_date', '<', $opening_date)->groupBy('loan_id')->get()->keyBy('loan_id')->all() : [];
        $closing_trench = (!empty($trenchLoans)) ? trench_model::whereIn('loan_id', $trenchLoans)->where('trench_disburse_date', '<=', $date)->orderBy('trench_disburse_date', 'asc')->get()->groupBy('loan_id')->all() : [];
        //----

        $loanDataFuncArg = ['date' => $date, 'opening_date' => $opening_date, 'opening_trench' => $opening_trench, 'closing_trench' => $closing_trench];
        $loanDataFunc = function ($loan_details, $loan_tx, $loanDataFuncArg) {
            extract($loanDataFuncArg);

            if (isset($closing_trench[$loan_details->li_id])) {
                //For Trench
                $trenchTxSchedule = [];
                $trenchLoanSchedule = []; /*$trench_sr_recoverable=0;*/
                $usage_day = 0;
                $principal_balance = 0;
                $trench_day_interest = 0;
                $qum_total_recover = 0;
                $trench_transaction = [];
                foreach ($closing_trench[$loan_details->li_id] as $trenchData) {
                    $principal_balance += $trenchData->trench_principal_amount;
                    $qum_total_recover += $trench_day_interest;

                    $txSchedule = (object)[
                        "date" => $trenchData->trench_disburse_date,
                        "usage_day" => $usage_day,
                        "principal_amount" => $principal_balance,
                        "int_installment_amount" => 0,
                        "prin_installment_amount" => 0,
                        "after_principal" => $principal_balance,
                        "pr_outstanding" => $principal_balance,
                        "sr_outstanding" => 0,
                        "total_outstanding" => 0,
                        "total_repay" => $principal_balance,
                        "pr_due" => 0,
                        "sr_due" => 0,
                        "qum_total_recover" => $qum_total_recover,
                        "total_recover" => $trench_day_interest,
                        "pr_recover" => 0,
                        "sr_recover" => $trench_day_interest,
                        "transaction" => $trench_transaction,
                        "schedule_date" => $trenchData->trench_disburse_date,
                        "status" => 1,
                    ];
                    $trenchTxSchedule[] = $txSchedule;
                    $trenchLoanSchedule[] = $trenchData->trench_disburse_date;

                    //Recoverable--
                    /*if($txSchedule->int_installment_amount>0 && strtotime($txSchedule->date)>=strtotime($opening_date) && strtotime($txSchedule->date)<=strtotime($date)) {
                        $trench_sr_recoverable+=$txSchedule->int_installment_amount;
                    }*/
                    //--

                    $usage_day = $trenchData->trench_day;
                    $trench_day_interest = ($trenchData->trench_day_interest > 0) ? $trenchData->trench_day_interest : 0;
                    $trench_transaction = ($trenchData->trench_day_interest > 0) ? [[
                        "date" => $trenchData->interest_collection_date,
                        "pr_amount" => 0,
                        "sr_amount" => $trenchData->trench_day_interest,
                        "rlt_amount" => $trenchData->trench_day_interest
                    ]] : [];
                }

                $pr_outstanding = $principal_balance;
                $trenchDisburseAmount = $principal_balance;
            } else {
                //General
                $trenchTxSchedule = [];
                $trenchLoanSchedule = [];
                $pr_outstanding = 0;
                $trenchDisburseAmount = 0;
            }

            $sr_outstanding = 0;
            $total_outstanding = $pr_outstanding;
            $total_repay = $pr_outstanding;
            $pr_advance = 0;
            $sr_advance = 0;
            $total_advance = 0;
            $pr_due = 0;
            $sr_due = 0;
            $total_due = 0;
            $due_payment_qty = 0;
            $onTime_payment_qty = 0;
            $advance_payment_qty = 0;
            $transactionSchedule = $loan_details->txSchedules;
            $is_interest_only = ($loan_details->loan_type == 3);
            $is_flat = ($loan_details->li_interest_calculation_method == 'flat');
            $schedule_disbursment_date = ($loan_details->loan_type == 2 && $loan_details->number_of_trench == $loan_details->trench_status) ? $loan_details->li_final_trench_release_date : $loan_details->li_disbursment_date;

            //Opening Data--
            $is_this_loan_opening = ($opening_date !== false && (strtotime($schedule_disbursment_date) < strtotime($opening_date) || isset($opening_trench[$loan_details->li_id]))) ? true : false;
            $is_opening_data = $is_this_loan_opening;
            $is_opening_death_data = ($is_this_loan_opening && !empty($loan_details->member_death_date) && strtotime($loan_details->member_death_date) < strtotime($opening_date));
            $opening_pr_outstanding = $is_this_loan_opening ? (isset($opening_trench[$loan_details->li_id]) ? $opening_trench[$loan_details->li_id]->ttlTrenchAmount : $loan_details->li_amount) : 0;
            $opening_sr_outstanding = ($opening_pr_outstanding == $loan_details->li_amount) ? $loan_details->full_schedule_interest : 0;
            $opening_total_outstanding = $opening_pr_outstanding + $opening_sr_outstanding;
            $opening_pr_advance = 0;
            $opening_sr_advance = 0;
            $opening_total_advance = 0;
            $opening_pr_due = 0;
            $opening_sr_due = 0;
            $opening_total_due = 0;
            $pr_recoverable = 0;
            $sr_recoverable = 0;
            $total_recoverable = 0;
            $op_pr_recoverable = 0;
            $op_sr_recoverable = 0;
            $opening_txSchedule = '';
            $out_opening_txSchedule = '';
            if ($is_flat && $is_interest_only) {
                $out_opening_nextTxSchedule = isset($transactionSchedule[0]) ? $transactionSchedule[0] : '';
                $out_opening_nextTxSchedule_cnt = count($transactionSchedule) - 1;
            }
            //--

            if (strtotime($schedule_disbursment_date) <= strtotime($date)) {
                $nextSchedule = collect($transactionSchedule)->where('status', 0)->first();
                $lastActiveSchedule = collect($transactionSchedule)->where('status', 1)->last();
                if ((!empty($nextSchedule) && ($nextSchedule->pr_recover > 0 || $nextSchedule->sr_recover > 0)) || empty($lastActiveSchedule)) {
                    //Future Schedule but current payment or no active schedule
                    $curSchedule = $nextSchedule;
                } else {
                    $curSchedule = $lastActiveSchedule;
                }

                if (!empty($curSchedule)) {
                    $pr_outstanding = $curSchedule->pr_outstanding;
                    $sr_outstanding = $curSchedule->sr_outstanding;
                    $total_outstanding = $curSchedule->total_outstanding;
                    $total_repay = $curSchedule->total_repay;

                    $pr_advance = ($is_flat && !$is_interest_only) ? $curSchedule->pr_advance : 0;
                    $sr_advance = ($is_flat && !$is_interest_only) ? $curSchedule->sr_advance : 0;
                    $total_advance = ($is_flat && !$is_interest_only) ? $pr_advance + $sr_advance : 0;

                    if (!empty($loan_details->member_death_date)) {
                        $pr_due = 0;
                        $sr_due = 0;
                    } else {
                        $pr_due = $curSchedule->pr_due;
                        $sr_due = $curSchedule->sr_due;
                    }
                    $total_due = $pr_due + $sr_due;

                    //For Death Member---
                    /*if(!empty($loan_details->member_death_date) || strtotime($curSchedule->date)<strtotime($date)) {
                        $activeDeathSchdl = collect($transactionSchedule)->where('date', '<=', $date)->last();
                        if(!empty($activeDeathSchdl)) {
                            $pr_outstanding = $activeDeathSchdl->pr_outstanding;
                            $sr_outstanding = $activeDeathSchdl->sr_outstanding;
                            $total_outstanding = $activeDeathSchdl->total_outstanding;
                        }
                    }*/
                    //---
                }

                //Payment---
                $tmp_pr_recoverable = 0;
                $tmp_sr_recoverable = 0;
                $preSchedule = "";
                $scdlTempDate = "";
                $scdlTempSrRecoverable = 0;
                foreach ($transactionSchedule as $key => $txSchedule) {
                    if ($is_flat) {
                        $payments = $txSchedule->transaction;

                        $preDue = !empty($preSchedule) ? $preSchedule->pr_due + $preSchedule->sr_due : 0;
                        $recoverable = $txSchedule->int_installment_amount + $txSchedule->prin_installment_amount;

                        foreach ($payments as $payment) {
                            if ($payment['rlt_amount'] > 0) {
                                $payAmount = $payment['rlt_amount'];
                                if ($payAmount > 0 && $recoverable > 0) {
                                    $onTime_payment_qty++;
                                    $tmp_payAmount = $payAmount;
                                    $payAmount -= $recoverable;
                                    $recoverable -= $tmp_payAmount;
                                }
                                if ($payAmount > 0 && $preDue > 0) {
                                    $due_payment_qty++;
                                    $payAmount -= $preDue;
                                    $preDue -= $payment['rlt_amount'];
                                }
                                if ($payAmount > 0 && !$is_interest_only) {
                                    $advance_payment_qty++;
                                }
                            }
                        }
                    } else {
                        if ($txSchedule->total_recover > 0) {
                            $payAmount = $txSchedule->total_recover;
                            $preDue = !empty($preSchedule) ? $preSchedule->pr_due + $preSchedule->sr_due : 0;
                            if ($preDue > 0) {
                                $due_payment_qty++;
                                $payAmount -= $preDue;
                            }
                            if ($payAmount > 0) {
                                $onTime_payment_qty++;
                            }
                        }
                    }
                    $preSchedule = $txSchedule;

                    //Opening Data--
                    if ($is_opening_data) {
                        if (!isset($transactionSchedule[$key + 1]) || strtotime($transactionSchedule[$key + 1]->date) >= strtotime($opening_date)) {
                            if (strtotime($txSchedule->date) < strtotime($opening_date)) {
                                $opening_txSchedule = $txSchedule;
                                if ($is_flat && $is_interest_only && !$is_opening_death_data) {
                                    $afterOpeningTx = false;
                                    foreach ($opening_txSchedule->transaction as $txData) {
                                        if (strtotime($txData['date']) >= strtotime($opening_date)) {
                                            $afterOpeningTx = true;
                                            break;
                                        }
                                    }
                                    if ($afterOpeningTx) {
                                        $out_opening_txSchedule = (isset($transactionSchedule[$key - 1])) ? $transactionSchedule[$key - 1] : '';
                                        $out_key = $key - 1;
                                    } else {
                                        $out_opening_txSchedule = $opening_txSchedule;
                                        $out_key = $key;
                                    }
                                    $out_opening_nextTxSchedule = isset($transactionSchedule[$out_key + 1]) ? $transactionSchedule[$out_key + 1] : '';
                                    $out_opening_nextTxSchedule_cnt -= $out_key + 1;
                                }
                            }
                            $is_opening_data = false;
                        }

                        //Recoverable
                        if ($is_flat && strtotime($txSchedule->date) < strtotime($opening_date)) {
                            $op_pr_recoverable += $txSchedule->prin_installment_amount;
                            $op_sr_recoverable += $txSchedule->int_installment_amount;

                            if ($is_interest_only && isset($transactionSchedule[$key + 1])) {
                                $op_pr_recoverable += $txSchedule->pr_recover;
                            }
                        }
                    }

                    if ($is_flat && $is_interest_only && $is_opening_death_data) {
                        if (!isset($transactionSchedule[$key + 1]) || strtotime($transactionSchedule[$key + 1]->date) >= strtotime($loan_details->member_death_date)) {
                            if (strtotime($txSchedule->date) < strtotime($loan_details->member_death_date)) {
                                $afterDeathTx = false;
                                foreach ($txSchedule->transaction as $txData) {
                                    if (strtotime($txData['date']) >= strtotime($loan_details->member_death_date)) {
                                        $afterDeathTx = true;
                                        break;
                                    }
                                }
                                if ($afterDeathTx) {
                                    $out_opening_txSchedule = (isset($transactionSchedule[$key - 1])) ? $transactionSchedule[$key - 1] : '';
                                    $out_key = $key - 1;
                                } else {
                                    $out_opening_txSchedule = $opening_txSchedule;
                                    $out_key = $key;
                                }
                                $out_opening_nextTxSchedule = isset($transactionSchedule[$out_key + 1]) ? $transactionSchedule[$out_key + 1] : '';
                                $out_opening_nextTxSchedule_cnt -= $out_key + 1;
                            }
                            $is_opening_death_data = false;
                        }
                    }
                    //--

                    //Recoverable--
                    $is_rec_allow = (@$is_rec_allow === false) ? false : (empty($loan_details->member_death_date) || strtotime($txSchedule->date) < strtotime($loan_details->member_death_date));

                    if ($scdlTempDate != $txSchedule->schedule_date) {
                        $scdlTempDate = $txSchedule->schedule_date;
                        $scdlTempSrRecoverable = 0; //For Decline
                    }
                    if (strtotime($txSchedule->date) >= strtotime($opening_date) && strtotime($txSchedule->date) <= strtotime($date)) {
                        $tmp_pr_recoverable += $txSchedule->prin_installment_amount;
                        $tmp_sr_recoverable += $txSchedule->int_installment_amount + $scdlTempSrRecoverable;
                    } else {
                        $scdlTempSrRecoverable += $txSchedule->int_installment_amount;
                    }

                    if ($is_rec_allow) {
                        if (!isset($transactionSchedule[$key + 1]) || $transactionSchedule[$key + 1]->schedule_date != $txSchedule->schedule_date) {
                            $pr_recoverable += $tmp_pr_recoverable;
                            $sr_recoverable += $tmp_sr_recoverable;
                            $tmp_pr_recoverable = 0;
                            $tmp_sr_recoverable = 0;
                        }
                    }
                    //--
                }
                //---
            }

            //Opening Data--
            if ($is_this_loan_opening) {
                if (!$is_flat && !empty($opening_txSchedule)) {
                    $opening_pr_outstanding = $opening_txSchedule->pr_outstanding;
                    $opening_sr_outstanding = $opening_txSchedule->sr_outstanding;

                    //$opening_pr_advance = ($is_flat && !$is_interest_only) ? $opening_txSchedule->pr_advance : 0;
                    //$opening_sr_advance = ($is_flat && !$is_interest_only) ? $opening_txSchedule->sr_advance : 0;

                    $opening_pr_due = $opening_txSchedule->pr_due;
                    $opening_sr_due = $opening_txSchedule->sr_due;
                }

                if ($is_flat) {
                    $payments = collect($loan_tx)->where('rlt_transcation_date', '<', $opening_date);
                    $op_pr_recover = $payments->sum('pr_amount');
                    $op_sr_recover = $payments->sum('sr_amount');
                    $death_before_opening = (!empty($loan_details->member_death_date) && strtotime($loan_details->member_death_date) < strtotime($opening_date));

                    //Due
                    if ($death_before_opening) {
                        $opening_pr_due = 0;
                        $opening_sr_due = 0;

                        $payments = collect($loan_tx)->where('rlt_transcation_date', '<', $loan_details->member_death_date);
                        $before_death_pr_amount = $payments->sum('pr_amount');
                        $op_pr_recoverable = $before_death_pr_amount;
                        $op_sr_recoverable = $payments->sum('sr_amount');
                    } else {
                        $opening_pr_due = ($op_pr_recoverable > $op_pr_recover) ? $op_pr_recoverable - $op_pr_recover : 0;
                        $opening_sr_due = ($op_sr_recoverable > $op_sr_recover) ? $op_sr_recoverable - $op_sr_recover : 0;

                        $opening_pr_due = ($opening_pr_due < 0) ? 0 : $opening_pr_due;
                        $opening_sr_due = ($opening_sr_due < 0) ? 0 : $opening_sr_due;
                    }

                    //Advance
                    if (!$is_interest_only) {
                        $opening_pr_advance = ($op_pr_recover > $op_pr_recoverable) ? $op_pr_recover - $op_pr_recoverable : 0;
                        $opening_sr_advance = ($op_sr_recover > $op_sr_recoverable) ? $op_sr_recover - $op_sr_recoverable : 0;
                    }

                    //Outstanding
                    $opening_pr_outstanding -= $op_pr_recover;
                    if (!$is_interest_only) {
                        $opening_sr_outstanding -= $op_sr_recover;
                    }

                    /*if(!empty($opening_txSchedule) && empty($opening_nextTxSchedule)) {
                        //For Last transaction-
                        $payments = collect($opening_txSchedule->transaction)->where('date','>=',$opening_date);
                        $op_pr_recover = $payments->sum('pr_amount');
                        $op_sr_recover = $payments->sum('sr_amount');

                        if($op_pr_recover>0) {
                            if($is_interest_only) { $pr_recoverable-=$op_pr_recover; }
                            $opening_pr_advance_tmp = $opening_pr_advance;
                            $opening_pr_outstanding += $op_pr_recover;
                            if(!$is_interest_only && $opening_pr_advance>0) {
                                $opening_pr_advance -= $op_pr_recover;
                                $op_pr_recover -= $opening_pr_advance_tmp;
                                if($opening_pr_advance<0) { $opening_pr_advance=0; }
                            }
                            if($op_pr_recover>0) { $opening_pr_due+=$op_pr_recover; }
                        }
                        if($op_sr_recover>0) {
                            if($is_interest_only) { $sr_recoverable-=$op_sr_recover; }
                            $opening_sr_advance_tmp = $opening_sr_advance;
                            $opening_sr_outstanding += $op_sr_recover;
                            if(!$is_interest_only && $opening_sr_advance>0) {
                                $opening_sr_advance -= $op_sr_recover;
                                $op_sr_recover -= $opening_sr_advance_tmp;
                                if($opening_sr_advance<0) { $opening_sr_advance=0; }
                            }
                            if($op_sr_recover>0) { $opening_sr_due+=$op_sr_recover; }
                        }
                        //-
                    } else {
                        //First transaction-
                        if(empty($opening_txSchedule)) {
                            $payments = collect($loan_tx)->where('rlt_transcation_date','<',$opening_date);
                            //if(!empty($opening_txSchedule)) { $payments = $payments->where('rlt_transcation_date','>',$opening_txSchedule->date); }
                            $op_pr_recover = $payments->sum('pr_amount');
                            $op_sr_recover = $payments->sum('sr_amount');

                            if($op_pr_recover>0) {
                                $opening_pr_due_tmp = $opening_pr_due;
                                $opening_pr_outstanding -= $op_pr_recover;
                                if($opening_pr_due>0) {
                                    $opening_pr_due -= $op_pr_recover;
                                    $op_pr_recover -= $opening_pr_due_tmp;
                                    if($opening_pr_due<0) { $opening_pr_due=0; }
                                }
                                if($op_pr_recover>0) {
                                    if($is_interest_only) { $pr_recoverable-=$op_pr_recover; }
                                    else { $opening_pr_advance+=$op_pr_recover; }
                                }
                            }
                            if($op_sr_recover>0) {
                                $opening_sr_due_tmp = $opening_sr_due;
                                $opening_sr_outstanding -= $op_sr_recover;
                                if($opening_sr_due>0) {
                                    $opening_sr_due -= $op_sr_recover;
                                    $op_sr_recover -= $opening_sr_due_tmp;
                                    if($opening_sr_due<0) { $opening_sr_due=0; }
                                }
                                if($op_sr_recover>0) {
                                    if($is_interest_only) { $sr_recoverable-=$op_sr_recover; }
                                    else { $opening_sr_advance+=$op_sr_recover; }
                                }
                            }
                        }
                        //-
                    }*/
                }

                //SR Outstanding - interest_only--
                if ($is_flat && $is_interest_only) {
                    $opening_sr_outstanding = (!empty($out_opening_txSchedule)) ? $out_opening_txSchedule->sr_due : 0;
                    if (!empty($out_opening_nextTxSchedule)) {
                        $opening_sr_outstanding += $out_opening_nextTxSchedule->int_installment_amount;
                        $out_opening_date = ($death_before_opening) ? $loan_details->member_death_date : $opening_date;
                        foreach ($out_opening_nextTxSchedule->transaction as $txData) {
                            if (strtotime($txData['date']) < strtotime($out_opening_date)) {
                                $opening_sr_outstanding -= $txData['sr_amount'];
                            }
                        }
                    }
                    if ($out_opening_nextTxSchedule_cnt > 0) {
                        $out_opening_pr_outstanding = ($death_before_opening) ? $loan_details->li_amount - $before_death_pr_amount : $opening_pr_outstanding;
                        $opening_sr_outstanding += $out_opening_pr_outstanding * $loan_details->calculated_interest_rate * $out_opening_nextTxSchedule_cnt;
                    }
                }
                //--

                $opening_pr_outstanding = round($opening_pr_outstanding, 8);
                $opening_sr_outstanding = round($opening_sr_outstanding, 8);
                $opening_pr_advance = round($opening_pr_advance, 8);
                $opening_sr_advance = round($opening_sr_advance, 8);
                $opening_pr_due = round($opening_pr_due, 8);
                $opening_sr_due = round($opening_sr_due, 8);
                if (($opening_pr_outstanding + $opening_sr_outstanding) < 1) {
                    $opening_pr_outstanding = 0;
                    $opening_sr_outstanding = 0;
                }

                $opening_total_outstanding = $opening_pr_outstanding + $opening_sr_outstanding;
                $opening_total_advance = $opening_pr_advance + $opening_sr_advance;
                $opening_total_due = $opening_pr_due + $opening_sr_due;
            }
            //--

            //Recoverable--
            if ($is_flat && !$is_interest_only/* && !empty($opening_txSchedule)*/) {
                // $pr_recoverable-=$opening_txSchedule->pr_advance;
                // $sr_recoverable-=$opening_txSchedule->sr_advance;
                $pr_recoverable -= $opening_pr_advance;
                $sr_recoverable -= $opening_sr_advance;
            }
            $pr_recoverable = ($pr_recoverable > 0) ? $pr_recoverable : 0;
            $sr_recoverable = ($sr_recoverable > 0) ? $sr_recoverable : 0;

            $pr_recoverable = ($pr_recoverable < 1) ? 0 : $pr_recoverable;
            $sr_recoverable = ($sr_recoverable < 1) ? 0 : $sr_recoverable;

            $loan_details->pr_recoverable = $pr_recoverable;
            $loan_details->sr_recoverable = $sr_recoverable;
            $loan_details->total_recoverable = $pr_recoverable + $sr_recoverable;
            //--

            //Less than 1
            $opening_pr_outstanding = ($opening_pr_outstanding < 1) ? 0 : $opening_pr_outstanding;
            $opening_sr_outstanding = ($opening_sr_outstanding < 1) ? 0 : $opening_sr_outstanding;
            $opening_total_outstanding = $opening_pr_outstanding + $opening_sr_outstanding;

            $opening_pr_advance = ($opening_pr_advance < 1) ? 0 : $opening_pr_advance;
            $opening_sr_advance = ($opening_sr_advance < 1) ? 0 : $opening_sr_advance;
            $opening_total_advance = $opening_pr_advance + $opening_sr_advance;

            $opening_pr_due = ($opening_pr_due < 1) ? 0 : $opening_pr_due;
            $opening_sr_due = ($opening_sr_due < 1) ? 0 : $opening_sr_due;
            $opening_total_due = $opening_pr_due + $opening_sr_due;

            $pr_outstanding = ($pr_outstanding < 1) ? 0 : $pr_outstanding;
            $sr_outstanding = ($sr_outstanding < 1) ? 0 : $sr_outstanding;
            $total_outstanding = $pr_outstanding + $sr_outstanding;
            $total_repay = ($total_repay < 1) ? 0 : $total_repay;

            $pr_advance = ($pr_advance < 1) ? 0 : $pr_advance;
            $sr_advance = ($sr_advance < 1) ? 0 : $sr_advance;
            $total_advance = $pr_advance + $sr_advance;

            $pr_due = ($pr_due < 1) ? 0 : $pr_due;
            $sr_due = ($sr_due < 1) ? 0 : $sr_due;
            $total_due = $pr_due + $sr_due;
            //--

            //Opening Declare
            $loan_details->opening_pr_outstanding = $opening_pr_outstanding;
            $loan_details->opening_sr_outstanding = $opening_sr_outstanding;
            $loan_details->opening_total_outstanding = $opening_total_outstanding;

            $loan_details->opening_pr_advance = $opening_pr_advance;
            $loan_details->opening_sr_advance = $opening_sr_advance;
            $loan_details->opening_total_advance = $opening_total_advance;

            $loan_details->opening_pr_due = $opening_pr_due;
            $loan_details->opening_sr_due = $opening_sr_due;
            $loan_details->opening_total_due = $opening_total_due;
            //--

            //Closing Declare
            $loan_details->pr_outstanding = $pr_outstanding;
            $loan_details->sr_outstanding = $sr_outstanding;
            $loan_details->total_outstanding = $total_outstanding;
            $loan_details->total_repay = $total_repay;

            $loan_details->pr_advance = $pr_advance;
            $loan_details->sr_advance = $sr_advance;
            $loan_details->total_advance = $total_advance;

            $loan_details->pr_due = $pr_due;
            $loan_details->sr_due = $sr_due;
            $loan_details->total_due = $total_due;

            $loan_details->due_payment_qty = $due_payment_qty;
            $loan_details->onTime_payment_qty = $onTime_payment_qty;
            $loan_details->advance_payment_qty = $advance_payment_qty;
            //--

            $is_write_of_eligible = false;
            if ($loan_details->li_eligible_period > 0 && round($total_outstanding) > 0 && !empty($loan_details->last_schedule_date)) {
                $eligibleDate = (new DateTime($loan_details->last_schedule_date))->add(new DateInterval('P' . $loan_details->li_eligible_period . 'Y'))->format('Y-m-d');
                if (strtotime($date) >= strtotime($eligibleDate)) {
                    $is_write_of_eligible = true;
                }
            }
            $loan_details->is_write_of_eligible = $is_write_of_eligible;

            //For Trench
            $loan_details->is_trench_complete = $loan_details->li_amount == $trenchDisburseAmount;
            if (!empty($trenchTxSchedule)) {
                if ($loan_details->li_amount != $trenchDisburseAmount) {
                    $loan_details->txSchedules = [];
                    $loan_details->loan_schedule = [];
                    $loan_details->li_no_of_repayment = 0;
                    $loan_details->li_installment_amount = 0;
                }

                $loan_details->txSchedules = array_merge($trenchTxSchedule, $loan_details->txSchedules);
                $loan_details->loan_schedule = array_merge($trenchLoanSchedule, $loan_details->loan_schedule);
                $loan_details->li_no_of_repayment += count($closing_trench[$loan_details->li_id]);
                //$loan_details->sr_recoverable += $trench_sr_recoverable;
                //$loan_details->total_recoverable += $trench_sr_recoverable;

                $loan_details->li_amount = $trenchDisburseAmount;
            }
            //---

            return $loan_details;
        };

        $txScheduleArr = $this->transactionSchedule($loans, $transactions, $date, false, 0, $loanDataFunc, $loanDataFuncArg, $func, $funcArg);
        return $txScheduleArr;
    }

    private static function curScdlLastdate($scdl_date, $li_payment_frequency, $full_schedule, $s)
    {
        if ($li_payment_frequency == "Weekly") {
            $li_payment_frequency = 2;
        } else if ($li_payment_frequency == "Monthly") {
            $li_payment_frequency = 3;
        }
        switch ($li_payment_frequency) {
            case 4: //Yearly
                $curScdlLastdate = date('Y-12-31', strtotime($scdl_date));
                break;
            case 3: //Monthly
                $curScdlLastdate = date('Y-m-t', strtotime($scdl_date));
                break;
            case 2: //Weekly
                $scdl_d = date('d', strtotime($scdl_date));
                $scdl_m = date('m', strtotime($scdl_date));
                if ($scdl_d > 28 || (isset($full_schedule[$s + 1]) && $scdl_m != date('m', strtotime($full_schedule[$s + 1]->date)))) {
                    $curScdlLastdate = date('Y-m-t', strtotime($scdl_date));
                } else {
                    $mod = $scdl_d % 7;
                    $mdiv = ($scdl_d - $mod) / 7;
                    $curScdlLastdate = ($mod > 0) ? ($mdiv + 1) * 7 : $mdiv * 7;
                    $curScdlLastdate = date("Y-m-$curScdlLastdate", strtotime($scdl_date));
                }
                break;
            default: //Daily
                $curScdlLastdate = $scdl_date;
                break;
        }
        return $curScdlLastdate;
    }

    public function objectFormatter($loanTransactionObject, $date, $objectName = 'txSchedules', $schedule = false)
    {
        $all_loan = [];
        foreach ($loanTransactionObject as $key => $loan) {
            $all_loan[$key] = $loan;
            $all_loan[$key][$objectName] = collect($loan['txSchedules'])->pluck('date')->toArray();
            $all_loan[$key]['installment_date_passed'] = collect($loan['txSchedules'])->where('date', '=<', $date)->count();
            $all_loan[$key]['thisMonthschedule'] = collect($loan['txSchedules'])->where('date', '=<', $date)->count();
            $all_loan[$key]['installment_payable_amount'] = $loan['li_total_repay_amount'];
        }
        return $all_loan;
    }
    //---

    public function staticLoanDuration()
    {
        return [
            [
                "loan_duration_period" => "days",
                "min_loan_duration" => 'any',
                "default_loan_duration" => '',
                "maximum_loan_duration" => 'any',
            ],
            [
                "loan_duration_period" => "weeks",
                "min_loan_duration" => 'any',
                "default_loan_duration" => '',
                "maximum_loan_duration" => 'any',
            ],
            [
                "loan_duration_period" => "months",
                "min_loan_duration" => 'any',
                "default_loan_duration" => '',
                "maximum_loan_duration" => 'any',
            ],
            [
                "loan_duration_period" => "years",
                "min_loan_duration" => 'any',
                "default_loan_duration" => '',
                "maximum_loan_duration" => 'any',
            ],
        ];
    }

}
