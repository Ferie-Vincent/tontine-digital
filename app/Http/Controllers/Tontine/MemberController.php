<?php

namespace App\Http\Controllers\Tontine;

use App\Enums\ContributionStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\User;
use App\Services\MemberCleanupService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    public function index(Tontine $tontine)
    {
        $members = $tontine->members()
            ->with('user')
            ->orderBy('position')
            ->get();

        $pendingMembers = $members->where('status', 'pending');
        $activeMembers = $members->where('status', 'active');

        $userMember = $tontine->members()->where('user_id', auth()->id())->first();

        return view('tontines.members', compact('tontine', 'members', 'pendingMembers', 'activeMembers', 'userMember'));
    }

    public function accept(Tontine $tontine, TontineMember $member)
    {

        if ($tontine->isFull()) {
            return back()->with('error', 'La tontine est complète.');
        }

        $nextPosition = $tontine->activeMembers()->max('position') + 1;

        $member->update([
            'status' => 'active',
            'joined_at' => now(),
            'position' => $nextPosition,
            'parts' => 1,
        ]);

        ActivityLog::log('accepted_member', $member, tontineId: $tontine->id, properties: [
            'member_name' => $member->user->name,
        ]);

        app(NotificationService::class)->send(
            $member->user_id,
            'member_added',
            'Demande acceptée',
            'Votre demande d\'adhésion à la tontine ' . $tontine->name . ' a été acceptée.',
            ['tontine_id' => $tontine->id],
            sendEmail: true
        );

        return back()->with('success', $member->user->name . ' a été accepté.');
    }

    public function reject(Tontine $tontine, TontineMember $member)
    {

        $name = $member->user->name;
        $member->delete();

        return back()->with('success', 'Demande de ' . $name . ' refusée.');
    }

    public function exclude(Tontine $tontine, TontineMember $member)
    {

        if ($member->role->value === 'admin') {
            return back()->with('error', 'Impossible d\'exclure l\'administrateur.');
        }

        $member->update(['status' => 'excluded']);

        // Nettoyer les donnees orphelines du membre exclu
        $cleanupResults = app(MemberCleanupService::class)->cleanup($tontine, $member->user);

        ActivityLog::log('excluded_member', $member, tontineId: $tontine->id, properties: [
            'member_name' => $member->user->name,
            'cleanup' => $cleanupResults,
        ]);

        app(NotificationService::class)->send(
            $member->user_id,
            'member_excluded',
            'Exclusion de la tontine',
            'Vous avez été exclu de la tontine ' . $tontine->name . '.',
            ['tontine_id' => $tontine->id],
            sendEmail: true
        );

        return back()->with('success', $member->user->name . ' a été exclu.');
    }

    public function updateRole(Request $request, Tontine $tontine, TontineMember $member)
    {

        $request->validate(['role' => 'required|in:admin,treasurer,member']);

        $member->update(['role' => $request->role]);

        ActivityLog::log('updated_role', $member, tontineId: $tontine->id, properties: [
            'member_name' => $member->user->name,
            'new_role' => $request->role,
        ]);

        return back()->with('success', 'Rôle mis à jour.');
    }

    public function updatePositions(Request $request, Tontine $tontine)
    {

        $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|exists:tontine_members,id',
            'positions.*.position' => 'required|integer|min:1',
        ]);

        foreach ($request->positions as $pos) {
            TontineMember::where('id', $pos['id'])
                ->where('tontine_id', $tontine->id)
                ->update(['position' => $pos['position']]);
        }

        return back()->with('success', 'Ordre de passage mis à jour.');
    }

    public function search(Request $request, Tontine $tontine)
    {

        $request->validate(['q' => 'required|string|min:2']);

        $q = $request->input('q');

        $existingUserIds = $tontine->members()
            ->whereIn('status', ['active', 'pending'])
            ->pluck('user_id');

        $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->whereNotIn('id', $existingUserIds)
            ->where('status', 'active')
            ->limit(10)
            ->get(['id', 'name', 'phone']);

        return response()->json($users);
    }

    public function addDirectly(Request $request, Tontine $tontine)
    {

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'parts' => 'nullable|integer|min:1',
        ]);

        $userId = $request->input('user_id');

        // Vérifier que l'utilisateur n'est pas déjà membre
        $alreadyMember = $tontine->members()
            ->where('user_id', $userId)
            ->whereIn('status', ['active', 'pending'])
            ->exists();

        if ($alreadyMember) {
            return back()->with('error', 'Cet utilisateur est déjà membre de la tontine.');
        }

        // Vérifier que la tontine n'est pas pleine
        if ($tontine->isFull()) {
            return back()->with('error', 'La tontine est complète.');
        }

        $nextPosition = ($tontine->activeMembers()->max('position') ?? 0) + 1;

        $member = TontineMember::create([
            'tontine_id' => $tontine->id,
            'user_id' => $userId,
            'role' => 'member',
            'status' => 'active',
            'position' => $nextPosition,
            'parts' => $request->input('parts', 1),
            'joined_at' => now(),
        ]);

        $user = User::find($userId);

        ActivityLog::log('added_member', $member, tontineId: $tontine->id, properties: [
            'member_name' => $user->name,
        ]);

        app(NotificationService::class)->send(
            $userId,
            'member_added',
            'Ajout à une tontine',
            'Vous avez été ajouté à la tontine ' . $tontine->name . '.',
            ['tontine_id' => $tontine->id],
            sendEmail: true
        );

        return back()->with('success', $user->name . ' a été ajouté à la tontine.');
    }

    public function createAndAdd(Request $request, Tontine $tontine)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'parts' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé. Utilisez la recherche pour trouver cet utilisateur.',
        ]);

        if ($tontine->isFull()) {
            return back()->with('error', 'La tontine est complète.');
        }

        // Générer un mot de passe aléatoire de 8 caractères
        $phone = $request->input('phone');
        $defaultPassword = Str::random(8);

        $user = User::create([
            'name' => $request->input('name'),
            'phone' => $phone,
            'password' => Hash::make($defaultPassword),
            'status' => 'active',
            'must_change_password' => true,
        ]);

        // Envoyer le mot de passe par SMS
        $smsFailed = false;
        try {
            app(\App\Services\SmsService::class)->send(
                $user->phone,
                "Bienvenue sur Tontine ! Votre mot de passe temporaire est : {$defaultPassword}. Vous devrez le changer à votre première connexion."
            );
        } catch (\Exception $e) {
            // Si l'envoi SMS échoue, on log mais on continue
            \Log::warning("Impossible d'envoyer le mot de passe par SMS à {$user->phone}: " . $e->getMessage());
            $smsFailed = true;
        }

        $nextPosition = ($tontine->activeMembers()->max('position') ?? 0) + 1;

        $member = TontineMember::create([
            'tontine_id' => $tontine->id,
            'user_id' => $user->id,
            'role' => 'member',
            'status' => 'active',
            'position' => $nextPosition,
            'parts' => $request->input('parts', 1),
            'joined_at' => now(),
        ]);

        ActivityLog::log('created_and_added_member', $member, tontineId: $tontine->id, properties: [
            'member_name' => $user->name,
        ]);

        app(NotificationService::class)->send(
            $user->id,
            'member_added',
            'Ajout à une tontine',
            'Vous avez été ajouté à la tontine ' . $tontine->name . '.',
            ['tontine_id' => $tontine->id],
            sendEmail: true
        );

        if ($smsFailed) {
            return back()
                ->with('success', "Membre {$user->name} créé et ajouté.")
                ->with('warning', "L'envoi du SMS a échoué. Mot de passe temporaire à transmettre manuellement : {$defaultPassword}");
        }

        return back()->with('success', "Membre {$user->name} créé et ajouté. Le mot de passe a été envoyé par SMS au {$user->phone}.");
    }

    public function updateParts(Request $request, Tontine $tontine, TontineMember $member)
    {

        $request->validate([
            'parts' => 'required|integer|min:1',
        ]);

        // Vérifier qu'aucun tour n'est en cours
        $hasOngoingTour = $tontine->tours()->where('status', 'ongoing')->exists();
        if ($hasOngoingTour) {
            return back()->with('error', 'Impossible de modifier les parts pendant un tour en cours.');
        }

        $oldParts = $member->parts;
        $member->update(['parts' => $request->parts]);

        // Recalculate expected_amount for all UPCOMING tours
        $expectedAmount = $tontine->activeMembers()->sum('parts') * $tontine->contribution_amount;
        $updatedTours = $tontine->tours()->where('status', 'upcoming')->update(['expected_amount' => $expectedAmount]);

        ActivityLog::log('updated_parts', $member, tontineId: $tontine->id, properties: [
            'member_name' => $member->user->name,
            'old_parts' => $oldParts,
            'new_parts' => $request->parts,
            'new_expected_amount' => $expectedAmount,
            'updated_tours_count' => $updatedTours,
        ]);

        app(NotificationService::class)->send(
            $member->user_id,
            'parts_changed',
            'Parts modifiées',
            'Vos parts dans la tontine ' . $tontine->name . ' ont été modifiées de ' . $oldParts . ' à ' . $request->parts . '.',
            ['tontine_id' => $tontine->id]
        );

        // Notify managers of the expected_amount recalculation
        if ($updatedTours > 0) {
            app(NotificationService::class)->notifyTontineManagers(
                $tontine,
                'expected_amount_updated',
                'Montant attendu recalculé',
                'Suite au changement de parts de ' . $member->user->name . ' (' . $oldParts . ' → ' . $request->parts . '), '
                    . 'le montant attendu de ' . $updatedTours . ' tour(s) à venir a été recalculé à ' . format_amount($expectedAmount) . '.',
                ['tontine_id' => $tontine->id]
            );
        }

        return back()->with('success', 'Parts de ' . $member->user->name . ' mises à jour (' . $oldParts . ' → ' . $request->parts . ').');
    }

    public function invite(Request $request, Tontine $tontine)
    {

        $request->validate([
            'phones' => 'required|string',
        ], [
            'phones.required' => 'Veuillez entrer au moins un numéro de téléphone.',
        ]);

        $smsService = app(\App\Services\SmsService::class);

        if (!$smsService->isEnabled()) {
            return back()->with('error', 'Le service SMS n\'est pas activé. Contactez l\'administrateur.');
        }

        // Parse phone numbers (comma, newline, or space separated)
        $rawPhones = preg_split('/[\s,;\n]+/', $request->phones);
        $phones = array_filter(array_map('trim', $rawPhones));

        if (empty($phones)) {
            return back()->with('error', 'Aucun numéro valide trouvé.');
        }

        $appName = \App\Models\SiteSettings::get('platform_name', 'DIGI-TONTINE CI');
        $frequency = $tontine->frequency->label();
        $amount = format_amount($tontine->contribution_amount);

        $message = "{$appName} - Vous etes invite(e) a rejoindre la tontine \"{$tontine->name}\". "
            . "Cotisation : {$amount} / {$frequency}. "
            . "Code d'invitation : {$tontine->code}. "
            . "Rendez-vous sur la plateforme pour rejoindre !";

        $results = $smsService->sendToMany($phones, $message);

        \App\Models\ActivityLog::log('sent_invitations', $tontine, tontineId: $tontine->id, properties: [
            'phones_count' => count($phones),
            'sent' => $results['sent'],
            'failed' => $results['failed'],
        ]);

        $successMsg = "{$results['sent']} invitation(s) envoyée(s) par SMS.";
        if ($results['failed'] > 0) {
            $successMsg .= " {$results['failed']} échec(s).";
        }

        return back()->with('success', $successMsg);
    }

    public function leave(Tontine $tontine)
    {
        $user = auth()->user();
        $member = $tontine->members()->where('user_id', $user->id)->first();

        if (!$member) {
            abort(404);
        }

        // Les administrateurs ne peuvent pas quitter (doivent transférer leur rôle d'abord)
        if ($member->role->value === 'admin') {
            return back()->with('error', 'En tant qu\'administrateur, vous ne pouvez pas quitter la tontine. Transférez d\'abord votre rôle à un autre membre.');
        }

        // Ne peut pas quitter si bénéficiaire d'un tour en cours
        $ongoingBeneficiary = $tontine->tours()
            ->where('status', 'ongoing')
            ->where('beneficiary_id', $user->id)
            ->exists();

        if ($ongoingBeneficiary) {
            return back()->with('error', 'Vous ne pouvez pas quitter la tontine car vous êtes bénéficiaire d\'un tour en cours.');
        }

        // Ne peut pas quitter si a des contributions déclarées en attente
        $pendingContributions = \App\Models\Contribution::where('tontine_id', $tontine->id)
            ->where('user_id', $user->id)
            ->where('status', 'declared')
            ->exists();

        if ($pendingContributions) {
            return back()->with('error', 'Vous avez des paiements déclarés en attente de validation. Veuillez attendre leur traitement.');
        }

        $member->update([
            'status' => 'left',
        ]);

        // Nettoyer les donnees orphelines du membre qui part
        app(MemberCleanupService::class)->cleanup($tontine, $user);

        ActivityLog::log('member_left', $member, userId: $user->id, tontineId: $tontine->id);

        // Notifier les managers
        app(NotificationService::class)->notifyTontineManagers(
            $tontine,
            'member_left',
            'Membre a quitté la tontine',
            $user->name . ' a volontairement quitté la tontine ' . $tontine->name . '.',
            ['tontine_id' => $tontine->id, 'user_id' => $user->id]
        );

        return redirect()->route('tontines.index')
            ->with('success', 'Vous avez quitté la tontine ' . $tontine->name . '.');
    }

    public function import(Request $request, Tontine $tontine)
    {

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ], [
            'csv_file.required' => 'Veuillez sélectionner un fichier CSV.',
            'csv_file.mimes' => 'Le fichier doit être au format CSV ou TXT.',
            'csv_file.max' => 'Le fichier ne doit pas dépasser 2 Mo.',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->with('error', 'Impossible de lire le fichier.');
        }

        // Read and skip header row
        $header = fgetcsv($handle, 0, ',');
        if (!$header) {
            fclose($handle);
            return back()->with('error', 'Le fichier CSV est vide.');
        }

        // Normalize header names (trim, lowercase)
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        // Find column indices
        $nameIdx = array_search('nom', $header);
        $phoneIdx = array_search('telephone', $header);
        $emailIdx = array_search('email', $header);

        if ($nameIdx === false || $phoneIdx === false) {
            fclose($handle);
            return back()->with('error', 'Le fichier CSV doit contenir les colonnes "nom" et "telephone".');
        }

        $added = 0;
        $skipped = 0;
        $errors = [];
        $smsFailures = [];
        $row = 1;
        $maxRows = 500;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            $row++;

            if ($row > $maxRows + 1) {
                $errors[] = "Limite de {$maxRows} lignes atteinte. Les lignes suivantes n'ont pas été traitées.";
                break;
            }

            $name = trim($data[$nameIdx] ?? '');
            $phone = trim($data[$phoneIdx] ?? '');
            $email = trim($data[$emailIdx] ?? '') ?: null;

            // Skip empty rows
            if (empty($name) && empty($phone)) {
                continue;
            }

            // Validate required fields
            if (empty($name)) {
                $errors[] = "Ligne {$row} : nom manquant.";
                continue;
            }

            if (empty($phone)) {
                $errors[] = "Ligne {$row} ({$name}) : téléphone manquant.";
                continue;
            }

            // Normalize phone number
            $phone = $this->normalizePhone($phone);

            if (!$phone) {
                $errors[] = "Ligne {$row} ({$name}) : numéro de téléphone invalide.";
                continue;
            }

            // Check if tontine is full
            if ($tontine->isFull()) {
                $errors[] = "Ligne {$row} ({$name}) : la tontine est complète.";
                break;
            }

            // Find existing user by phone or create new one
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                $defaultPassword = Str::random(8);
                $userData = [
                    'name' => $name,
                    'phone' => $phone,
                    'password' => Hash::make($defaultPassword),
                    'status' => 'active',
                    'must_change_password' => true,
                ];
                if ($email) {
                    $userData['email'] = $email;
                }
                try {
                    $user = User::create($userData);
                } catch (\Exception $e) {
                    $errors[] = "Ligne {$row} ({$name}) : erreur lors de la création du compte.";
                    continue;
                }

                // Try sending password via SMS
                try {
                    app(\App\Services\SmsService::class)->send(
                        $user->phone,
                        "Bienvenue sur Tontine ! Votre mot de passe temporaire est : {$defaultPassword}. Vous devrez le changer à votre première connexion."
                    );
                } catch (\Exception $e) {
                    \Log::warning("Import CSV - Impossible d'envoyer le mot de passe par SMS à {$user->phone}: " . $e->getMessage());
                    $smsFailures[] = "{$name} ({$phone}) : {$defaultPassword}";
                }
            }

            // Check if already a member
            $alreadyMember = $tontine->members()
                ->where('user_id', $user->id)
                ->whereIn('status', ['active', 'pending'])
                ->exists();

            if ($alreadyMember) {
                $skipped++;
                continue;
            }

            $nextPosition = ($tontine->activeMembers()->max('position') ?? 0) + 1;

            TontineMember::create([
                'tontine_id' => $tontine->id,
                'user_id' => $user->id,
                'role' => 'member',
                'status' => 'active',
                'position' => $nextPosition,
                'parts' => 1,
                'joined_at' => now(),
            ]);

            $added++;
        }

        fclose($handle);

        ActivityLog::log('imported_members', $tontine, tontineId: $tontine->id, properties: [
            'added' => $added,
            'skipped' => $skipped,
            'errors' => count($errors),
        ]);

        $message = "{$added} membre(s) ajouté(s).";
        if ($skipped > 0) {
            $message .= " {$skipped} doublon(s) ignoré(s).";
        }
        if (!empty($errors)) {
            $message .= " " . count($errors) . " erreur(s) : " . implode(' ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " ...";
            }
        }

        $redirect = back()->with($added > 0 ? 'success' : 'error', $message);

        if (!empty($smsFailures)) {
            $redirect = $redirect->with('warning', 'SMS non envoyé pour ' . count($smsFailures) . ' membre(s). Mots de passe à transmettre manuellement : ' . implode(' | ', $smsFailures));
        }

        return $redirect;
    }

    public function importTemplate(Tontine $tontine)
    {

        $content = "nom,telephone,email\nKouassi Yao,0701020304,kouassi@example.com\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modele-import-membres.csv"',
        ]);
    }

    public function performance(Tontine $tontine, TontineMember $member)
    {
        $member->load('user');

        // All contributions for this member in this tontine, with their tour
        $contributions = Contribution::where('tontine_id', $tontine->id)
            ->where('user_id', $member->user_id)
            ->with('tour')
            ->orderByDesc('created_at')
            ->get();

        $totalContributions = $contributions->count();

        // On-time: CONFIRMED and confirmed_at <= tour due_date
        $onTimeCount = $contributions->filter(function ($c) {
            return $c->status === ContributionStatus::CONFIRMED
                && $c->tour
                && $c->confirmed_at
                && $c->confirmed_at->lte($c->tour->due_date->endOfDay());
        })->count();

        // Late contributions
        $lateCount = $contributions->where('status', ContributionStatus::LATE)->count();

        // Also count confirmed contributions that were confirmed after due_date as late for stats
        $confirmedLateCount = $contributions->filter(function ($c) {
            return $c->status === ContributionStatus::CONFIRMED
                && $c->tour
                && $c->confirmed_at
                && $c->confirmed_at->gt($c->tour->due_date->endOfDay());
        })->count();

        $totalLate = $lateCount + $confirmedLateCount;

        // Punctuality rate based on resolved contributions (confirmed + late)
        $resolvedCount = $onTimeCount + $totalLate;
        $punctualityRate = $resolvedCount > 0 ? round(($onTimeCount / $resolvedCount) * 100, 1) : 0;

        // Total amount contributed (confirmed only)
        $totalAmount = $contributions->where('status', ContributionStatus::CONFIRMED)->sum('amount');

        // Average delay in days (confirmed_at - due_date, for confirmed contributions with a tour)
        $delays = $contributions->filter(function ($c) {
            return $c->status === ContributionStatus::CONFIRMED
                && $c->tour
                && $c->confirmed_at;
        })->map(function ($c) {
            return $c->confirmed_at->diffInDays($c->tour->due_date, false);
        });
        $averageDelay = $delays->count() > 0 ? round($delays->avg(), 1) : 0;

        // Consecutive on-time streak (most recent first)
        $streak = 0;
        $sortedContributions = $contributions->filter(function ($c) {
            return in_array($c->status, [ContributionStatus::CONFIRMED, ContributionStatus::LATE])
                && $c->tour;
        })->sortByDesc(function ($c) {
            return $c->tour->due_date;
        });

        foreach ($sortedContributions as $c) {
            if ($c->status === ContributionStatus::CONFIRMED
                && $c->confirmed_at
                && $c->confirmed_at->lte($c->tour->due_date->endOfDay())) {
                $streak++;
            } else {
                break;
            }
        }

        // Build history rows for the table
        $history = $contributions->map(function ($c) {
            $delay = null;
            if ($c->tour && $c->confirmed_at) {
                $delay = (int) $c->confirmed_at->diffInDays($c->tour->due_date, false);
            }

            return (object) [
                'date' => $c->confirmed_at ?? $c->declared_at ?? $c->created_at,
                'tour_number' => $c->tour?->tour_number,
                'tour_due_date' => $c->tour?->due_date,
                'amount' => $c->amount,
                'status' => $c->status,
                'delay' => $delay,
            ];
        });

        return view('tontines.members.performance', compact(
            'tontine',
            'member',
            'totalContributions',
            'onTimeCount',
            'totalLate',
            'punctualityRate',
            'totalAmount',
            'averageDelay',
            'streak',
            'history'
        ));
    }

    /**
     * Normalize a phone number to +225XXXXXXXXXX format.
     */
    private function normalizePhone(string $phone): ?string
    {
        // Remove spaces, dashes, dots, parentheses
        $phone = preg_replace('/[\s\-\.\(\)]+/', '', $phone);

        // If starts with +225, keep as-is
        if (str_starts_with($phone, '+225')) {
            $digits = substr($phone, 4);
            if (strlen($digits) === 10) {
                return '+225' . $digits;
            }
            return null;
        }

        // If starts with 225 (without +)
        if (str_starts_with($phone, '225') && strlen($phone) === 13) {
            return '+' . $phone;
        }

        // If starts with 00225
        if (str_starts_with($phone, '00225')) {
            $digits = substr($phone, 5);
            if (strlen($digits) === 10) {
                return '+225' . $digits;
            }
            return null;
        }

        // If 10-digit local number (07XXXXXXXX, 05XXXXXXXX, 01XXXXXXXX)
        if (strlen($phone) === 10 && preg_match('/^0[0-9]{9}$/', $phone)) {
            return '+225' . $phone;
        }

        return null;
    }

}