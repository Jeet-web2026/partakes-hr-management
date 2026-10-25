<?php

namespace App\Enums;

enum HrFlagStage: string
{
    case ScheduleInterview = 'Schedule Interview';
    case CandidateOnboard = 'Candidate Onboard';
    case ExitCandidate = 'Exit Candidate';

    public function label(): string
    {
        return $this->value;
    }

    // Display order of the stages
    public function order(): int
    {
        return match ($this) {
            self::ScheduleInterview => 1,
            self::CandidateOnboard => 2,
            self::ExitCandidate => 3,
        };
    }

    /** @return list<string> Flags created for every organisation */
    public function defaultFlags(): array
    {
        return match ($this) {
            self::ScheduleInterview => ['Phone Not Received', 'Not Interested', 'Study going on'],
            self::CandidateOnboard => ['Join after 30 days', 'Join after 15 days', 'Join after 7 days'],
            self::ExitCandidate => ['PF Process Pending', 'ESI Process Pending', 'Gratuity Pending'],
        };
    }
}
