<?php
/**
 * InterviewController — Schedule interviews and submit feedback
 */
class InterviewController {
    private InterviewRepository $interviews;
    private ApplicationRepository $apps;

    public function __construct(PDO $pdo) {
        $this->interviews = new InterviewRepository($pdo);
        $this->apps = new ApplicationRepository($pdo);
    }

    public function create(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $applicationId = intval(getPost('application_id'));
        $date = getPost('scheduled_date');
        $time = getPost('start_time');

        if (empty($date) || empty($time)) {
            setFlash('error', 'Date and time are required.');
            header('Location: ' . EMPLOYER_URL . 'interviews.php');
            exit;
        }

        $appInfo = $this->interviews->getEmployeeFromApplication($applicationId);
        if (!$appInfo) {
            setFlash('error', 'Application not found.');
            header('Location: ' . EMPLOYER_URL . 'interviews.php');
            exit;
        }

        $this->interviews->create([
            'application_id'      => $applicationId,
            'employer_id'         => getCurrentUserId(),
            'employee_id'         => $appInfo['employee_id'],
            'scheduled_date'      => $date,
            'start_time'          => $time,
            'duration_minutes'    => intval(getPost('duration_minutes')) ?: 60,
            'interview_type'      => sanitize(getPost('interview_type')),
            'meeting_link'        => sanitize(getPost('meeting_link')),
            'location'            => sanitize(getPost('location')),
            'panel_members'       => sanitize(getPost('panel_members')),
            'notes_for_candidate' => sanitize(getPost('notes_for_candidate')),
        ]);

        $this->apps->updateStatus($applicationId, 'interview');

        $name = $appInfo['first_name'] . ' ' . $appInfo['last_name'];
        setFlash('success', "Interview with $name scheduled for $date.");
        header('Location: ' . EMPLOYER_URL . 'interviews.php');
        exit;
    }

    public function feedback(): void {
        if (!isLoggedIn() || getCurrentUserRole() !== 'employer') {
            header('Location: ' . AUTH_URL . 'login.php');
            exit;
        }

        $interviewId = intval(getPost('interview_id'));
        $ratings = ['overall_rating', 'technical_rating', 'communication_rating',
                     'problem_solving_rating', 'culture_fit_rating'];

        foreach ($ratings as $r) {
            $val = intval(getPost($r));
            if ($val < 1 || $val > 5) {
                setFlash('error', 'All ratings must be between 1 and 5.');
                header('Location: ' . EMPLOYER_URL . "interview-feedback.php?id=$interviewId");
                exit;
            }
        }

        $this->interviews->addFeedback($interviewId, getCurrentUserId(), [
            'overall_rating'          => intval(getPost('overall_rating')),
            'technical_rating'        => intval(getPost('technical_rating')),
            'communication_rating'    => intval(getPost('communication_rating')),
            'problem_solving_rating'  => intval(getPost('problem_solving_rating')),
            'culture_fit_rating'      => intval(getPost('culture_fit_rating')),
            'strengths'               => sanitize(getPost('strengths')),
            'improvements'            => sanitize(getPost('improvements')),
            'additional_notes'        => sanitize(getPost('additional_notes')),
            'recommendation'          => sanitize(getPost('recommendation')),
        ]);

        $this->interviews->markCompleted($interviewId);

        setFlash('success', 'Interview feedback submitted successfully.');
        header('Location: ' . EMPLOYER_URL . 'interviews.php');
        exit;
    }
}
