<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Notification;
use App\Models\PaymentProof;
use App\Models\PositionSwapRequest;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\TontineMessage;
use App\Models\Tour;
use App\Models\User;
use App\Models\UserRequest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TontineSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_admin', true)->first();
        $users = User::where('is_admin', false)->orderBy('id')->get();

        // ══════════════════════════════════════════════════════
        // TONTINE 1 : Active, mensuelle, 8 membres, 3 tours
        // Admin est aussi membre (admin rôle)
        // ══════════════════════════════════════════════════════
        $t1 = Tontine::create([
            'creator_id' => $admin->id,
            'name' => 'Tontine des Collègues',
            'description' => 'Tontine mensuelle entre collègues de bureau pour s\'entraider. Chacun cotise 50 000 FCFA par mois.',
            'contribution_amount' => 50000,
            'frequency' => 'monthly',
            'max_members' => 12,
            'start_date' => Carbon::now()->subMonths(3),
            'status' => 'active',
            'rules' => 'Chaque membre doit contribuer avant le 5 du mois. Retard accepté jusqu\'au 10. Au-delà, pénalité de 5000 FCFA.',
            'settings' => json_encode([
                'late_detection_enabled' => true,
                'late_detection_days' => 5,
                'penalty_enabled' => true,
                'penalty_type' => 'fixed',
                'penalty_amount' => 5000,
                'auto_exclusion_enabled' => true,
                'auto_exclusion_threshold' => 3,
                'reminder_enabled' => true,
                'reminder_days_before' => 3,
            ]),
        ]);

        // Membres tontine 1 : admin + 7 users
        $t1Members = collect([$admin])->merge($users->take(7));
        $roles = ['admin', 'treasurer', 'member', 'member', 'member', 'member', 'member', 'member'];
        foreach ($t1Members as $i => $user) {
            TontineMember::create([
                'tontine_id' => $t1->id,
                'user_id' => $user->id,
                'role' => $roles[$i],
                'position' => $i + 1,
                'parts' => $i === 3 ? 2 : 1, // User 4 a 2 parts
                'status' => 'active',
                'joined_at' => now()->subMonths(3)->addDays($i),
            ]);
        }

        // Demande en attente
        TontineMember::create([
            'tontine_id' => $t1->id,
            'user_id' => $users[8]->id,
            'role' => 'member',
            'status' => 'pending',
        ]);

        // Tour 1 : COMPLETED (tout confirmé, décaissé)
        $tour1 = Tour::create([
            'tontine_id' => $t1->id,
            'beneficiary_id' => $t1Members[0]->id,
            'tour_number' => 1,
            'due_date' => now()->subMonths(2),
            'expected_amount' => 50000 * 8, // 7 membres * 1 part + 1 membre * 2 parts = 8 parts
            'collected_amount' => 50000 * 8,
            'status' => 'completed',
            'collection_date' => now()->subMonths(2)->addDays(5),
            'disbursed_at' => now()->subMonths(2)->addDays(6),
            'disbursed_by' => $t1Members[1]->id,
            'disbursement_method' => 'orange_money',
            'disbursement_reference' => 'DISB-' . fake()->numerify('########'),
            'beneficiary_confirmed_at' => now()->subMonths(2)->addDays(7),
        ]);

        foreach ($t1Members as $i => $user) {
            if ($user->id === $t1Members[0]->id) continue; // Pas le bénéficiaire
            $parts = $i === 3 ? 2 : 1;
            $c = Contribution::create([
                'tour_id' => $tour1->id,
                'user_id' => $user->id,
                'tontine_id' => $t1->id,
                'amount' => 50000 * $parts,
                'status' => 'confirmed',
                'declared_at' => now()->subMonths(2)->addDays(rand(1, 3)),
                'confirmed_at' => now()->subMonths(2)->addDays(rand(3, 5)),
                'confirmed_by' => $t1Members[1]->id,
            ]);
            PaymentProof::create([
                'contribution_id' => $c->id,
                'transaction_reference' => 'CI' . fake()->numerify('##########'),
                'payment_method' => fake()->randomElement(['orange_money', 'mtn_momo', 'wave']),
                'sender_phone' => $user->phone,
                'transaction_date' => $c->declared_at,
                'verification_status' => 'verified',
                'verified_by' => $t1Members[1]->id,
                'verified_at' => $c->confirmed_at,
            ]);
        }

        // Tour 2 : COMPLETED (avec 1 retard et pénalité)
        $tour2 = Tour::create([
            'tontine_id' => $t1->id,
            'beneficiary_id' => $t1Members[1]->id,
            'tour_number' => 2,
            'due_date' => now()->subMonth(),
            'expected_amount' => 50000 * 8,
            'collected_amount' => 50000 * 8,
            'status' => 'completed',
            'collection_date' => now()->subMonth()->addDays(8),
            'disbursed_at' => now()->subMonth()->addDays(9),
            'disbursed_by' => $admin->id,
            'disbursement_method' => 'wave',
            'disbursement_reference' => 'DISB-' . fake()->numerify('########'),
            'beneficiary_confirmed_at' => now()->subMonth()->addDays(10),
        ]);

        foreach ($t1Members as $i => $user) {
            if ($user->id === $t1Members[1]->id) continue;
            $parts = $i === 3 ? 2 : 1;
            $isLate = $i === 5; // Un membre en retard
            $c = Contribution::create([
                'tour_id' => $tour2->id,
                'user_id' => $user->id,
                'tontine_id' => $t1->id,
                'amount' => 50000 * $parts,
                'penalty_amount' => $isLate ? 5000 : 0,
                'status' => 'confirmed',
                'declared_at' => now()->subMonth()->addDays($isLate ? 8 : rand(1, 4)),
                'confirmed_at' => now()->subMonth()->addDays($isLate ? 8 : rand(4, 6)),
                'confirmed_by' => $admin->id,
                'notes' => $isLate ? 'Paiement en retard — pénalité appliquée' : null,
            ]);
            PaymentProof::create([
                'contribution_id' => $c->id,
                'transaction_reference' => 'CI' . fake()->numerify('##########'),
                'payment_method' => fake()->randomElement(['orange_money', 'mtn_momo', 'wave', 'cash']),
                'sender_phone' => $user->phone,
                'transaction_date' => $c->declared_at,
                'verification_status' => 'verified',
                'verified_by' => $admin->id,
                'verified_at' => $c->confirmed_at,
            ]);
        }

        // Tour 3 : ONGOING (mix de statuts : confirmé, déclaré, en retard, pending)
        $tour3 = Tour::create([
            'tontine_id' => $t1->id,
            'beneficiary_id' => $t1Members[2]->id,
            'tour_number' => 3,
            'due_date' => now()->subDays(2),
            'expected_amount' => 50000 * 8,
            'collected_amount' => 50000 * 3,
            'status' => 'ongoing',
        ]);

        $contribStatuses = ['confirmed', 'confirmed', 'confirmed', 'declared', 'declared', 'late', 'pending'];
        $idx = 0;
        foreach ($t1Members as $i => $user) {
            if ($user->id === $t1Members[2]->id) continue;
            $parts = $i === 3 ? 2 : 1;
            $st = $contribStatuses[$idx];
            $c = Contribution::create([
                'tour_id' => $tour3->id,
                'user_id' => $user->id,
                'tontine_id' => $t1->id,
                'amount' => 50000 * $parts,
                'penalty_amount' => $st === 'late' ? 5000 : 0,
                'status' => $st,
                'declared_at' => in_array($st, ['declared', 'confirmed']) ? now()->subDays(rand(1, 4)) : null,
                'confirmed_at' => $st === 'confirmed' ? now()->subDays(rand(0, 2)) : null,
                'confirmed_by' => $st === 'confirmed' ? $admin->id : null,
                'notes' => $st === 'late' ? 'Échéance dépassée' : ($st === 'declared' && $parts === 2 ? '[MONTANT INHABITUEL] Montant déclaré : 100 000 FCFA — Montant attendu : 100 000 FCFA' : null),
                'requires_review' => $st === 'declared' && $idx === 4,
            ]);
            if (in_array($st, ['declared', 'confirmed'])) {
                PaymentProof::create([
                    'contribution_id' => $c->id,
                    'transaction_reference' => 'CI' . fake()->numerify('##########'),
                    'payment_method' => fake()->randomElement(['orange_money', 'mtn_momo', 'wave']),
                    'sender_phone' => $user->phone,
                    'transaction_date' => $c->declared_at,
                    'verification_status' => $st === 'confirmed' ? 'verified' : 'pending',
                    'verified_by' => $st === 'confirmed' ? $admin->id : null,
                    'verified_at' => $st === 'confirmed' ? $c->confirmed_at : null,
                ]);
            }
            $idx++;
        }

        // Tours 4-8 : UPCOMING
        for ($n = 4; $n <= 8; $n++) {
            $tour = Tour::create([
                'tontine_id' => $t1->id,
                'beneficiary_id' => $t1Members[$n - 1]->id,
                'tour_number' => $n,
                'due_date' => now()->addMonths($n - 3),
                'expected_amount' => 50000 * 8,
                'collected_amount' => 0,
                'status' => 'upcoming',
            ]);
            foreach ($t1Members as $i => $user) {
                if ($user->id === $t1Members[$n - 1]->id) continue;
                $parts = $i === 3 ? 2 : 1;
                Contribution::create([
                    'tour_id' => $tour->id,
                    'user_id' => $user->id,
                    'tontine_id' => $t1->id,
                    'amount' => 50000 * $parts,
                    'status' => 'pending',
                ]);
            }
        }

        // ══════════════════════════════════════════════════════
        // TONTINE 2 : Active, hebdomadaire, 5 membres
        // Admin est membre aussi
        // ══════════════════════════════════════════════════════
        $t2 = Tontine::create([
            'creator_id' => $users[2]->id,
            'name' => 'Épargne Familiale',
            'description' => 'Petite tontine hebdomadaire en famille pour constituer une épargne commune.',
            'contribution_amount' => 10000,
            'frequency' => 'weekly',
            'max_members' => 8,
            'start_date' => Carbon::now()->subWeeks(6),
            'status' => 'active',
            'settings' => json_encode([
                'late_detection_enabled' => true,
                'late_detection_days' => 2,
                'penalty_enabled' => false,
                'reminder_enabled' => true,
                'reminder_days_before' => 1,
            ]),
        ]);

        $t2Members = collect([$users[2], $users[3], $users[4], $admin, $users[7]]);
        $t2Roles = ['admin', 'treasurer', 'member', 'member', 'member'];
        foreach ($t2Members as $i => $user) {
            TontineMember::create([
                'tontine_id' => $t2->id,
                'user_id' => $user->id,
                'role' => $t2Roles[$i],
                'position' => $i + 1,
                'parts' => 1,
                'status' => 'active',
                'joined_at' => now()->subWeeks(6)->addDays($i),
            ]);
        }

        // 4 tours completed, 1 ongoing
        for ($n = 1; $n <= 5; $n++) {
            $status = $n <= 4 ? 'completed' : 'ongoing';
            $tour = Tour::create([
                'tontine_id' => $t2->id,
                'beneficiary_id' => $t2Members[$n - 1]->id,
                'tour_number' => $n,
                'due_date' => now()->subWeeks(6)->addWeeks($n - 1),
                'expected_amount' => 10000 * 4,
                'collected_amount' => $status === 'completed' ? 10000 * 4 : 10000 * 2,
                'status' => $status,
                'collection_date' => $status === 'completed' ? now()->subWeeks(6)->addWeeks($n - 1)->addDays(2) : null,
                'disbursed_at' => $status === 'completed' ? now()->subWeeks(6)->addWeeks($n - 1)->addDays(3) : null,
                'disbursed_by' => $status === 'completed' ? $t2Members[1]->id : null,
                'disbursement_method' => $status === 'completed' ? 'cash' : null,
                'beneficiary_confirmed_at' => $status === 'completed' ? now()->subWeeks(6)->addWeeks($n - 1)->addDays(3) : null,
            ]);

            $cIdx = 0;
            foreach ($t2Members as $i => $user) {
                if ($user->id === $t2Members[$n - 1]->id) continue;
                $cStatus = $status === 'completed' ? 'confirmed' : ($cIdx < 2 ? 'confirmed' : ($cIdx === 2 ? 'declared' : 'pending'));
                $c = Contribution::create([
                    'tour_id' => $tour->id,
                    'user_id' => $user->id,
                    'tontine_id' => $t2->id,
                    'amount' => 10000,
                    'status' => $cStatus,
                    'declared_at' => in_array($cStatus, ['declared', 'confirmed']) ? $tour->due_date->copy()->subDays(rand(0, 2)) : null,
                    'confirmed_at' => $cStatus === 'confirmed' ? $tour->due_date->copy()->addDay() : null,
                    'confirmed_by' => $cStatus === 'confirmed' ? $t2Members[1]->id : null,
                ]);
                if (in_array($cStatus, ['declared', 'confirmed'])) {
                    PaymentProof::create([
                        'contribution_id' => $c->id,
                        'transaction_reference' => 'FAM' . fake()->numerify('########'),
                        'payment_method' => fake()->randomElement(['orange_money', 'wave', 'cash']),
                        'sender_phone' => $user->phone,
                        'transaction_date' => $c->declared_at,
                        'verification_status' => $cStatus === 'confirmed' ? 'verified' : 'pending',
                        'verified_by' => $cStatus === 'confirmed' ? $t2Members[1]->id : null,
                        'verified_at' => $cStatus === 'confirmed' ? $c->confirmed_at : null,
                    ]);
                }
                $cIdx++;
            }
        }

        // ══════════════════════════════════════════════════════
        // TONTINE 3 : En pause, mensuelle
        // ══════════════════════════════════════════════════════
        $t3 = Tontine::create([
            'creator_id' => $users[5]->id,
            'name' => 'Projet Immobilier',
            'description' => 'Tontine pour financer un projet immobilier commun. Actuellement en pause suite au décès d\'un membre.',
            'contribution_amount' => 200000,
            'frequency' => 'monthly',
            'max_members' => 15,
            'start_date' => Carbon::now()->subMonths(4),
            'status' => 'paused',
            'rules' => 'Cotisation de 200 000 FCFA/mois. Objectif : achat terrain collectif.',
        ]);

        $t3Members = collect([$users[5], $users[6], $users[0], $users[1], $users[9]]);
        foreach ($t3Members as $i => $user) {
            TontineMember::create([
                'tontine_id' => $t3->id,
                'user_id' => $user->id,
                'role' => $i === 0 ? 'admin' : ($i === 1 ? 'treasurer' : 'member'),
                'position' => $i + 1,
                'status' => $i === 4 ? 'left' : 'active',
                'joined_at' => now()->subMonths(4),
            ]);
        }

        // 2 tours completed avant la pause
        for ($n = 1; $n <= 2; $n++) {
            $tour = Tour::create([
                'tontine_id' => $t3->id,
                'beneficiary_id' => $t3Members[$n - 1]->id,
                'tour_number' => $n,
                'due_date' => now()->subMonths(4)->addMonths($n - 1),
                'expected_amount' => 200000 * 4,
                'collected_amount' => 200000 * 4,
                'status' => 'completed',
                'collection_date' => now()->subMonths(4)->addMonths($n - 1)->addDays(5),
                'disbursed_at' => now()->subMonths(4)->addMonths($n - 1)->addDays(6),
                'disbursed_by' => $t3Members[1]->id,
                'disbursement_method' => 'bank_transfer',
                'disbursement_reference' => 'VIR-' . fake()->numerify('########'),
                'beneficiary_confirmed_at' => now()->subMonths(4)->addMonths($n - 1)->addDays(7),
            ]);
            foreach ($t3Members as $i => $user) {
                if ($user->id === $t3Members[$n - 1]->id) continue;
                if ($i === 4) continue; // Membre parti
                $c = Contribution::create([
                    'tour_id' => $tour->id,
                    'user_id' => $user->id,
                    'tontine_id' => $t3->id,
                    'amount' => 200000,
                    'status' => 'confirmed',
                    'declared_at' => $tour->due_date->copy()->addDays(rand(1, 3)),
                    'confirmed_at' => $tour->due_date->copy()->addDays(rand(3, 5)),
                    'confirmed_by' => $t3Members[1]->id,
                ]);
                PaymentProof::create([
                    'contribution_id' => $c->id,
                    'transaction_reference' => 'IMM' . fake()->numerify('########'),
                    'payment_method' => 'bank_transfer',
                    'sender_phone' => $user->phone,
                    'transaction_date' => $c->declared_at,
                    'verification_status' => 'verified',
                    'verified_by' => $t3Members[1]->id,
                    'verified_at' => $c->confirmed_at,
                ]);
            }
        }

        // ══════════════════════════════════════════════════════
        // TONTINE 4 : Brouillon (pas encore démarrée)
        // ══════════════════════════════════════════════════════
        $t4 = Tontine::create([
            'creator_id' => $admin->id,
            'name' => 'Tontine Tech Abidjan',
            'description' => 'Tontine pour les développeurs et tech d\'Abidjan. Bimensuelle, cotisation modérée.',
            'contribution_amount' => 25000,
            'frequency' => 'biweekly',
            'max_members' => 10,
            'start_date' => Carbon::now()->addWeeks(2),
            'status' => 'draft',
        ]);

        TontineMember::create([
            'tontine_id' => $t4->id,
            'user_id' => $admin->id,
            'role' => 'admin',
            'position' => 1,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        // ══════════════════════════════════════════════════════
        // TONTINE 5 : Terminée (tous les tours complétés)
        // ══════════════════════════════════════════════════════
        $t5 = Tontine::create([
            'creator_id' => $users[1]->id,
            'name' => 'Tontine Mamans du Quartier',
            'description' => 'Tontine terminée avec succès ! 4 mamans, 4 mois.',
            'contribution_amount' => 30000,
            'frequency' => 'monthly',
            'max_members' => 4,
            'start_date' => Carbon::now()->subMonths(6),
            'end_date' => Carbon::now()->subMonths(2),
            'status' => 'completed',
        ]);

        $t5Members = collect([$users[1], $users[3], $users[7], $users[9]]);
        foreach ($t5Members as $i => $user) {
            TontineMember::create([
                'tontine_id' => $t5->id,
                'user_id' => $user->id,
                'role' => $i === 0 ? 'admin' : 'member',
                'position' => $i + 1,
                'status' => 'active',
                'joined_at' => now()->subMonths(6),
            ]);
        }

        for ($n = 1; $n <= 4; $n++) {
            $tour = Tour::create([
                'tontine_id' => $t5->id,
                'beneficiary_id' => $t5Members[$n - 1]->id,
                'tour_number' => $n,
                'due_date' => now()->subMonths(6)->addMonths($n - 1),
                'expected_amount' => 30000 * 3,
                'collected_amount' => 30000 * 3,
                'status' => 'completed',
                'collection_date' => now()->subMonths(6)->addMonths($n - 1)->addDays(4),
                'disbursed_at' => now()->subMonths(6)->addMonths($n - 1)->addDays(5),
                'disbursed_by' => $t5Members[0]->id,
                'disbursement_method' => 'orange_money',
                'beneficiary_confirmed_at' => now()->subMonths(6)->addMonths($n - 1)->addDays(5),
            ]);
            foreach ($t5Members as $i => $user) {
                if ($user->id === $t5Members[$n - 1]->id) continue;
                $c = Contribution::create([
                    'tour_id' => $tour->id,
                    'user_id' => $user->id,
                    'tontine_id' => $t5->id,
                    'amount' => 30000,
                    'status' => 'confirmed',
                    'declared_at' => $tour->due_date->copy()->addDays(rand(0, 2)),
                    'confirmed_at' => $tour->due_date->copy()->addDays(rand(2, 4)),
                    'confirmed_by' => $t5Members[0]->id,
                ]);
                PaymentProof::create([
                    'contribution_id' => $c->id,
                    'transaction_reference' => 'MAM' . fake()->numerify('########'),
                    'payment_method' => fake()->randomElement(['orange_money', 'wave', 'cash']),
                    'sender_phone' => $user->phone,
                    'transaction_date' => $c->declared_at,
                    'verification_status' => 'verified',
                    'verified_by' => $t5Members[0]->id,
                    'verified_at' => $c->confirmed_at,
                ]);
            }
        }

        // ══════════════════════════════════════════════════════
        // TONTINE 6 : Annulée
        // ══════════════════════════════════════════════════════
        Tontine::create([
            'creator_id' => $users[4]->id,
            'name' => 'Tontine Annulée',
            'description' => 'Cette tontine a été annulée faute de participants suffisants.',
            'contribution_amount' => 100000,
            'frequency' => 'monthly',
            'max_members' => 10,
            'start_date' => Carbon::now()->subMonths(2),
            'status' => 'cancelled',
        ]);

        // ══════════════════════════════════════════════════════
        // MESSAGES (Chat tontine 1)
        // ══════════════════════════════════════════════════════
        $messages = [
            [$admin->id, 'text', 'Bonjour à tous ! Bienvenue dans notre tontine. N\'oubliez pas de contribuer avant le 5.', now()->subMonths(2)],
            [$t1Members[1]->id, 'text', 'Merci pour l\'organisation ! J\'ai fait mon virement Orange Money.', now()->subMonths(2)->addHours(2)],
            [$t1Members[2]->id, 'text', 'Bien reçu, je ferai mon paiement demain.', now()->subMonths(2)->addHours(5)],
            [null, 'system', 'Le paiement de Kouassi Yao (50 000 FCFA) pour le tour #1 a été confirmé.', now()->subMonths(2)->addDays(1)],
            [null, 'system', 'Toutes les contributions du tour #1 sont confirmées. Montant collecté : 400 000 FCFA.', now()->subMonths(2)->addDays(5)],
            [$t1Members[3]->id, 'text', 'Super ! Tour #2 c\'est mon tour, j\'ai hâte 😊', now()->subMonth()->subDays(2)],
            [$admin->id, 'text', 'Rappel : le tour #3 est en cours. Merci de faire vos contributions rapidement.', now()->subDays(5)],
            [$t1Members[5]->id, 'text', 'Désolé pour le retard, j\'ai eu un imprévu. Je paie ce soir.', now()->subDays(1)],
            [null, 'system', 'Le paiement de Bamba Moussa est en retard pour le tour #3. Une pénalité de 5 000 FCFA a été appliquée.', now()->subDays(1)],
        ];

        foreach ($messages as $msg) {
            TontineMessage::create([
                'tontine_id' => $t1->id,
                'user_id' => $msg[0],
                'type' => $msg[1],
                'content' => $msg[2],
                'created_at' => $msg[3],
                'updated_at' => $msg[3],
            ]);
        }

        // Messages tontine 2
        TontineMessage::create([
            'tontine_id' => $t2->id,
            'user_id' => $users[2]->id,
            'type' => 'text',
            'content' => 'Rappel : cotisation de cette semaine à faire avant vendredi !',
            'created_at' => now()->subDays(2),
        ]);

        // ══════════════════════════════════════════════════════
        // DEMANDE D'ÉCHANGE DE POSITION (Tontine 1)
        // ══════════════════════════════════════════════════════
        PositionSwapRequest::create([
            'tontine_id' => $t1->id,
            'requester_id' => $t1Members[4]->id,
            'target_id' => $t1Members[6]->id,
            'requester_position' => 5,
            'target_position' => 7,
            'reason' => 'J\'ai besoin de recevoir les fonds plus tôt pour un projet urgent.',
            'status' => 'pending',
        ]);

        // ══════════════════════════════════════════════════════
        // REQUÊTES SUPPORT (UserRequest)
        // ══════════════════════════════════════════════════════
        UserRequest::create([
            'user_id' => $t1Members[5]->id,
            'tontine_id' => $t1->id,
            'type' => 'dispute',
            'subject' => 'Pénalité injuste',
            'description' => 'J\'ai payé le 6 du mois mais la pénalité a été appliquée. Le délai est pourtant jusqu\'au 10. Merci de vérifier.',
            'status' => 'pending',
            'created_at' => now()->subDays(1),
        ]);
        UserRequest::create([
            'user_id' => $users[3]->id,
            'tontine_id' => $t2->id,
            'type' => 'payment',
            'subject' => 'Paiement non reçu',
            'description' => 'J\'étais bénéficiaire du tour #3 mais je n\'ai toujours pas reçu les fonds. Ça fait 5 jours.',
            'status' => 'in_progress',
            'admin_response' => 'En cours de vérification avec le trésorier.',
            'created_at' => now()->subDays(3),
        ]);
        UserRequest::create([
            'user_id' => $users[7]->id,
            'type' => 'info',
            'subject' => 'Comment rejoindre une tontine ?',
            'description' => 'Bonjour, je suis nouveau sur la plateforme. Comment puis-je rejoindre une tontine existante ?',
            'status' => 'resolved',
            'admin_response' => 'Orienté vers la page "Rejoindre une tontine" avec le code d\'invitation.',
            'created_at' => now()->subWeeks(2),
        ]);

        // ══════════════════════════════════════════════════════
        // NOTIFICATIONS
        // ══════════════════════════════════════════════════════
        $notifData = [
            [$admin->id, 'payment_declared', 'Nouveau paiement déclaré', 'Touré Fatou a déclaré un paiement de 50 000 FCFA pour le tour #3 de Tontine des Collègues.', now()->subHours(3), null],
            [$admin->id, 'payment_declared', 'Nouveau paiement déclaré', 'Bamba Moussa a déclaré un paiement de 50 000 FCFA pour le tour #3 de Tontine des Collègues. [VÉRIFICATION REQUISE]', now()->subHours(1), null],
            [$admin->id, 'member_request', 'Demande d\'adhésion', 'Aka Serge souhaite rejoindre la Tontine des Collègues.', now()->subDays(2), null],
            [$t1Members[5]->id, 'payment_late', 'Paiement en retard', 'Votre paiement pour le tour #3 de Tontine des Collègues est en retard. Une pénalité de 5 000 FCFA a été appliquée.', now()->subDay(), now()->subHours(6)],
            [$t1Members[2]->id, 'tour_beneficiary', 'Vous êtes bénéficiaire !', 'Vous êtes le bénéficiaire du tour #3 de Tontine des Collègues. Collecte en cours.', now()->subDays(5), now()->subDays(4)],
            [$t1Members[4]->id, 'swap_request', 'Demande d\'échange de position', 'Un membre souhaite échanger sa position avec vous dans Tontine des Collègues.', now()->subDays(1), null],
            [$admin->id, 'contribution_reminder', 'Rappel de cotisation', 'N\'oubliez pas votre cotisation pour le tour #5 de Épargne Familiale. Échéance dans 2 jours.', now()->subHours(12), now()->subHours(6)],
            [$users[3]->id, 'payment_validated', 'Paiement confirmé', 'Votre paiement de 10 000 FCFA pour le tour #5 de Épargne Familiale a été confirmé.', now()->subHours(6), null],
        ];

        foreach ($notifData as $n) {
            Notification::create([
                'user_id' => $n[0],
                'type' => $n[1],
                'title' => $n[2],
                'content' => $n[3],
                'channel' => 'database',
                'status' => $n[5] ? 'read' : 'sent',
                'sent_at' => $n[4],
                'read_at' => $n[5],
                'data' => json_encode(['tontine_id' => $t1->id]),
                'created_at' => $n[4],
            ]);
        }

        // ══════════════════════════════════════════════════════
        // ACTIVITY LOGS
        // ══════════════════════════════════════════════════════
        $logs = [
            ['created', $t1, $admin->id, $t1->id, now()->subMonths(3)],
            ['member_joined', $t1, $t1Members[1]->id, $t1->id, now()->subMonths(3)->addHour()],
            ['tours_generated', $t1, $admin->id, $t1->id, now()->subMonths(3)->addHours(2)],
            ['started_tour', $tour1, $admin->id, $t1->id, now()->subMonths(2)],
            ['contributed_for_member', $tour1->contributions->first(), $t1Members[1]->id, $t1->id, now()->subMonths(2)->addDays(2)],
            ['all_contributions_confirmed', $tour1, $admin->id, $t1->id, now()->subMonths(2)->addDays(5)],
            ['disbursed_tour', $tour1, $t1Members[1]->id, $t1->id, now()->subMonths(2)->addDays(6)],
            ['started_tour', $tour2, $admin->id, $t1->id, now()->subMonth()],
            ['all_contributions_confirmed', $tour2, $admin->id, $t1->id, now()->subMonth()->addDays(8)],
            ['disbursed_tour', $tour2, $admin->id, $t1->id, now()->subMonth()->addDays(9)],
            ['started_tour', $tour3, $admin->id, $t1->id, now()->subDays(5)],
            ['contributed_for_member', $tour3->contributions->first(), $admin->id, $t1->id, now()->subDays(3)],
            ['confirmed', $tour3->contributions->first(), $admin->id, $t1->id, now()->subDays(2)],
            ['penalty_applied', $tour3->contributions->where('status', 'late')->first() ?? $tour3, $admin->id, $t1->id, now()->subDay()],
            ['member_request', $t1, $users[8]->id, $t1->id, now()->subDays(2)],
            ['created', $t2, $users[2]->id, $t2->id, now()->subWeeks(6)],
            ['paused', $t3, $users[5]->id, $t3->id, now()->subMonth()],
            ['member_left', $t3, $users[9]->id, $t3->id, now()->subMonths(2)],
        ];

        foreach ($logs as $log) {
            ActivityLog::create([
                'action' => $log[0],
                'subject_type' => get_class($log[1]),
                'subject_id' => $log[1]->id,
                'user_id' => $log[2],
                'tontine_id' => $log[3],
                'created_at' => $log[4],
                'updated_at' => $log[4],
            ]);
        }
    }
}
