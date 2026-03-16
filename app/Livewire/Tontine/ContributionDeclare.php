<?php

namespace App\Livewire\Tontine;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Models\Contribution;
use App\Models\PaymentProof;
use App\Models\TontineMessage;
use App\Models\ActivityLog;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ContributionDeclare extends Component
{
    use WithFileUploads;

    public Contribution $contribution;

    public string $payment_method = 'orange_money';
    public string $transaction_reference = '';
    public string $sender_phone = '';
    public ?string $transaction_date = null;
    public string $notes = '';
    public $screenshot = null;

    public bool $showForm = false;
    public int $expectedAmount = 0;
    public int $memberParts = 1;

    public function mount(Contribution $contribution)
    {
        $this->contribution = $contribution;
        $this->sender_phone = auth()->user()->phone;
        $this->transaction_date = now()->format('Y-m-d\TH:i');

        // Calcul du montant attendu
        $tontine = $contribution->tontine;
        $member = $tontine->members()->where('user_id', $contribution->user_id)->first();
        $this->memberParts = $member->parts ?? 1;
        $this->expectedAmount = $this->memberParts * $tontine->contribution_amount;
    }

    protected function rules(): array
    {
        return [
            'payment_method' => 'required|in:orange_money,mtn_momo,wave,cash,bank_transfer,other',
            'transaction_reference' => 'nullable|string|max:100',
            'sender_phone' => 'nullable|string|max:20',
            'transaction_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'screenshot' => 'nullable|image|max:5120',
        ];
    }

    public function declare()
    {
        $tontine = $this->contribution->tontine;
        $tour = $this->contribution->tour;

        if ($tontine->status !== TontineStatus::ACTIVE) {
            $message = $tontine->status === TontineStatus::PAUSED
                ? 'La tontine est en pause. Les déclarations de paiement sont suspendues.'
                : 'La tontine doit être active pour déclarer des paiements.';
            session()->flash('error', $message);
            return;
        }

        if ($tour->status !== TourStatus::ONGOING) {
            session()->flash('error', 'Le tour doit être en cours pour déclarer des paiements.');
            return;
        }

        $this->validate();

        // Cleanup old proof if re-declaring after rejection
        if ($this->contribution->paymentProof) {
            if ($this->contribution->paymentProof->screenshot_path) {
                Storage::disk('public')->delete($this->contribution->paymentProof->screenshot_path);
            }
            $this->contribution->paymentProof->delete();
        }

        // Validation du montant et flag requires_review
        $requiresReview = false;
        $notes = $this->notes ?: null;

        if ($this->contribution->amount != $this->expectedAmount) {
            $deviation = abs($this->contribution->amount - $this->expectedAmount) / $this->expectedAmount * 100;
            $amountNote = '[MONTANT INHABITUEL] Montant déclaré : ' . format_amount($this->contribution->amount)
                . ' — Montant attendu : ' . format_amount($this->expectedAmount)
                . ' (écart de ' . round($deviation, 1) . '%)';
            $notes = ($notes ? $notes . "\n" : '') . $amountNote;

            if ($deviation > 10) {
                $requiresReview = true;
            }
        }

        $this->contribution->update([
            'status' => 'declared',
            'declared_at' => now(),
            'notes' => $notes,
            'requires_review' => $requiresReview,
        ]);

        $screenshotPath = null;
        if ($this->screenshot) {
            $screenshotPath = $this->screenshot->store('payment-proofs', 'public');
        }

        PaymentProof::create([
            'contribution_id' => $this->contribution->id,
            'transaction_reference' => $this->transaction_reference ?: null,
            'payment_method' => $this->payment_method,
            'sender_phone' => $this->sender_phone ?: null,
            'screenshot_path' => $screenshotPath,
            'transaction_date' => $this->transaction_date,
        ]);

        ActivityLog::log('contributed', $this->contribution, tontineId: $this->contribution->tontine_id);

        // Duplicate check
        $detector = app(\App\Services\DuplicatePaymentDetector::class);
        $dupWarnings = $detector->check(
            $this->contribution->tontine_id,
            $this->contribution->id,
            $this->transaction_reference ?: null,
            $this->sender_phone ?: null,
            $this->contribution->amount,
            $this->transaction_date
        );

        if (!empty($dupWarnings)) {
            $warningText = collect($dupWarnings)->pluck('message')->implode(' ');
            session()->flash('warning', 'Attention - Doublon possible : ' . $warningText);
        }

        // Create chat message for payment submission
        $tour = $this->contribution->tour;
        TontineMessage::create([
            'tontine_id' => $this->contribution->tontine_id,
            'user_id' => auth()->id(),
            'type' => 'payment_submission',
            'content' => auth()->user()->name . ' a soumis un paiement de '
                . format_amount($this->contribution->amount)
                . ' pour le tour #' . $tour->tour_number,
            'metadata' => [
                'contribution_id' => $this->contribution->id,
                'tour_id' => $this->contribution->tour_id,
                'amount' => $this->contribution->amount,
            ],
        ]);

        // Notify managers
        $tontine = $this->contribution->tontine;
        app(NotificationService::class)->notifyTontineManagers(
            $tontine,
            'payment_submitted',
            'Paiement soumis',
            auth()->user()->name . ' a soumis un paiement pour le tour #' . $tour->tour_number . ' de ' . $tontine->name,
            [
                'tontine_id' => $tontine->id,
                'tour_id' => $tour->id,
                'contribution_id' => $this->contribution->id,
            ]
        );

        $this->dispatch('contribution-declared');
        $this->dispatch('message-sent');

        session()->flash('success', 'Contribution déclarée avec succès. En attente de validation.');
        return redirect()->route('tontines.tours.show', [$this->contribution->tontine_id, $this->contribution->tour_id]);
    }

    public function render()
    {
        return view('livewire.tontine.contribution-declare');
    }
}
