<?php

namespace App\Enums;

class UserActionEnum
{
    const StandCreate = 'stand-create';
    const StandEdit = 'stand-edit';
    const StandDestroy = 'destroy-stands';
    const StandSee = 'see-stands';

    const UserCreate = 'create-users';
    const UserEdit = 'edit-users';
    const UserDestroy = 'destroy-users';
    const UserSee = 'see-users';

    const StandTemplateCreate = 'create-stand_templates';
    const StandTemplateEdit = 'edit-stand_templates';
    const StandTemplateDestroy = 'destroy-stand_templates';
    const StandTemplateSee = 'see-stand_templates';

    const CongregationCreate = 'create-congregations';
    const CongregationEdit = 'edit-congregations';
    const CongregationDestroy = 'destroy-congregations';
    const CongregationSee = 'see-congregations';

    const StandsRecordCreate = 'create-stands_records';
    const StandsRecordEdit = 'edit-stands_records';
    const StandsRecordDestroy = 'destroy-stands_records';
    const StandsRecordSee = 'see-stands_records';
}
