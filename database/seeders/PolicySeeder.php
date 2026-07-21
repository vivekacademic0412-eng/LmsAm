<?php

namespace Database\Seeders;

use App\Models\Policy;
use App\Models\PolicySection;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    public function run(): void
    {
        // Deactivate any previous version so only one policy is ever "active"
        // Policy::where('code', 'enrollment_terms')->update(['is_active' => false]);

        $policy = Policy::where('is_active',1)->first();

        $sections = [
            [
                'section_key' => 'eligibility',
                'title' => 'Eligibility and Enrollment Validity',
                'sort_order' => 1,
                'body' => <<<TXT
Legally binding document.

Enrollment is valid only upon submission of a completed form, all required documents, and confirmation of fee payment.

The institution reserves the right to reject any application that contains false, incomplete, or unverifiable information without refund of the registration fee.

Students found to have misrepresented qualifications or identity will be immediately debarred and their enrollment cancelled.

Enrollment is non-transferable — a seat confirmed for one student cannot be transferred to another individual.

Backend verification: All submitted IDs and documents are cross-verified with source records. Discrepancies trigger a mandatory re-verification process before the student is granted access to the program.
TXT,
            ],
            [
                'section_key' => 'fees',
                'title' => 'Fee Payment and Refund Policy',
                'sort_order' => 2,
                'body' => <<<TXT
Registration fee is non-refundable under all circumstances once the enrollment process is initiated.

Full program fee must be cleared as per the agreed payment schedule (one-time or installment plan). Failure to pay by the due date will result in suspension of access to classes and materials. Examination fees will be additional to course fees.

No refund is applicable once the program has been enrolled.

In case of batch cancellation by the institution, a full refund or free batch transfer will be offered.

Any payment disputes must be raised within 7 days of the transaction date with supporting proof.
TXT,
            ],
            [
                'section_key' => 'attendance',
                'title' => 'Attendance and Program Completion',
                'sort_order' => 3,
                'body' => <<<TXT
Attendance is confirmed via face ID verification.

A minimum task completion of 75% is mandatory to be eligible for the final examination and certification.

Students falling below the threshold will be required to repeat the module or attend make-up sessions at additional cost.

Attendance is recorded digitally. Proxy attendance or misrepresentation of attendance is a serious disciplinary offence and will result in immediate suspension.

Once a batch has commenced live, no extension, session change, repeat session, reassignment, or cancellation of session will be allowed under any circumstances.
TXT,
            ],
            [
                'section_key' => 'conduct',
                'title' => 'Code of Conduct and Disciplinary Policy',
                'sort_order' => 4,
                'body' => <<<TXT
Students must maintain respectful and professional behaviour towards faculty, staff, and fellow students at all times — in class, online, and in all institutional communications.

The following are strictly prohibited and will result in immediate disciplinary action including expulsion:
— Harassment, bullying, or intimidation of any kind
— Cheating, plagiarism, or academic dishonesty in any form
— Sharing, distributing, or leaking course materials, exam papers, or proprietary content
— Impersonation during online or offline examinations
— Use of unfair means during assessments unless explicitly permitted

Students suspended or expelled for misconduct will not be entitled to any refund of fees paid.

Zero-tolerance policy: Any form of misconduct that causes harm to the institution's reputation or another student's wellbeing will be escalated to appropriate authorities if required.
TXT,
            ],
            [
                'section_key' => 'ip',
                'title' => 'Intellectual Property and Content Usage',
                'sort_order' => 5,
                'body' => <<<TXT
All course materials, videos, assignments, presentations, and assessments are the exclusive intellectual property of the institution.

Students are granted a personal, non-transferable licence to access and use content solely for their own learning during the program period.

It is strictly prohibited to:
— Record, screenshot, download, or copy course materials without written permission
— Share, resell, or redistribute any content on any platform, group, or channel
— Use institution materials for commercial purposes or to create derivative courses

Violation of IP terms may result in legal action under applicable copyright law in addition to immediate expulsion.
TXT,
            ],
            [
                'section_key' => 'exam',
                'title' => 'Examination and Certification',
                'sort_order' => 6,
                'body' => <<<TXT
Students must clear all internal assessments and the final examination with a minimum passing skill score as specified per program to be eligible for certification. Charges for certification will be additional.

Certificates will be issued only after full fee clearance, including examination fees, minimum attendance compliance, and successful completion of all required assessments.

The institution reserves the right to withhold or revoke a certificate if post-issuance evidence of fraud, cheating, or document falsification is discovered.

Re-examination (if permitted) will be subject to a re-exam fee and a separate schedule as communicated by the institution.

Certificates are issued with a unique certificate number and QR code. Tampering with or forging certificates is a criminal offence.
TXT,
            ],
            [
                'section_key' => 'privacy',
                'title' => 'Data Privacy and Communication Consent',
                'sort_order' => 7,
                'body' => <<<TXT
By enrolling, the student consents to the collection, storage, and processing of their personal data for academic, administrative, and communication purposes.

The institution may use anonymised data for program improvement, research, and reporting.

Students may receive communications via SMS, email, or WhatsApp regarding schedules, results, and program updates. To opt out of marketing communications, contact the student helpdesk.

Students are responsible for keeping their login credentials confidential. Sharing of LMS access is a violation of these terms.
TXT,
            ],
            [
                'section_key' => 'batch_changes',
                'title' => 'Batch Changes, Deferrals and Program Modifications',
                'sort_order' => 8,
                'body' => <<<TXT
A student may request a one-time batch transfer or deferral (to a future cohort) by submitting a written request at least 5 working days before the batch commencement date. Deferral beyond one cycle will not be permitted.

The institution reserves the right to modify program content, schedules, faculty assignments, or delivery mode to maintain quality standards. Students will be notified in advance.

In the event of a force majeure (natural disaster, pandemic, regulatory directive), the institution may shift to online delivery or temporarily suspend operations without liability.
TXT,
            ],
            [
                'section_key' => 'liability',
                'title' => 'Limitation of Liability and Disputes',
                'sort_order' => 9,
                'body' => <<<TXT
The institution's liability is limited to the fees paid by the student for the enrolled program. No consequential, indirect, or incidental damages are claimable.

The institution does not guarantee employment or specific salary outcomes as a result of program completion.

Any dispute arising from enrollment shall first be addressed through the institution's internal grievance resolution process.

Unresolved disputes shall be subject to the exclusive jurisdiction of courts in the city where the institution is registered, under applicable Indian law.

This agreement shall be governed by the laws of the Republic of India.
TXT,
            ],
            [
                'section_key' => 'deliverables',
                'title' => 'Participant Deliverables and Commitments',
                'sort_order' => 10,
                'body' => <<<TXT
Laptop availability for participants is compulsory.

All payments are to be made 100% in advance, as set out in the Payment Structure for the respective course. Fees are 100% non-refundable and non-transferable.

All details of students being registered for the course must reach the institution on time and must be self-authenticated as per credentials and government ID. No changes will be allowed later.

Training schedule, once finalized, cannot be changed later.

Since training is delivered 100% live, no recorded sessions will be provided for professional courses.

Any enrollment, once completed, cannot be postponed or cancelled — no refund is issued after enrollment.
TXT,
            ],
        ];

        foreach ($sections as $section) {
            PolicySection::create([
                'policy_id' => $policy->id,
                'section_key' => $section['section_key'],
                'title' => $section['title'],
                'sort_order' => $section['sort_order'],
                'body' => trim($section['body']),
            ]);
        }

        $this->command?->info("Policy '{$policy->title}' ({$policy->version}) seeded with " . count($sections) . ' sections.');
    }
}