<?php
function getStatistics() {
    $data = [];
    $data['users'] = [];
    $reusableArray = [
        'valid' => 0,
        'pending' => 0,
        'invalid' => 0,
        'total' => 0,
        'cash' => 0
    ];
    // 65k rows
    $allTptp = TariffProviderTariffMatch::query()->groupBy('user_id')->chunk(100, function ($allTptps) use ($data, $reusableArray) {
    foreach ($allTptps as $each) {
        $one = filterOne($each, $reusableArray);
        $one['cash'] = number_format($one['cash'],2);
        array_push($data['users'], $one);
    }});
    return $data;
}

function filterOne(array $each, array $reusableArray)
{
    $one = $reusableArray;
    $one['name'] = $each[0]->user->first_name . " " . $each[0]->user->last_name;
    foreach ($each as $single) {
        switch ($single->active_status) {
            case ActiveStatus::ACTIVE: // 1
                $one['valid']++;
                $one['cash'] += floatval(GlobalVariable::getById(GlobalVariable::STANDARDIZATION_UNIT_PRICE)->value);
                break;
            case ActiveStatus::PENDING: // 2
                $one['pending']++;
                break;
            case ActiveStatus::DELETED: // 3
                $one['invalid']++;
                break;
        }
        $one['total']++;
    }
    return $one;
}
