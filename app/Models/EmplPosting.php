<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Session;

class EmplPosting extends Model
{
    protected $table = 'tbl_empl_posting';
    protected $primaryKey = 'Post_UIN';

    public const CREATED_AT = 'CrOn';
    public const UPDATED_AT = 'MoOn';

    /** relation => [column label, name column on the related table] */
    public const LOOKUPS = [
        'employee'      => ['Employee', 'User_Name'],
        'organisation'  => ['Organisation', 'Orga_Name'],
        'branchOffice'  => ['Branch Office', 'Ofic_Name'],
        'designation'   => ['Designation', 'Dsig_name'],
        'department'    => ['Department', 'Dept_Name'],
        'project'       => ['Project', 'Proj_name'],
        'reportingHead' => ['Reporting Head', 'User_Name'],
        'status'        => ['Status', 'Stau_Name'],
        'creator'       => ['Created By', 'User_Name'],
        'modifier'      => ['Modified By', 'User_Name'],
    ];

    protected $fillable = [
        'Orga_UIN',
        'User_UIN',
        'Ofic_UIN',
        'Dsig_UIN',
        'Dept_UIN',
        'Proj_UIN',
        'Rprt_Head_UIN',
        'Strt_Dt',
        'End_Dt',
        'Stau_UIN',
        'CrBy',
        'MoBy',
    ];

    protected function casts(): array
    {
        return [
            'Strt_Dt' => 'date',
            'End_Dt'  => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $posting) {
            $posting->CrBy ??= Session::get('partaker_user_uin');
        });

        static::updating(function (self $posting) {
            $posting->MoBy = Session::get('partaker_user_uin') ?? $posting->MoBy;
        });
    }

    public function displayName(string $relation): string
    {
        return $this->{$relation}?->{self::LOOKUPS[$relation][1]} ?? '—';
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(HrMastJobAply::class, 'User_UIN', 'id');
    }

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(OrganisationDetails::class, 'Orga_UIN', 'Orga_UIN');
    }

    public function branchOffice(): BelongsTo
    {
        return $this->belongsTo(MastOfic::class, 'Ofic_UIN', 'Ofic_UIN');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(MastDsig::class, 'Dsig_UIN', 'Dsig_UIN');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(MastDept::class, 'Dept_UIN', 'Dept_UIN');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(MastProj::class, 'Proj_UIN', 'Proj_UIN');
    }

    public function reportingHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'Rprt_Head_UIN', 'User_UIN');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(MastStau::class, 'Stau_UIN', 'Stau_UIN');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CrBy', 'User_UIN');
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'MoBy', 'User_UIN');
    }
}
