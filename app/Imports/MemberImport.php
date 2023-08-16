<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\Member;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MemberImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Member([
            'user_id' => $row['user_id'],
            'member_name' => $row['member_name'],
            'department_id' => $this->findDepartment($row['department_id']),
            'member_phone_number' => $row['member_phone_number'],
            'member_profile_picture' => $row['member_profile_picture'],
        ]);
    }

    public function findDepartment($data){
        $returnData = Department::firstOrCreate(
            [
                'department_name' => $data,
            ]
        );

        return $returnData->id;
    }
}
