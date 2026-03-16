<?php

namespace App\Services;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Enums\ContributionStatus;

class StatusTransitionService
{
    // Transitions autorisées pour les tontines
    private static array $tontineTransitions = [
        'DRAFT' => ['PENDING', 'CANCELLED'],
        'PENDING' => ['ACTIVE', 'CANCELLED'],
        'ACTIVE' => ['PAUSED', 'COMPLETED', 'CANCELLED'],
        'PAUSED' => ['ACTIVE', 'CANCELLED'],
        'COMPLETED' => [],
        'CANCELLED' => [],
    ];

    // Transitions autorisées pour les tours
    private static array $tourTransitions = [
        'UPCOMING' => ['ONGOING', 'CANCELLED'],
        'ONGOING' => ['COMPLETED', 'FAILED'],
        'COMPLETED' => [],
        'FAILED' => ['ONGOING'],  // Permet relance d'un tour FAILED
        'CANCELLED' => [],
    ];

    // Transitions autorisées pour les contributions
    private static array $contributionTransitions = [
        'PENDING' => ['DECLARED', 'LATE'],
        'DECLARED' => ['CONFIRMED', 'REJECTED', 'LATE'],
        'CONFIRMED' => [],
        'REJECTED' => ['DECLARED'],  // Permet re-déclaration après rejet
        'LATE' => ['DECLARED', 'CONFIRMED'],  // Permet déclaration tardive ou confirmation directe
    ];

    /**
     * Vérifie si une transition de statut est autorisée.
     *
     * @param string $type  Type d'entité : 'tontine', 'tour', 'contribution'
     * @param string $from  Statut actuel (nom du case enum en UPPERCASE, ex: 'ACTIVE')
     * @param string $to    Statut cible (nom du case enum en UPPERCASE, ex: 'COMPLETED')
     */
    public static function canTransition(string $type, string $from, string $to): bool
    {
        $transitions = match ($type) {
            'tontine' => self::$tontineTransitions,
            'tour' => self::$tourTransitions,
            'contribution' => self::$contributionTransitions,
            default => [],
        };

        return in_array($to, $transitions[$from] ?? []);
    }

    /**
     * Valide une transition de statut. Abort 422 si la transition est invalide.
     *
     * @param string $type  Type d'entité : 'tontine', 'tour', 'contribution'
     * @param string $from  Statut actuel (nom du case enum en UPPERCASE, ex: 'ACTIVE')
     * @param string $to    Statut cible (nom du case enum en UPPERCASE, ex: 'COMPLETED')
     */
    public static function validateTransition(string $type, string $from, string $to): void
    {
        if (!self::canTransition($type, $from, $to)) {
            $typeLabel = match ($type) {
                'tontine' => 'la tontine',
                'tour' => 'le tour',
                'contribution' => 'la contribution',
                default => $type,
            };
            abort(422, "Transition de statut invalide pour {$typeLabel} : {$from} → {$to}");
        }
    }

    /**
     * Retourne la liste des transitions autorisées depuis un statut donné.
     *
     * @param string $type  Type d'entité : 'tontine', 'tour', 'contribution'
     * @param string $from  Statut actuel (nom du case enum en UPPERCASE, ex: 'ACTIVE')
     * @return array Liste des statuts cibles autorisés (UPPERCASE)
     */
    public static function getAllowedTransitions(string $type, string $from): array
    {
        $transitions = match ($type) {
            'tontine' => self::$tontineTransitions,
            'tour' => self::$tourTransitions,
            'contribution' => self::$contributionTransitions,
            default => [],
        };

        return $transitions[$from] ?? [];
    }

    /**
     * Helper pour résoudre le nom UPPERCASE d'un statut enum ou string.
     * Ex: TontineStatus::ACTIVE -> 'ACTIVE', 'active' -> 'ACTIVE'
     */
    public static function resolveEnumName(mixed $status): string
    {
        if ($status instanceof \BackedEnum) {
            return $status->name;
        }

        return strtoupper((string) $status);
    }
}
